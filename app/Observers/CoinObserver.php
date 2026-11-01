<?php

namespace App\Observers;

use App\Models\Coin;
use App\Repositories\Contracts\CoinRepositoryInterface;
use Illuminate\Support\Facades\Log;

class CoinObserver
{
    public function __construct(
        protected CoinRepositoryInterface $coinRepository
    ) {}

    /**
     * Sikke oluşturulduğunda çalışır.
     */
    public function created(Coin $coin): void
    {
        $this->coinRepository->clearCoinCache($coin);
        Log::info("CoinObserver: Yeni sikke eklendi, önbellek temizlendi. [Coin: {$coin->id}]");
    }

    /**
     * Sikke güncellendiğinde çalışır.
     * Sadece o sikkenin ve bağlı olduğu dönemin önbelleğini temizler.
     */
    public function updated(Coin $coin): void
    {
        $this->coinRepository->clearCoinCache($coin);

        // Eğer dönemi veya hükümdarı değiştiyse, eski dönemin de önbelleğini temizle
        if ($coin->isDirty('period_id')) {
            $oldPeriodId = $coin->getOriginal('period_id');
            // Eski dönem önbelleğini de sıfırla
            $oldPeriod = \App\Models\Period::find($oldPeriodId);
            if ($oldPeriod) {
                for ($p = 1; $p <= 5; $p++) {
                    cache()->forget("coins:period:{$oldPeriod->slug}:page:{$p}:limit:20");
                }
            }
        }

        Log::info("CoinObserver: Sikke güncellendi, hedeflenmiş önbellek temizlendi. [Coin: {$coin->id}, Slug: {$coin->slug}]");
    }

    /**
     * Sikke silindiğinde çalışır.
     */
    public function deleted(Coin $coin): void
    {
        $this->coinRepository->clearCoinCache($coin);
        Log::info("CoinObserver: Sikke silindi, önbellek tahliye edildi. [Coin: {$coin->id}]");
    }

    /**
     * Sikke geri yüklendiğinde (SoftDeletes) çalışır.
     */
    public function restored(Coin $coin): void
    {
        $this->coinRepository->clearCoinCache($coin);
    }
}

