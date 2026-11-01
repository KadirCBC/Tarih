<?php

namespace App\Repositories;

use App\Models\Coin;
use App\Models\Period;
use App\Models\Ruler;
use App\Repositories\Contracts\CoinRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentCoinRepository implements CoinRepositoryInterface
{
    /**
     * Slug değerine göre yayındaki sikkeyi getirir (İlişkileriyle birlikte).
     */
    public function findPublishedBySlug(string $slug): ?Coin
    {
        return Coin::where('slug', $slug)
            ->where('is_published', true)
            ->with([
                'period:id,name,slug,start_year,end_year',
                'ruler:id,name,slug,title,reign_start_year,reign_end_year',
                'mint:id,name,slug,location,latitude,longitude',
                'primaryImage',
                'images',
                'references',
                'valuations',
                'stories',
            ])
            ->first();
    }

    /**
     * En çok incelenen / popüler sikkeleri getirir.
     */
    public function getPopularCoins(int $limit = 10): Collection
    {
        return Coin::where('is_published', true)
            ->with([
                'period:id,name,slug',
                'ruler:id,name,slug',
                'primaryImage',
            ])
            ->orderByDesc('id') // veya view_count / popülerlik metriği
            ->limit($limit)
            ->get();
    }

    /**
     * Belirli bir döneme ait sikkeleri sayfalı listeler.
     */
    public function getCoinsByPeriod(string $periodSlug, int $perPage = 20): LengthAwarePaginator
    {
        return Coin::where('is_published', true)
            ->whereHas('period', fn ($q) => $q->where('slug', $periodSlug))
            ->with(['period:id,name,slug', 'ruler:id,name,slug', 'primaryImage'])
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    /**
     * Belirli bir hükümdara ait sikkeleri sayfalı listeler.
     */
    public function getCoinsByRuler(string $rulerSlug, int $perPage = 20): LengthAwarePaginator
    {
        return Coin::where('is_published', true)
            ->whereHas('ruler', fn ($q) => $q->where('slug', $rulerSlug))
            ->with(['period:id,name,slug', 'ruler:id,name,slug', 'primaryImage'])
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    /**
     * Tüm aktif dönemleri (menü ve filtreler için) getirir.
     */
    public function getAllPeriods(): Collection
    {
        return Period::orderBy('start_year')->get();
    }

    /**
     * Tüm hükümdarları getirir.
     */
    public function getAllRulers(): Collection
    {
        return Ruler::orderBy('reign_start_year')->get();
    }

    /**
     * Doğrudan Eloquent katmanında önbellek temizleme ihtiyacı yoktur.
     */
    public function clearCoinCache(Coin $coin): void
    {
        // Eloquent reposu saf veritabanı işlemlerini yürütür.
    }
}

