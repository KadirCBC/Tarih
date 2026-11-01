<?php

namespace App\Jobs;

use App\Enums\CoinMetal;
use App\Enums\RarityGrade;
use App\Models\Coin;
use App\Models\Mint;
use App\Models\Period;
use App\Models\Ruler;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportCoinsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600; // 10 dakika maksimum işlem süresi

    protected string $filePath;
    protected ?int $userId;

    public function __construct(string $filePath, ?int $userId = null)
    {
        $this->filePath = $filePath;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        if (!file_exists($this->filePath)) {
            Log::error("İçe aktarma dosyası bulunamadı: {$this->filePath}");
            return;
        }

        $handle = fopen($this->filePath, 'r');
        if (!$handle) return;

        // Başlık satırını oku
        $headers = fgetcsv($handle, 4096, ',');
        if (!$headers) {
            fclose($handle);
            return;
        }

        $normalizedHeaders = array_map(fn ($h) => trim(strtolower(str_replace([' ', '-'], '_', $h))), $headers);

        $successCount = 0;
        $errorLogs = [];
        $lineNumber = 1;

        while (($row = fgetcsv($handle, 4096, ',')) !== false) {
            $lineNumber++;
            if (empty(array_filter($row))) continue; // Boş satırı atla

            if (count($row) !== count($normalizedHeaders)) {
                $errorLogs[] = "Satır {$lineNumber}: Sütun sayısı başlıkla eşleşmiyor.";
                continue;
            }

            $data = array_combine($normalizedHeaders, array_map('trim', $row));

            try {
                DB::transaction(function () use ($data, &$successCount) {
                    // 1. Dönem Çözümleme (Yoksa oluştur)
                    $periodName = $data['period'] ?? 'Bilinmeyen Dönem';
                    $period = Period::firstOrCreate(
                        ['name' => $periodName],
                        ['slug' => Str::slug($periodName, '-', 'tr')]
                    );

                    // 2. Hükümdar Çözümleme (Varsa)
                    $ruler = null;
                    if (!empty($data['ruler'])) {
                        $ruler = Ruler::firstOrCreate(
                            ['period_id' => $period->id, 'name' => $data['ruler']],
                            ['slug' => Str::slug($data['ruler'], '-', 'tr')]
                        );
                    }

                    // 3. Darphane Çözümleme
                    $mint = null;
                    if (!empty($data['mint'])) {
                        $mint = Mint::firstOrCreate(
                            ['name' => $data['mint']],
                            ['slug' => Str::slug($data['mint'], '-', 'tr')]
                        );
                    }

                    // 4. Metal & Nadirlik Çözümleme
                    $metalInput = strtolower($data['metal'] ?? 'bronze');
                    $metal = match ($metalInput) {
                        'gold', 'altın' => CoinMetal::GOLD,
                        'silver', 'gümüş' => CoinMetal::SILVER,
                        'billon' => CoinMetal::BILLON,
                        'electrum', 'elektron' => CoinMetal::ELECTRUM,
                        'copper', 'bakır' => CoinMetal::COPPER,
                        default => CoinMetal::BRONZE,
                    };

                    $rarityInput = strtoupper($data['rarity_grade'] ?? $data['rarity'] ?? 'C');
                    $rarity = match ($rarityInput) {
                        'R1' => RarityGrade::R1,
                        'R2' => RarityGrade::R2,
                        'R3' => RarityGrade::R3,
                        'R4' => RarityGrade::R4,
                        'R5' => RarityGrade::R5,
                        default => RarityGrade::C,
                    };

                    // 5. Sikke Kaydını Oluştur veya Güncelle
                    $catalogRef = $data['catalog_reference'] ?? $data['ric_no'] ?? ('REF-' . Str::upper(Str::random(6)));
                    $title = $data['title'] ?? ($ruler ? "{$ruler->name} {$data['denomination']}" : $catalogRef);

                    Coin::updateOrCreate(
                        ['catalog_reference' => $catalogRef],
                        [
                            'period_id' => $period->id,
                            'ruler_id' => $ruler?->id,
                            'mint_id' => $mint?->id,
                            'title' => $title,
                            'denomination' => $data['denomination'] ?? 'Sikke',
                            'metal' => $metal,
                            'weight_g' => !empty($data['weight_g']) ? (float) $data['weight_g'] : null,
                            'diameter_mm' => !empty($data['diameter_mm']) ? (float) $data['diameter_mm'] : null,
                            'obverse_legend' => $data['obverse_legend'] ?? null,
                            'obverse_legend_trans' => $data['obverse_legend_trans'] ?? null,
                            'reverse_legend' => $data['reverse_legend'] ?? null,
                            'reverse_legend_trans' => $data['reverse_legend_trans'] ?? null,
                            'rarity_grade' => $rarity,
                            'historical_notes' => $data['historical_notes'] ?? null,
                        ]
                    );

                    $successCount++;
                });
            } catch (\Throwable $e) {
                $errorLogs[] = "Satır {$lineNumber} ({$data['catalog_reference'] ?? 'Tanımsız'}): " . $e->getMessage();
            }
        }

        fclose($handle);

        // Hata log dosyasını kaydet
        $logSummaryPath = 'imports/import_report_' . time() . '.json';
        Storage::disk('local')->put($logSummaryPath, json_encode([
            'total_success' => $successCount,
            'total_errors' => count($errorLogs),
            'errors' => $errorLogs,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Kullanıcıya bildirim gönder
        if ($this->userId) {
            $user = User::find($this->userId);
            if ($user) {
                Notification::make()
                    ->title('Sikke İçe Aktarma Tamamlandı')
                    ->body("{$successCount} sikke başarıyla eklendi. " . count($errorLogs) . " satırda hata oluştu.")
                    ->status(count($errorLogs) > 0 ? 'warning' : 'success')
                    ->sendToDatabase($user);
            }
        }
    }
}

