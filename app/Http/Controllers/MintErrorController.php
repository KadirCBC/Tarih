<?php

namespace App\Http\Controllers;

use App\Models\MintErrorType;
use Illuminate\Http\Request;

class MintErrorController extends Controller
{
    /**
     * Darp Hataları Nümizmatik Eğitim Rehberi ve İnteraktif Simülatörü.
     * GET /darp-hatalari
     */
    public function index()
    {
        $errorTypes = MintErrorType::all();

        // Eğer veritabanı boşsa varsayılan dinamik listeyi sun
        if ($errorTypes->isEmpty()) {
            $errorTypes = collect([
                (object)[
                    'id' => 1,
                    'name' => 'Off-Center Strike (Merkezleme Kayması)',
                    'slug' => 'off-center-strike',
                    'severity_impact' => '1.5x - 4x',
                    'multiplier_min' => 1.5,
                    'multiplier_max' => 4.0,
                    'visual_guide_image' => '/images/mint-errors/off-center.svg',
                    'explanation_tr' => 'Sikke pulunun (flan) darp esnasında alt ve üst kalıpların arasına tam ortalanmadan konulması sonucu oluşur. Sikke tasarımının bir kısmı dışarı taşar ve hilal şeklinde düz boş pul yüzeyi açıkta kalır. Tasarımın ve portrenin korunduğu %30-%50 arası kaymalar en değerli gruptur.',
                ],
                (object)[
                    'id' => 2,
                    'name' => 'Double Die / Double Strike (Çift Darp)',
                    'slug' => 'double-die',
                    'severity_impact' => '2x - 5x',
                    'multiplier_min' => 2.0,
                    'multiplier_max' => 5.0,
                    'visual_guide_image' => '/images/mint-errors/double-die.svg',
                    'explanation_tr' => 'Sikkenin ilk darbe sonrası kalıptan fırlamaması veya hafif kayarak ikinci kez çekiç darbesi alması sonucu meydana gelir. Harflerde, portre hatlarında veya lejantlarda gölge gibi belirgin çift kontur (ghosting) ve çift kabartma çizgileri görülür.',
                ],
                (object)[
                    'id' => 3,
                    'name' => 'Brockage (Ters Baskı / Ayna Hatası)',
                    'slug' => 'brockage',
                    'severity_impact' => '5x - 10x',
                    'multiplier_min' => 5.0,
                    'multiplier_max' => 10.0,
                    'visual_guide_image' => '/images/mint-errors/brockage.svg',
                    'explanation_tr' => 'Darp edilen önceki sikke üst kalıba (çekiç kalıbı) yapışık kaldığında, yeni konulan boş pula baskı yapar. Yeni sikkenin bir yüzü normal kabartma iken, diğer yüzü yapışan sikkenin çukur/negatif ayna görüntüsü (incuse) şeklinde basılır. Antik dünyada nadir rastlanan en prestijli darp hatalarındandır.',
                ],
                (object)[
                    'id' => 4,
                    'name' => 'Die Crack / Cud (Kalıp Çatlağı ve Kırığı)',
                    'slug' => 'die-crack',
                    'severity_impact' => '1.3x - 2.5x',
                    'multiplier_min' => 1.3,
                    'multiplier_max' => 2.5,
                    'visual_guide_image' => '/images/mint-errors/die-crack.svg',
                    'explanation_tr' => 'Binlerce sikke darp edildikten sonra demir veya bronz kalıbın aşırı yorulması ve çatlaması sonucu oluşur. Isıtılmış metal çatlağın içine dolarak sikke yüzeyinde belirgin kabartı şerit (die crack) veya kenarda dolgu kütlesi (cud) bırakır.',
                ],
                (object)[
                    'id' => 5,
                    'name' => 'Overstrike (Üst Üste Darp / Yeniden Basım)',
                    'slug' => 'overstrike',
                    'severity_impact' => '3x - 8x',
                    'multiplier_min' => 3.0,
                    'multiplier_max' => 8.0,
                    'visual_guide_image' => '/images/mint-errors/overstrike.svg',
                    'explanation_tr' => 'Ekonomik kriz veya savaş zamanlarında pul eritmekle vakit kaybetmemek için düşman devletin veya önceki imparatorun tedavüldeki sikkesinin üzerine doğrudan yeni kalıpla baskı yapılmasıdır. Alttaki sikkenin lejant veya portresinin kısmen okunabilir olması nümizmatik açıdan olağanüstü tarihsel belge değeri katar.',
                ],
                (object)[
                    'id' => 6,
                    'name' => 'Clipped Planchet (Eksik / Kesik Pul)',
                    'slug' => 'clipped-planchet',
                    'severity_impact' => '1.5x - 3x',
                    'multiplier_min' => 1.5,
                    'multiplier_max' => 3.0,
                    'visual_guide_image' => '/images/mint-errors/clipped-planchet.svg',
                    'explanation_tr' => 'Döküm metal şeritten yuvarlak pullar kesilirken delgi bıçağının önceki kesimle üst üste binmesi sonucu pulun hilal şeklinde eksik kalmasıdır. Orijinal darp anında eksik olduğunu gösteren Blakesley etkisi taşır.',
                ],
            ]);
        }

        return view('mint_errors.index', compact('errorTypes'));
    }
}

