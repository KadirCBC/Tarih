<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes & Scheduled Tasks
|--------------------------------------------------------------------------
*/

// Model ve önbellek temizlikleri
Schedule::command('model:prune')->daily();
Schedule::command('cache:prune-stale-tags')->hourly();

// 1. Spatie Backup: Her gece 02:00'de veritabanı ve sikke görsellerini yedekler
Schedule::command('backup:run --only-to-disk=backup_remote')
    ->dailyAt('02:00')
    ->onOneServer()
    ->runInBackground();

// 2. Spatie Backup: Her gece 03:00'te eskiyen yedek arşivlerini temizler
Schedule::command('backup:clean')
    ->dailyAt('03:00')
    ->onOneServer();

// 3. Dinamik Sitemap.xml otomatik yenileme (Her gece 04:00'te)
Schedule::command('sitemap:generate')
    ->dailyAt('04:00')
    ->onOneServer();

// 4. cPanel / Plesk Kaynak Dostu Temizlik: Her Pazar 04:30'da geçici taramaları temizler
Schedule::command('coins:prune-temp-scans --days=3')
    ->weeklyOn(0, '04:30')
    ->onOneServer();

// 5. ECB (Avrupa Merkez Bankası) Günlük Döviz Kurları (Hafta içi 16:30'da)
Schedule::command('coins:fetch-currency-rates')
    ->dailyAt('16:30')
    ->onOneServer();

// 6. Uluslararası Açık Artırma Fiyat Konsolidasyonu (Her Pazartesi 05:00'te)
Schedule::command('coins:update-market-valuations --months=24')
    ->weeklyOn(1, '05:00')
    ->onOneServer();
