<?php

use App\Http\Controllers\CoinController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\ProgrammaticSeoController;
use App\Http\Controllers\RulerController;
use App\Http\Controllers\StoryController;
use App\Http\Controllers\UserCollectionController;
use App\Services\SitemapGeneratorService;
use Illuminate\Support\Facades\Route;

Route::get('/', [CoinController::class, 'index'])->name('home');
Route::get('/search', [CoinController::class, 'search'])->name('coins.search');
Route::get('/harita', [\App\Http\Controllers\MapController::class, 'index'])->name('map.index');
Route::get('/tarih-hesaplayici', [\App\Http\Controllers\RegnalCalculatorController::class, 'index'])->name('calculator.index');
Route::post('/tarih-hesaplayici', [\App\Http\Controllers\RegnalCalculatorController::class, 'calculate'])->name('calculator.calculate');
Route::get('/darp-hatalari', [\App\Http\Controllers\MintErrorController::class, 'index'])->name('mint-errors.index');
Route::view('/offline', 'offline')->name('offline');

// Güvenlik & Scraper Bot Koruması: Cloudflare Turnstile Doğrulama
Route::post('/security/verify-turnstile', [\App\Http\Controllers\SecurityChallengeController::class, 'verify'])->name('security.verify-turnstile');

// Dinamik Filigranlı Görsel Teslim Ucu (Orijinal Dosya Bozulmadan)
Route::get('/watermark/{path}', [\App\Http\Controllers\WatermarkedImageController::class, 'show'])
    ->where('path', '.*')
    ->name('image.watermark');

// Dinamik Sitemap (spatie/laravel-sitemap)
Route::get('/sitemap.xml', function (SitemapGeneratorService $service) {
    $path = public_path('sitemap.xml');
    if (!file_exists($path) || (time() - filemtime($path) > 86400)) {
        $service->generate();
    }
    return response()->file($path, ['Content-Type' => 'text/xml']);
})->name('sitemap');

// Haftanın Sikkesi & Tarihsel Editoryal Hikayeler (Google Discover)
Route::get('/haftanin-sikkesi', [StoryController::class, 'coinOfTheWeek'])->name('stories.week');
Route::prefix('hikayeler')->name('stories.')->group(function () {
    Route::get('/', [StoryController::class, 'index'])->name('index');
    Route::get('/{story:slug}', [StoryController::class, 'show'])->name('show');
});

// Sikke Kataloğu
Route::prefix('coins')->name('coins.')->group(function () {
    Route::get('/', [CoinController::class, 'index'])->name('index');
    Route::get('/search', [CoinController::class, 'search'])->name('search');
    Route::get('/{coin:slug}', [CoinController::class, 'show'])->name('show');
});

// Dönemler
Route::prefix('periods')->name('periods.')->group(function () {
    Route::get('/', [PeriodController::class, 'index'])->name('index');
    Route::get('/{period:slug}', [PeriodController::class, 'show'])->name('show');
});

// Hükümdarlar
Route::prefix('rulers')->name('rulers.')->group(function () {
    Route::get('/', [RulerController::class, 'index'])->name('index');
    Route::get('/{ruler:slug}', [RulerController::class, 'show'])->name('show');
});

// Kullanıcı Envanteri ve Portföy
Route::middleware('auth')->prefix('collection')->name('collection.')->group(function () {
    Route::get('/', [UserCollectionController::class, 'index'])->name('index');
    Route::get('/export-pdf', [UserCollectionController::class, 'exportPdf'])->name('export-pdf');
    Route::post('/{coin}', [UserCollectionController::class, 'store'])->name('store');
    Route::delete('/{userCollection}', [UserCollectionController::class, 'destroy'])->name('destroy');
});

// Programatik SEO Sayfaları (Örn: /roma-gumus-sikkeleri, /fatih-sultan-mehmed-altin-paralari)
Route::get('/{slug}', [ProgrammaticSeoController::class, 'show'])
    ->where('slug', '^[a-z0-9\-]+-(sikkeleri|paralari)$')
    ->name('seo.programmatic');

// Sikke Kimlikleme Kulübü (Moderasyonlu Topluluk ve Uzman Danışma Platformu)
Route::prefix('kulup')->name('identifications.')->group(function () {
    Route::get('/', [\App\Http\Controllers\IdentificationRequestController::class, 'index'])->name('index');
    Route::get('/yeni-talep', [\App\Http\Controllers\IdentificationRequestController::class, 'create'])->name('create');
    Route::post('/yeni-talep', [\App\Http\Controllers\IdentificationRequestController::class, 'store'])->name('store');
    Route::get('/{identification}', [\App\Http\Controllers\IdentificationRequestController::class, 'show'])->name('show');
    Route::post('/{identification}/yorum', [\App\Http\Controllers\IdentificationRequestController::class, 'addComment'])->name('comment');
    Route::post('/{identification}/onayla', [\App\Http\Controllers\IdentificationRequestController::class, 'verify'])->name('verify');
    Route::post('/{identification}/tartismali', [\App\Http\Controllers\IdentificationRequestController::class, 'dispute'])->name('dispute');
});

// Epigrafik Lejant Tamamlama & Dairesel Yazı Düzleştirici (Polar Transform)
Route::get('/lejant-tamamla', [\App\Http\Controllers\EpigraphyWebController::class, 'index'])->name('epigraphy.index');
Route::post('/lejant-tamamla/gorsel', [\App\Http\Controllers\EpigraphyWebController::class, 'reconstructImage'])->name('epigraphy.reconstruct-image');

// Dil ve Para Birimi Değiştirme
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array(strtolower($locale), ['tr', 'en', 'de'], true)) {
        session(['locale' => strtolower($locale)]);
    }
    return redirect()->back();
})->name('locale.switch');

Route::get('/currency/{currency}', function (string $currency) {
    $supported = array_keys(\App\Services\CurrencyService::SUPPORTED_CURRENCIES);
    if (in_array(strtoupper($currency), $supported, true)) {
        session(['user_currency' => strtoupper($currency)]);
    }
    return redirect()->back();
})->name('currency.switch');
