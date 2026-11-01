<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PruneTempScansCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coins:prune-temp-scans 
                            {--days=3 : Kaç günden eski geçici tarama ve önbellek dosyalarının silineceği}
                            {--dry-run : Dosyaları silmeden yalnızca temizlenecek boyut ve dosya sayısını listeler}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Eski geçici sikke tarama görsellerini, kuyruk artıklarını ve geçici dosyaları temizler.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $isDryRun = (bool) $this->option('dry-run');
        $cutoff = Carbon::now()->subDays($days);

        $this->info("=== Antik Sikke Geçici Dosya ve Tarama Temizliği Başlatıldı ===");
        $this->line("Eşik Tarihi: {$cutoff->toDateTimeString()} ({$days} günden eski dosyalar)");

        if ($isDryRun) {
            $this->warn("[DRY-RUN MODU AKTİF]: Gerçek silme işlemi yapılmayacaktır.");
        }

        $directoriesToPrune = [
            'AI Kamera Taramaları' => storage_path('app/public/identifications'),
            'Geçici Yüklemeler'   => storage_path('app/temp'),
            'Yedekleme Geçicileri' => storage_path('app/backup-temp'),
            'Geçici İçe Aktarmalar'=> storage_path('app/imports'),
        ];

        $totalFilesDeleted = 0;
        $totalBytesFreed = 0;

        foreach ($directoriesToPrune as $label => $path) {
            if (!File::isDirectory($path)) {
                continue;
            }

            $this->comment("Denetleniyor: {$label} ({$path})");
            $files = File::files($path);
            $dirFilesCount = 0;
            $dirBytes = 0;

            foreach ($files as $file) {
                $lastModified = Carbon::createFromTimestamp($file->getMTime());

                if ($lastModified->lessThan($cutoff)) {
                    $fileSize = $file->getSize();
                    $dirFilesCount++;
                    $dirBytes += $fileSize;

                    if (!$isDryRun) {
                        try {
                            File::delete($file->getRealPath());
                        } catch (\Throwable $e) {
                            $this->error("Dosya silinemedi: " . $file->getFilename());
                        }
                    }
                }
            }

            $this->line("  -> Temizlenen: {$dirFilesCount} dosya (" . $this->formatBytes($dirBytes) . ")");
            $totalFilesDeleted += $dirFilesCount;
            $totalBytesFreed += $dirBytes;
        }

        // Clean Laravel Framework cache & expired database sessions if available
        $this->comment("Veritabanı ve Önbellek Temizliği...");
        try {
            $this->callSilently('auth:clear-resets');
        } catch (\Throwable $e) {
            // Ignored if table doesn't exist
        }

        $formattedFreed = $this->formatBytes($totalBytesFreed);
        $this->newLine();
        $this->info("✓ Temizlik Tamamlandı!");
        $this->table(
            ['Metrik', 'Sonuç'],
            [
                ['Silinen Toplam Dosya', (string) $totalFilesDeleted],
                ['Kazanılan Disk Alanı', $formattedFreed],
                ['Mod', $isDryRun ? 'Dry-Run (Simülasyon)' : 'Canlı (Uygulandı)'],
            ]
        );

        Log::info("coins:prune-temp-scans çalıştırıldı. {$totalFilesDeleted} dosya temizlendi, {$formattedFreed} alan kazanıldı.");

        return Command::SUCCESS;
    }

    /**
     * Format bytes into human readable format.
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

