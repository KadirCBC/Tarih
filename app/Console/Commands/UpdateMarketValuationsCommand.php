<?php

namespace App\Console\Commands;

use App\Services\ValuationConsolidationService;
use Illuminate\Console\Command;

class UpdateMarketValuationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coins:update-market-valuations
                            {--months=24 : Taranacak geriye dönük ay sayısı}
                            {--coin= : Yalnızca belirli bir sikke ID için konsolidasyon yapar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Son 24 aydaki açık artırma satış kayıtlarını tarar, uç değerleri temizleyip kondisyon piyasa fiyatlarını günceller.';

    /**
     * Execute the console command.
     */
    public function handle(ValuationConsolidationService $service): int
    {
        $months = (int) $this->option('months');
        $coinId = $this->option('coin') ? (int) $this->option('coin') : null;

        $this->info("=== Uluslararası Açık Artırma Fiyat Konsolidasyonu Başlatıldı ===");
        $this->line("Kapsam: Son {$months} aydaki satış kayıtları");
        if ($coinId) {
            $this->comment("Filtre: Yalnızca Sikke ID #{$coinId}");
        }

        $stats = $service->consolidate($months, $coinId);

        if (empty($stats['details'])) {
            $this->warn("Taranan aralıkta değerlendirilecek yeni açık artırma satışı bulunamadı.");
            return Command::SUCCESS;
        }

        $tableRows = [];
        foreach ($stats['details'] as $item) {
            $tableRows[] = [
                $item['coin_id'],
                \Illuminate\Support\Str::limit($item['coin_title'], 28),
                $item['condition'],
                $item['lots_count'] . ' lot' . ($item['outliers_removed'] > 0 ? " (-{$item['outliers_removed']} uç)" : ''),
                $item['old_range'],
                $item['new_range'],
                '$' . number_format($item['median'], 0),
            ];
        }

        $this->newLine();
        $this->table(
            ['ID', 'Sikke Adı', 'Kondisyon', 'İncelenen Lot', 'Eski Aralık', 'Yeni Değer Aralığı', 'Medyan'],
            $tableRows
        );

        $this->newLine();
        $this->info("✓ Piyasa Değerlemeleri Başarıyla Konsolide Edildi!");
        $this->table(
            ['Metrik', 'Değer'],
            [
                ['İşlenen Sikke Sayısı', (string) $stats['coins_processed']],
                ['Analiz Edilen Satış Lotu', (string) $stats['records_analyzed']],
                ['Temizlenen Aykırı Uç (Outlier)', (string) $stats['outliers_pruned']],
                ['Güncellenen Değerleme Sayısı', (string) $stats['valuations_updated']],
            ]
        );

        return Command::SUCCESS;
    }
}

