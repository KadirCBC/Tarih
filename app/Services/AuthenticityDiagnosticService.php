<?php

namespace App\Services;

use App\Models\Coin;

class AuthenticityDiagnosticService
{
    /**
     * Kullanıcının girdiği ağırlık ve çap değerlerini sikkenin orijinal darp standartlarıyla karşılaştırır.
     *
     * @param Coin|object $coin Katalog sikkesi
     * @param float $measuredWeight Ölçülen ağırlık (gram)
     * @param float|null $measuredDiameter Ölçülen çap (milimetre)
     * @return array Teşhis ve sapma analizi
     */
    public function analyzePhysicalDeviations(object $coin, float $measuredWeight, ?float $measuredDiameter = null): array
    {
        $refWeight = (float) $coin->weight_g;
        $refDiameter = (float) $coin->diameter_mm;

        $weightDeviationPercent = 0.0;
        if ($refWeight > 0) {
            $weightDeviationPercent = round((($measuredWeight - $refWeight) / $refWeight) * 100, 1);
        }

        $diameterDeviationPercent = 0.0;
        if ($refDiameter > 0 && $measuredDiameter !== null && $measuredDiameter > 0) {
            $diameterDeviationPercent = round((($measuredDiameter - $refDiameter) / $refDiameter) * 100, 1);
        }

        $absWeightDev = abs($weightDeviationPercent);
        $absDiameterDev = abs($diameterDeviationPercent);

        // Teşhis Seviyesi Belirleme:
        // - Normal (0 - %8 arası)
        // - Şüpheli / Hafif Aşınma (%8 - %15 arası)
        // - Kritik Sapma / Muhtemel Sahte (> %15)
        if ($absWeightDev <= 8.0 && $absDiameterDev <= 6.0) {
            $status = 'normal';
            $level = 'success';
            $title = 'Normal Darp Tolerans Aralığında';
            $message = "Girdiğiniz ağırlık ({$measuredWeight}g) ve çap, orijinal referans standartlarına ({$refWeight}g) oldukça yakındır (%{$weightDeviationPercent} sapma). Antik darp toleransları dahilindedir.";
        } elseif ($absWeightDev <= 15.0 && $absDiameterDev <= 10.0) {
            $status = 'warning';
            $level = 'warning';
            $title = 'Hafif Tolerans / Aşınma Sapması';
            $message = "Girdiğiniz değerlerde %{$weightDeviationPercent} oranında sapma tespit edildi. Sikkenin dolaşımda aşınması veya antik flan kesimindeki doğal toleranslar bu farka yol açmış olabilir.";
        } else {
            $status = 'critical';
            $level = 'danger';
            $title = 'Yüksek Sapma / Sahtelik & Döküm Uyarısı!';

            if ($weightDeviationPercent < -15.0) {
                $message = "Dikkat: Girdiğiniz ağırlık ({$measuredWeight}g), orijinal darp standartlarının ({$refWeight}g) oldukça altındadır (%{$weightDeviationPercent} sapma). Sikke döküm replika, kalay/kurşun dolgulu taklit veya antik dönemde aşırı traşlanmış (clipped) olabilir.";
            } elseif ($weightDeviationPercent > 15.0) {
                $message = "Dikkat: Girdiğiniz ağırlık ({$measuredWeight}g), orijinal darp standartlarının ({$refWeight}g) oldukça üzerindedir (%+{$weightDeviationPercent} sapma). Orijinal sikkelerden kalıp alınarak yapılan modern döküm kopyalar genellikle daha kalın ve ağır çıkmaktadır.";
            } else {
                $message = "Dikkat: Sikkenin çap ve ağırlık oranları antik darp standartlarıyla uyuşmamaktadır (%{$weightDeviationPercent} ağırlık, %{$diameterDeviationPercent} çap sapması).";
            }
        }

        return [
            'status' => $status,
            'level' => $level,
            'title' => $title,
            'message' => $message,
            'reference_weight' => $refWeight,
            'measured_weight' => $measuredWeight,
            'weight_deviation_percent' => $weightDeviationPercent,
            'reference_diameter' => $refDiameter,
            'measured_diameter' => $measuredDiameter,
            'diameter_deviation_percent' => $diameterDeviationPercent,
            'red_flags' => [
                'casting_bubbles' => 'Yüzeyde mikroskobik gözenekler veya çukurlar var mı?',
                'seam_lines' => 'Sikke kenarında iki parçalı kalıp birleşim çizgisi (dikiş izi) görülüyor mu?',
                'soft_details' => 'Portre ve harf hatları aşırı yuvarlak veya silik mi (döküm yumuşaklığı)?',
                'artificial_patina' => 'Patina tırnakla kazınabiliyor veya asetonla çıkıyor mu?',
            ],
        ];
    }
}

