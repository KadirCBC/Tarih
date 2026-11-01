<?php

namespace App\Services;

use App\Enums\ConditionGrade;
use App\Models\AuctionRecord;
use App\Models\Coin;
use App\Models\CoinValuation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ValuationConsolidationService
{
    /**
     * Son 24 aydaki tüm müzayede satış kayıtlarını tarayıp
     * kondisyon bazında aykırı değerleri (outlier) temizler ve
     * piyasa değerleme matrisini (coin_valuations) günceller.
     *
     * @param int $months Geriye dönük taranacak ay sayısı (varsayılan: 24)
     * @param int|null $coinId Yalnızca belirli bir sikke için çalıştırılmak istenirse
     * @return array{
     *     coins_processed: int,
     *     records_analyzed: int,
     *     valuations_updated: int,
     *     outliers_pruned: int,
     *     details: array<int, array<string, mixed>>
     * }
     */
    public function consolidate(int $months = 24, ?int $coinId = null): array
    {
        $cutoffDate = Carbon::now()->subMonths($months);

        $query = AuctionRecord::where('sale_date', '>=', $cutoffDate);
        if ($coinId) {
            $query->where('coin_id', $coinId);
        }

        // İlgili sikkeleri ve kondisyonları grupla
        $coinIds = $query->distinct()->pluck('coin_id');

        $stats = [
            'coins_processed' => 0,
            'records_analyzed' => 0,
            'valuations_updated' => 0,
            'outliers_pruned' => 0,
            'details' => [],
        ];

        foreach ($coinIds as $id) {
            $coin = Coin::find($id);
            if (!$coin) {
                continue;
            }

            $stats['coins_processed']++;

            foreach (ConditionGrade::cases() as $grade) {
                $records = AuctionRecord::where('coin_id', $coin->id)
                    ->where('condition_grade', $grade->value)
                    ->where('sale_date', '>=', $cutoffDate)
                    ->orderBy('realized_price_usd', 'asc')
                    ->get();

                if ($records->isEmpty()) {
                    continue;
                }

                $rawPrices = $records->pluck('realized_price_usd')->map(fn ($p) => (float) $p)->toArray();
                $stats['records_analyzed'] += count($rawPrices);

                // Aykırı Değer (Outlier) Temizliği ve Bant Hesaplaması
                $filtered = $this->filterOutliersAndCalculateRange($rawPrices);
                $stats['outliers_pruned'] += $filtered['outliers_count'];

                // Mevcut değerlemeyi kontrol et
                $existingValuation = CoinValuation::where('coin_id', $coin->id)
                    ->where('condition_grade', $grade->value)
                    ->first();

                $oldRange = $existingValuation
                    ? "\${$existingValuation->min_value_usd} - \${$existingValuation->max_value_usd}"
                    : 'Kayıt Yok';

                // Veritabanını güncelle veya yeni değerleme ekle
                $valuation = CoinValuation::updateOrCreate(
                    [
                        'coin_id' => $coin->id,
                        'condition_grade' => $grade->value,
                    ],
                    [
                        'min_value_usd' => $filtered['min_usd'],
                        'max_value_usd' => $filtered['max_usd'],
                        'last_updated_at' => now(),
                    ]
                );

                $newRange = "\${$filtered['min_usd']} - \${$filtered['max_usd']}";

                $stats['valuations_updated']++;
                $stats['details'][] = [
                    'coin_id' => $coin->id,
                    'coin_title' => $coin->title,
                    'condition' => $grade->value,
                    'lots_count' => count($rawPrices),
                    'outliers_removed' => $filtered['outliers_count'],
                    'old_range' => $oldRange,
                    'new_range' => $newRange,
                    'median' => $filtered['median'],
                ];
            }
        }

        Log::info("Müzayede konsolidasyonu tamamlandı: {$stats['coins_processed']} sikke, {$stats['valuations_updated']} değerleme güncellendi, {$stats['outliers_pruned']} aykırı uç temizlendi.");

        return $stats;
    }

    /**
     * Çeyrekler Açıklığı (IQR - Interquartile Range) algoritması ile aşırı uçları temizler
     * ve sağlıklı min-max piyasa aralığını hesaplar.
     *
     * @param array<float> $prices Sıralı fiyat listesi
     * @return array{min_usd: float, max_usd: float, median: float, outliers_count: int, clean_count: int}
     */
    protected function filterOutliersAndCalculateRange(array $prices): array
    {
        sort($prices);
        $count = count($prices);

        if ($count === 1) {
            $p = $prices[0];
            return [
                'min_usd' => round($p * 0.90, 2),
                'max_usd' => round($p * 1.10, 2),
                'median' => $p,
                'outliers_count' => 0,
                'clean_count' => 1,
            ];
        }

        if ($count === 2) {
            return [
                'min_usd' => round($prices[0] * 0.95, 2),
                'max_usd' => round($prices[1] * 1.05, 2),
                'median' => round(($prices[0] + $prices[1]) / 2, 2),
                'outliers_count' => 0,
                'clean_count' => 2,
            ];
        }

        // 3 veya daha fazla satış kaydı varsa IQR analizi
        $q1 = $this->calculatePercentile($prices, 0.25);
        $q3 = $this->calculatePercentile($prices, 0.75);
        $iqr = $q3 - $q1;

        $lowerBound = max($q1 - (1.5 * $iqr), 0);
        $upperBound = $q3 + (1.5 * $iqr);

        $cleanPrices = array_values(array_filter($prices, fn ($p) => $p >= $lowerBound && $p <= $upperBound));
        $outliersCount = $count - count($cleanPrices);

        if (empty($cleanPrices)) {
            $cleanPrices = $prices;
            $outliersCount = 0;
        }

        $min = min($cleanPrices);
        $max = max($cleanPrices);
        $median = $this->calculateMedian($cleanPrices);

        // Piyasa derinliği için toleranslı aralık oluştur
        $calculatedMin = min($min, round($median * 0.88, 2));
        $calculatedMax = max($max, round($median * 1.12, 2));

        return [
            'min_usd' => round($calculatedMin, 0),
            'max_usd' => round($calculatedMax, 0),
            'median' => round($median, 2),
            'outliers_count' => $outliersCount,
            'clean_count' => count($cleanPrices),
        ];
    }

    /**
     * Dizi için belirli bir yüzdelik dilimi (percentile) hesaplar.
     */
    protected function calculatePercentile(array $sortedArray, float $percentile): float
    {
        $count = count($sortedArray);
        if ($count === 0) {
            return 0.0;
        }

        $index = ($count - 1) * $percentile;
        $lower = floor($index);
        $fraction = $index - $lower;

        if ($lower + 1 < $count) {
            return $sortedArray[$lower] + ($fraction * ($sortedArray[$lower + 1] - $sortedArray[$lower]));
        }

        return (float) $sortedArray[$lower];
    }

    /**
     * Medyan hesaplar.
     */
    protected function calculateMedian(array $sortedArray): float
    {
        $count = count($sortedArray);
        if ($count === 0) {
            return 0.0;
        }

        $middle = floor($count / 2);
        if ($count % 2 === 0) {
            return ($sortedArray[$middle - 1] + $sortedArray[$middle]) / 2.0;
        }

        return (float) $sortedArray[$middle];
    }
}

