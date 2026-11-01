<?php

namespace App\Http\Controllers;

use App\Enums\CoinMetal;
use App\Models\Coin;
use App\Models\Period;
use App\Models\Ruler;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProgrammaticSeoController extends Controller
{
    /**
     * Programatik SEO Landing Sayfaları Çözümleyicisi
     * Örn: /roma-gumus-sikkeleri, /fatih-sultan-mehmed-altin-paralari, /antik-grek-gumus-sikkeleri
     */
    public function show(string $slug): View
    {
        $metalMap = [
            'altin' => CoinMetal::GOLD,
            'gumus' => CoinMetal::SILVER,
            'bronz' => CoinMetal::BRONZE,
            'bakir' => CoinMetal::COPPER,
            'elektrum' => CoinMetal::ELECTRUM,
            'elektron' => CoinMetal::ELECTRUM,
            'billon' => CoinMetal::BILLON,
        ];

        // 1. URL Kalıbını Çözümle: {slug}-{metal}-(sikkeleri|paralari)
        if (!preg_match('/^(.*?)-(altin|gumus|bronz|bakir|elektrum|elektron|billon)-(sikkeleri|paralari)$/', $slug, $matches)) {
            throw new NotFoundHttpException('Sayfa bulunamadı.');
        }

        $entitySlug = $matches[1];
        $metalKey = $matches[2];
        $metal = $metalMap[$metalKey] ?? CoinMetal::SILVER;

        // 2. Varlığı Tespit Et (Hükümdar mı, Tarihsel Dönem mi?)
        $ruler = Ruler::where('slug', $entitySlug)->first();
        $period = null;

        if (!$ruler) {
            $period = Period::where('slug', $entitySlug)->first();
            if (!$period) {
                // Özel takma adlar (Örn: 'roma' -> 'roma-imparatorlugu')
                $period = Period::where('slug', 'like', "{$entitySlug}%")->first();
            }
        }

        if (!$ruler && !$period) {
            throw new NotFoundHttpException('İlgili nümizmatik kategori bulunamadı.');
        }

        // 3. Filtrelenmiş Sikkeleri Getir
        $query = Coin::query()->where('metal', $metal->value)->with(['period', 'ruler', 'primaryImage', 'valuations']);

        if ($ruler) {
            $query->where('ruler_id', $ruler->id);
            $pageTitle = "{$ruler->name} {$metal->label()} Kataloğu ve Değerleri";
            $h1Title = "{$ruler->name} {$metal->label()}";
            $entityName = $ruler->name;
            $entityContext = "{$ruler->name} ({$ruler->title}) adına darbedilmiş orijinal {$metal->value} sikkeler, lejant çözümlemeleri, nadirlik dereceleri ve güncel müzayede referans değerleri.";
        } else {
            $query->where('period_id', $period->id);
            $pageTitle = "{$period->name} {$metal->label()} Kataloğu ve Değerleri";
            $h1Title = "{$period->name} {$metal->label()}";
            $entityName = $period->name;
            $entityContext = "{$period->name} ({$period->formatted_date_range}) dönemine ait antik {$metal->value} sikkeler, darphaneler, lejant epigrafi metinleri ve uluslararası müzayede taban-tavan fiyat bantları.";
        }

        $coins = $query->paginate(24);

        $metaDescription = "Orijinal {$entityName} {$metal->value} sikkeleri inceleyin. Lejant çözümlemeleri, gramaj, nadirlik ve uluslararası müzayede referans fiyatları.";

        return view('seo.programmatic_coins', compact(
            'coins',
            'ruler',
            'period',
            'metal',
            'pageTitle',
            'h1Title',
            'metaDescription',
            'entityContext',
            'slug'
        ));
    }
}

