<?php

namespace App\Services;

use App\Enums\ConditionGrade;
use App\Models\Coin;
use InvalidArgumentException;

class CoinValuationService
{
    /**
     * Sikke ve kondisyona göre tahmini USD piyasa değer aralığını hesaplar.
     * Geçersiz kondisyon girildiğinde InvalidArgumentException fırlatır.
     *
     * @param Coin $coin
     * @param string|ConditionGrade $condition
     * @return array{condition: string, min_value_usd: float, max_value_usd: float, formatted_range: string}
     * @throws InvalidArgumentException
     */
    public function calculateForCondition(Coin $coin, string|ConditionGrade $condition): array
    {
        $grade = $condition instanceof ConditionGrade ? $condition : ConditionGrade::tryFrom($condition);

        if (!$grade) {
            $validGrades = implode(', ', array_map(fn (ConditionGrade $c) => $c->value, ConditionGrade::cases()));
            throw new InvalidArgumentException("Geçersiz kondisyon derecesi: '{$condition}'. Kabul edilen değerler: {$validGrades}.");
        }

        // Öncelikli olarak coin_valuations tablosundaki konsolide değere bak
        $valuation = null;
        if ($coin->exists) {
            $valuation = $coin->valuations()->where('condition_grade', $grade->value)->first();
        }

        if ($valuation && $valuation->min_value_usd > 0) {
            $min = (float) $valuation->min_value_usd;
            $max = (float) $valuation->max_value_usd;

            return [
                'condition' => $grade->value,
                'min_value_usd' => $min,
                'max_value_usd' => $max,
                'formatted_range' => '$' . number_format($min, 2) . ' - $' . number_format($max, 2),
            ];
        }

        // Referans baz taban fiyat (Örn: Sikke tipine veya varsayılan nominale göre)
        $baseMin = 100.0;
        $baseMax = 200.0;

        $multiplier = match ($grade) {
            ConditionGrade::POOR => 0.35,
            ConditionGrade::FINE => 0.65,
            ConditionGrade::VF => 1.0,
            ConditionGrade::XF => 2.2,
            ConditionGrade::UNC => 4.5,
        };

        $min = round($baseMin * $multiplier, 2);
        $max = round($baseMax * $multiplier, 2);

        return [
            'condition' => $grade->value,
            'min_value_usd' => $min,
            'max_value_usd' => $max,
            'formatted_range' => '$' . number_format($min, 2) . ' - $' . number_format($max, 2),
        ];
    }
}

