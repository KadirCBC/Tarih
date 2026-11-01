<?php

namespace App\Repositories;

use App\Models\Coin;
use App\Repositories\Contracts\CoinRepositoryInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CachedCoinRepository implements CoinRepositoryInterface
{
    /**
     * 24 Saatlik Önbellek Süresi (Saniye Cinsinden: 86400)
     */
    protected const TTL = 86400;

    public function __construct(
        protected EloquentCoinRepository $repository,
        protected CacheRepository $cache
    ) {}

    /**
     * Sikke detayını 24 saat önbellekleyerek sunar.
     */
    public function findPublishedBySlug(string $slug): ?Coin
    {
        $cacheKey = "coins:detail:{$slug}";

        if ($this->supportsTags()) {
            return $this->cache->tags(['coins', "coin:slug:{$slug}"])
                ->remember($cacheKey, self::TTL, fn () => $this->repository->findPublishedBySlug($slug));
        }

        return $this->cache->remember($cacheKey, self::TTL, fn () => $this->repository->findPublishedBySlug($slug));
    }

    /**
     * Popüler sikkeleri 24 saat önbellekli getirir.
     */
    public function getPopularCoins(int $limit = 10): Collection
    {
        $cacheKey = "coins:popular:{$limit}";

        if ($this->supportsTags()) {
            return $this->cache->tags(['coins', 'coins:popular'])
                ->remember($cacheKey, self::TTL, fn () => $this->repository->getPopularCoins($limit));
        }

        return $this->cache->remember($cacheKey, self::TTL, fn () => $this->repository->getPopularCoins($limit));
    }

    /**
     * Belirli bir döneme ait sikkeleri sayfalı olarak 24 saat önbellekler.
     */
    public function getCoinsByPeriod(string $periodSlug, int $perPage = 20): LengthAwarePaginator
    {
        $page = request()->get('page', 1);
        $cacheKey = "coins:period:{$periodSlug}:page:{$page}:limit:{$perPage}";

        if ($this->supportsTags()) {
            return $this->cache->tags(['coins', "period:{$periodSlug}"])
                ->remember($cacheKey, self::TTL, fn () => $this->repository->getCoinsByPeriod($periodSlug, $perPage));
        }

        return $this->cache->remember($cacheKey, self::TTL, fn () => $this->repository->getCoinsByPeriod($periodSlug, $perPage));
    }

    /**
     * Belirli bir hükümdara ait sikkeleri sayfalı olarak 24 saat önbellekler.
     */
    public function getCoinsByRuler(string $rulerSlug, int $perPage = 20): LengthAwarePaginator
    {
        $page = request()->get('page', 1);
        $cacheKey = "coins:ruler:{$rulerSlug}:page:{$page}:limit:{$perPage}";

        if ($this->supportsTags()) {
            return $this->cache->tags(['coins', "ruler:{$rulerSlug}"])
                ->remember($cacheKey, self::TTL, fn () => $this->repository->getCoinsByRuler($rulerSlug, $perPage));
        }

        return $this->cache->remember($cacheKey, self::TTL, fn () => $this->repository->getCoinsByRuler($rulerSlug, $perPage));
    }

    /**
     * Tüm dönemleri (menüler/filtreler için) 24 saat önbellekler.
     */
    public function getAllPeriods(): Collection
    {
        $cacheKey = "catalog:periods:all";

        return $this->cache->remember($cacheKey, self::TTL, fn () => $this->repository->getAllPeriods());
    }

    /**
     * Tüm hükümdarları 24 saat önbellekler.
     */
    public function getAllRulers(): Collection
    {
        $cacheKey = "catalog:rulers:all";

        return $this->cache->remember($cacheKey, self::TTL, fn () => $this->repository->getAllRulers());
    }

    /**
     * Model Observer: Bir sikke güncellendiğinde veya silindiğinde
     * sadece o sikkenin ve bağlı olduğu dönemin önbelleğini temizler.
     */
    public function clearCoinCache(Coin $coin): void
    {
        $slug = $coin->slug ?? $coin->getOriginal('slug');

        // 1. Eğer Redis / Memcached gibi etiket (Tag) destekleyen bir store varsa:
        if ($this->supportsTags()) {
            $tagsToFlush = ["coin:slug:{$slug}", 'coins:popular'];
            if ($coin->period?->slug) {
                $tagsToFlush[] = "period:{$coin->period->slug}";
            }
            if ($coin->ruler?->slug) {
                $tagsToFlush[] = "ruler:{$coin->ruler->slug}";
            }

            $this->cache->tags($tagsToFlush)->flush();
        }

        // 2. Dosya / Veritabanı / cPanel Standart Önbellek Mağazası İçin Anahtar Bazlı Temizleme:
        if ($slug) {
            $this->cache->forget("coins:detail:{$slug}");
        }

        $this->cache->forget("coins:popular:10");
        $this->cache->forget("coins:popular:20");

        // Bağlı olduğu dönemin ilk 5 sayfa önbelleğini temizle
        if ($periodSlug = $coin->period?->slug ?? $coin->period()->value('slug')) {
            for ($p = 1; $p <= 5; $p++) {
                $this->cache->forget("coins:period:{$periodSlug}:page:{$p}:limit:20");
            }
        }

        // Bağlı olduğu hükümdarın ilk 5 sayfa önbelleğini temizle
        if ($rulerSlug = $coin->ruler?->slug ?? $coin->ruler()->value('slug')) {
            for ($p = 1; $p <= 5; $p++) {
                $this->cache->forget("coins:ruler:{$rulerSlug}:page:{$p}:limit:20");
            }
        }
    }

    /**
     * Aktif önbellek sürücüsünün etiketleri (Tags) destekleyip desteklemediğini denetler.
     */
    protected function supportsTags(): bool
    {
        return method_exists($this->cache->getStore(), 'tags');
    }
}

