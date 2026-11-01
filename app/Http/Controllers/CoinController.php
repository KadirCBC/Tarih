<?php

namespace App\Http\Controllers;

use App\Enums\CoinMetal;
use App\Enums\ConditionGrade;
use App\Enums\RarityGrade;
use App\Models\Coin;
use App\Models\Mint;
use App\Models\Period;
use App\Models\Ruler;
use Illuminate\Http\Request;

class CoinController extends Controller
{
    /**
     * Nümizmatik sikke kataloğu listeleme ve çoklu filtreleme.
     */
    public function index(Request $request)
    {
        $query = Coin::query()->with([
            'period:id,name,slug,start_year,end_year',
            'ruler:id,name,slug,title',
            'mint:id,name,slug,location',
            'primaryImage',
            'valuations',
        ]);

        // Arama sorgusu (q)
        if ($search = $request->input('q')) {
            $query->search($search);
        }

        // Dönem filtresi
        if ($periodSlug = $request->input('period')) {
            $query->whereHas('period', fn ($q) => $q->where('slug', $periodSlug));
        }

        // Hükümdar filtresi
        if ($rulerSlug = $request->input('ruler')) {
            $query->whereHas('ruler', fn ($q) => $q->where('slug', $rulerSlug));
        }

        // Darphane filtresi
        if ($mintSlug = $request->input('mint')) {
            $query->whereHas('mint', fn ($q) => $q->where('slug', $mintSlug));
        }

        // Maden filtresi
        if ($metal = $request->input('metal')) {
            $query->byMetal($metal);
        }

        // Nadirlik derecesi filtresi
        if ($rarity = $request->input('rarity')) {
            $query->byRarity($rarity);
        }

        // Sıralama
        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'views' => $query->orderByDesc('view_count'),
            'weight_desc' => $query->orderByDesc('weight_g'),
            'weight_asc' => $query->orderBy('weight_g'),
            'oldest' => $query->oldest('id'),
            default => $query->latest('id'),
        };

        $coins = $query->paginate(24)->withQueryString();

        $periods = Period::select(['id', 'name', 'slug'])->orderBy('start_year')->get();
        $rulers = Ruler::select(['id', 'name', 'slug', 'period_id'])->orderBy('name')->get();
        $mints = Mint::select(['id', 'name', 'slug', 'location'])->orderBy('name')->get();
        $metals = CoinMetal::cases();
        $rarities = RarityGrade::cases();

        return view('coins.index', compact('coins', 'periods', 'rulers', 'mints', 'metals', 'rarities'));
    }

    /**
     * Hızlı nümizmatik arama motoru (Lejant, İmparator, RIC ve Katalog no).
     */
    public function search(Request $request)
    {
        $term = trim((string) $request->input('q', ''));

        if (empty($term)) {
            return redirect()->route('coins.index');
        }

        $coins = Coin::search($term)
            ->with([
                'period:id,name,slug',
                'ruler:id,name,slug',
                'mint:id,name,slug',
                'primaryImage',
                'valuations',
            ])
            ->paginate(24)
            ->withQueryString();

        $periods = Period::select(['id', 'name', 'slug'])->get();
        $rulers = Ruler::select(['id', 'name', 'slug', 'period_id'])->get();
        $mints = Mint::select(['id', 'name', 'slug'])->get();
        $metals = CoinMetal::cases();
        $rarities = RarityGrade::cases();

        return view('coins.index', [
            'coins' => $coins,
            'periods' => $periods,
            'rulers' => $rulers,
            'mints' => $mints,
            'metals' => $metals,
            'rarities' => $rarities,
            'searchTerm' => $term,
        ]);
    }

    /**
     * Sikke detay sayfası (Akademik bilgiler, lejant çözümlemeleri, piyasa bantları).
     */
    public function show(Coin $coin)
    {
        $coin->incrementViews();

        $coin->load([
            'period',
            'ruler',
            'mint',
            'images',
            'valuations' => fn ($q) => $q->orderBy('min_value_usd', 'asc'),
        ]);

        // Kondisyon derecelerine göre standart referans haritası
        $conditionGrades = ConditionGrade::cases();
        $valuationMap = $coin->valuations->keyBy(fn ($item) => $item->condition_grade->value ?? $item->condition_grade);

        // İlgili / benzer sikkeler (Aynı hükümdar veya aynı dönem)
        $similarCoins = Coin::query()
            ->where('id', '!=', $coin->id)
            ->where(function ($q) use ($coin) {
                if ($coin->ruler_id) {
                    $q->where('ruler_id', $coin->ruler_id);
                } else {
                    $q->where('period_id', $coin->period_id);
                }
            })
            ->with(['primaryImage', 'period', 'ruler'])
            ->limit(4)
            ->get();

        return view('coins.show', compact('coin', 'conditionGrades', 'valuationMap', 'similarCoins'));
    }
}
