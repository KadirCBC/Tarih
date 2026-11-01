<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserCollection;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class CollectionPortfolioService
{
    /**
     * Kullanıcının toplam portföy değerini ve detaylı analizini hesaplar.
     *
     * @param User $user
     * @return array
     */
    public function getPortfolioSummary(User $user): array
    {
        $items = $user->collections()
            ->with([
                'coin.period',
                'coin.ruler',
                'coin.primaryImage',
                'coin.valuations',
            ])
            ->get();

        $totalCoins = $items->count();
        $totalEstimatedMinUsd = 0.0;
        $totalEstimatedMaxUsd = 0.0;
        $totalPurchaseCostUsd = 0.0;

        $metalBreakdown = [];
        $periodBreakdown = [];
        $conditionBreakdown = [];

        foreach ($items as $item) {
            $coin = $item->coin;
            if (!$coin) continue;

            // Kullanıcının belirlediği kondisyona ait değerlemeyi al
            $valuation = $item->estimated_valuation;

            if ($valuation) {
                $minVal = (float) $valuation->min_value_usd;
                $maxVal = (float) $valuation->max_value_usd;
                $totalEstimatedMinUsd += $minVal;
                $totalEstimatedMaxUsd += $maxVal;
            } else {
                $minVal = 0.0;
                $maxVal = 0.0;
            }

            if ($item->purchase_price_usd) {
                $totalPurchaseCostUsd += (float) $item->purchase_price_usd;
            }

            // Maden Dağılımı
            $metalKey = $coin->metal->value;
            if (!isset($metalBreakdown[$metalKey])) {
                $metalBreakdown[$metalKey] = [
                    'label' => $coin->metal->label(),
                    'count' => 0,
                    'min_usd' => 0.0,
                    'max_usd' => 0.0,
                ];
            }
            $metalBreakdown[$metalKey]['count']++;
            $metalBreakdown[$metalKey]['min_usd'] += $minVal;
            $metalBreakdown[$metalKey]['max_usd'] += $maxVal;

            // Dönem Dağılımı
            $periodKey = $coin->period?->name ?? 'Bilinmeyen Dönem';
            if (!isset($periodBreakdown[$periodKey])) {
                $periodBreakdown[$periodKey] = [
                    'count' => 0,
                    'min_usd' => 0.0,
                    'max_usd' => 0.0,
                ];
            }
            $periodBreakdown[$periodKey]['count']++;
            $periodBreakdown[$periodKey]['min_usd'] += $minVal;
            $periodBreakdown[$periodKey]['max_usd'] += $maxVal;

            // Kondisyon Dağılımı
            $condKey = $item->user_condition->value;
            $conditionBreakdown[$condKey] = ($conditionBreakdown[$condKey] ?? 0) + 1;
        }

        $minFormatted = '$' . number_format($totalEstimatedMinUsd, 0);
        $maxFormatted = '$' . number_format($totalEstimatedMaxUsd, 0);

        return [
            'total_coins' => $totalCoins,
            'estimated_total_min_usd' => round($totalEstimatedMinUsd, 2),
            'estimated_total_max_usd' => round($totalEstimatedMaxUsd, 2),
            'total_purchase_cost_usd' => round($totalPurchaseCostUsd, 2),
            'price_range_formatted' => "{$minFormatted} - {$maxFormatted} USD",
            'summary_message' => "Portföyünüzde {$totalCoins} sikke var, tahmini toplam değer: {$minFormatted} - {$maxFormatted} USD",
            'metal_breakdown' => $metalBreakdown,
            'period_breakdown' => $periodBreakdown,
            'condition_breakdown' => $conditionBreakdown,
        ];
    }

    /**
     * Kullanıcının koleksiyonu için A4 formatında Nümizmatik Envanter Sertifikası PDF'i oluşturur.
     */
    public function generateInventoryPdf(User $user)
    {
        $summary = $this->getPortfolioSummary($user);

        $items = $user->collections()
            ->with([
                'coin.period',
                'coin.ruler',
                'coin.mint',
                'coin.primaryImage',
                'coin.valuations',
            ])
            ->get();

        $pdf = Pdf::loadView('pdf.inventory_certificate', [
            'user' => $user,
            'summary' => $summary,
            'items' => $items,
            'documentNumber' => 'NUMIS-CERT-' . strtoupper(substr(md5($user->id . date('Ymd')), 0, 8)),
            'issuedDate' => now()->format('d.m.Y H:i'),
        ])->setPaper('a4', 'portrait')
          ->setOption('isHtml5ParserEnabled', true)
          ->setOption('isRemoteEnabled', true);

        return $pdf;
    }
}

