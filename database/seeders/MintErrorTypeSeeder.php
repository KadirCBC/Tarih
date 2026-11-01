<?php

namespace Database\Seeders;

use App\Models\MintErrorType;
use Illuminate\Database\Seeder;

class MintErrorTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $errors = [
            [
                'name' => 'Off-Center Strike',
                'slug' => 'off-center-strike',
                'severity_impact' => '1.5x - 4x',
                'multiplier_min' => 1.50,
                'multiplier_max' => 4.00,
                'visual_guide_image' => '/images/mint-errors/off-center.svg',
                'explanation_tr' => 'Sikke pulunun (flan) darp esnasında alt ve üst kalıpların arasına tam ortalanmadan konulması sonucu oluşur. Sikke tasarımının bir kısmı dışarı taşar ve hilal şeklinde düz boş pul yüzeyi açıkta kalır. Tasarımın ve portrenin korunduğu %30-%50 arası kaymalar en değerli gruptur.',
            ],
            [
                'name' => 'Double Die / Double Strike',
                'slug' => 'double-die',
                'severity_impact' => '2x - 5x',
                'multiplier_min' => 2.00,
                'multiplier_max' => 5.00,
                'visual_guide_image' => '/images/mint-errors/double-die.svg',
                'explanation_tr' => 'Sikkenin ilk darbe sonrası kalıptan fırlamaması veya hafif kayarak ikinci kez çekiç darbesi alması sonucu meydana gelir. Harflerde, portre hatlarında veya lejantlarda gölge gibi belirgin çift kontur (ghosting) ve çift kabartma çizgileri görülür.',
            ],
            [
                'name' => 'Brockage (Ters Baskı / Ayna Hatası)',
                'slug' => 'brockage',
                'severity_impact' => '5x - 10x',
                'multiplier_min' => 5.00,
                'multiplier_max' => 10.00,
                'visual_guide_image' => '/images/mint-errors/brockage.svg',
                'explanation_tr' => 'Darp edilen önceki sikke üst kalıba (çekiç kalıbı) yapışık kaldığında, yeni konulan boş pula baskı yapar. Yeni sikkenin bir yüzü normal kabartma iken, diğer yüzü yapışan sikkenin çukur/negatif ayna görüntüsü (incuse) şeklinde basılır. Antik dünyada nadir rastlanan en prestijli darp hatalarındandır.',
            ],
            [
                'name' => 'Die Crack / Cud (Kalıp Çatlağı ve Kırığı)',
                'slug' => 'die-crack',
                'severity_impact' => '1.3x - 2.5x',
                'multiplier_min' => 1.30,
                'multiplier_max' => 2.50,
                'visual_guide_image' => '/images/mint-errors/die-crack.svg',
                'explanation_tr' => 'Binlerce sikke darp edildikten sonra demir veya bronz kalıbın aşırı yorulması ve çatlaması sonucu oluşur. Isıtılmış metal çatlağın içine dolarak sikke yüzeyinde belirgin kabartı şerit (die crack) veya kenarda dolgu kütlesi (cud) bırakır.',
            ],
            [
                'name' => 'Overstrike (Üst Üste Darp / Yeniden Basım)',
                'slug' => 'overstrike',
                'severity_impact' => '3x - 8x',
                'multiplier_min' => 3.00,
                'multiplier_max' => 8.00,
                'visual_guide_image' => '/images/mint-errors/overstrike.svg',
                'explanation_tr' => 'Ekonomik kriz veya savaş zamanlarında pul eritmekle vakit kaybetmemek için düşman devletin veya önceki imparatorun tedavüldeki sikkesinin üzerine doğrudan yeni kalıpla baskı yapılmasıdır. Alttaki sikkenin lejant veya portresinin kısmen okunabilir olması nümizmatik açıdan olağanüstü tarihsel belge değeri katar.',
            ],
            [
                'name' => 'Clipped Planchet (Eksik / Kesik Pul)',
                'slug' => 'clipped-planchet',
                'severity_impact' => '1.5x - 3x',
                'multiplier_min' => 1.50,
                'multiplier_max' => 3.00,
                'visual_guide_image' => '/images/mint-errors/clipped-planchet.svg',
                'explanation_tr' => 'Döküm metal şeritten yuvarlak pullar kesilirken delgi bıçağının önceki kesimle üst üste binmesi sonucu pulun hilal şeklinde eksik kalmasıdır. Orijinal darp anında eksik olduğunu gösteren Blakesley etkisi taşır.',
            ],
        ];

        foreach ($errors as $error) {
            MintErrorType::firstOrCreate(
                ['slug' => $error['slug']],
                $error
            );
        }
    }
}

