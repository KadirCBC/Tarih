<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CoinApiController;
use App\Http\Controllers\Api\MintApiController;
use App\Http\Controllers\Api\PeriodApiController;
use App\Http\Controllers\Api\RulerApiController;
use App\Http\Controllers\Api\UserCollectionApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| RESTful API Routes (v1)
|--------------------------------------------------------------------------
| Flutter / React Native mobil istemcileri için optimize edilmiş JSON API uç noktaları.
*/

Route::prefix('v1')->group(function () {

    // 1. Kimlik Doğrulama (Sanctum Auth)
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    // 2. Taksonomi ve Referans Listeleri
    Route::get('/periods', [PeriodApiController::class, 'index']);
    Route::get('/periods/{slug}', [PeriodApiController::class, 'show']);

    Route::get('/rulers', [RulerApiController::class, 'index']);
    Route::get('/rulers/{slug}', [RulerApiController::class, 'show']);

    Route::get('/mints', [MintApiController::class, 'index']);
    Route::get('/mints/geojson', [MintApiController::class, 'geoJson']);
    Route::get('/mints/{slug}', [MintApiController::class, 'show']);
    Route::get('/geo/boundaries', [\App\Http\Controllers\Api\GeoApiController::class, 'boundaries']);

    // 3. Sikke Kataloğu ve Görsel Eşleştirme (AI Identify & Recognition - Kota Korumalı)
    Route::get('/coins', [CoinApiController::class, 'index']);
    Route::post('/coins/identify', [\App\Http\Controllers\Api\CoinRecognitionController::class, 'recognize'])
        ->middleware(\App\Http\Middleware\CheckScanQuota::class);
    Route::post('/coins/recognize', [\App\Http\Controllers\Api\CoinRecognitionController::class, 'recognize'])
        ->middleware(\App\Http\Middleware\CheckScanQuota::class);
    Route::get('/coins/{slug}', [CoinApiController::class, 'show']);
    Route::post('/coins/{slug}/verify-physical', [CoinApiController::class, 'verifyPhysical']);

    // 4. Koleksiyon Defteri & Portföy Yönetimi (Sanctum Auth Korumalı)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/my-collection', [UserCollectionApiController::class, 'index']);
        Route::get('/my-collection/portfolio-summary', [UserCollectionApiController::class, 'portfolioSummary']);
        Route::get('/my-collection/export-pdf', [UserCollectionApiController::class, 'exportPdf']);
        Route::post('/my-collection/add', [UserCollectionApiController::class, 'store']);
        Route::post('/my-collection/sync', [UserCollectionApiController::class, 'sync']);
        Route::delete('/my-collection/{id}', [UserCollectionApiController::class, 'destroy']);
    });

    // 5. Nümizmat Pro Abonelik & Mağaza Webhookları (Apple StoreKit 2 & Google Play)
    Route::prefix('subscriptions')->group(function () {
        Route::post('/apple-webhook', [\App\Http\Controllers\Api\SubscriptionWebhookController::class, 'handleAppleWebhook']);
        Route::post('/google-webhook', [\App\Http\Controllers\Api\SubscriptionWebhookController::class, 'handleGoogleWebhook']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/verify-purchase', [\App\Http\Controllers\Api\SubscriptionWebhookController::class, 'verifyPurchase']);
            Route::get('/status', function (\Illuminate\Http\Request $request) {
                $user = $request->user();
                return response()->json([
                    'success' => true,
                    'data' => [
                        'tier' => $user->subscription_tier,
                        'is_pro' => $user->isPro(),
                        'expires_at' => $user->subscription_expires_at?->toIso8601String(),
                        'daily_scan_limit' => $user->getDailyScanLimit(),
                        'max_collection_limit' => $user->getMaxCollectionLimit(),
                    ],
                ]);
            });
        });
    });

    // 6. Çoklu Para Birimi ve Döviz Kurları (ECB)
    Route::get('/currencies', [\App\Http\Controllers\Api\CurrencyApiController::class, 'index']);
    Route::post('/currencies/convert', [\App\Http\Controllers\Api\CurrencyApiController::class, 'convert']);

    // 7. Epigrafik Lejant Tamamlama & Dairesel Polar OCR Motoru
    Route::prefix('epigraphy')->group(function () {
        Route::post('/reconstruct-text', [\App\Http\Controllers\Api\EpigraphyApiController::class, 'reconstructFromText']);
        Route::post('/reconstruct-image', [\App\Http\Controllers\Api\EpigraphyApiController::class, 'reconstructFromImage'])
            ->middleware(\App\Http\Middleware\CheckScanQuota::class);
    });

    // 8. Sikke Kimlikleme Kulübü (Topluluk & Doğrulanmış Nümizmat Danışma)
    Route::prefix('club')->group(function () {
        Route::get('/identifications', [\App\Http\Controllers\Api\IdentificationApiController::class, 'index']);
        Route::get('/identifications/{id}', [\App\Http\Controllers\Api\IdentificationApiController::class, 'show']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/identifications', [\App\Http\Controllers\Api\IdentificationApiController::class, 'store']);
            Route::post('/identifications/{id}/comments', [\App\Http\Controllers\Api\IdentificationApiController::class, 'addComment']);
            Route::post('/identifications/{id}/verify', [\App\Http\Controllers\Api\IdentificationApiController::class, 'verify']);
        });
    });

    // 9. Antik Sikke Tarih ve Regnal Yıl Hesaplayıcı (Roma TR P/COS, Grek A.S., Osmanlı Cülus)
    Route::prefix('dating')->group(function () {
        Route::post('/convert', [\App\Http\Controllers\Api\RegnalCalculatorApiController::class, 'convert']);
        Route::get('/reference-data', [\App\Http\Controllers\Api\RegnalCalculatorApiController::class, 'referenceData']);
    });

});
