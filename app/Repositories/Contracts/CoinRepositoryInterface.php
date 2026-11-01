<?php

namespace App\Repositories\Contracts;

use App\Models\Coin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CoinRepositoryInterface
{
    /**
     * Slug değerine göre yayındaki sikkeyi getirir (İlişkileriyle birlikte).
     */
    public function findPublishedBySlug(string $slug): ?Coin;

    /**
     * En çok incelenen / popüler sikkeleri 24 saat önbellekli getirir.
     */
    public function getPopularCoins(int $limit = 10): Collection;

    /**
     * Belirli bir döneme ait sikkeleri sayfalı listeler.
     */
    public function getCoinsByPeriod(string $periodSlug, int $perPage = 20): LengthAwarePaginator;

    /**
     * Belirli bir hükümdara ait sikkeleri sayfalı listeler.
     */
    public function getCoinsByRuler(string $rulerSlug, int $perPage = 20): LengthAwarePaginator;

    /**
     * Tüm aktif dönemleri (menü ve filtreler için) önbellekli getirir.
     */
    public function getAllPeriods(): Collection;

    /**
     * Tüm hükümdarları önbellekli getirir.
     */
    public function getAllRulers(): Collection;

    /**
     * Sikke güncellendiğinde veya silindiğinde sadece ilgili sikkenin ve bağlı olduğu dönemin önbelleğini temizler.
     */
    public function clearCoinCache(Coin $coin): void;
}

