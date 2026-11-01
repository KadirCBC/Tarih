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
    Route::get('/mints/{slug}', [MintApiController::class, 'show']);

    // 3. Sikke Kataloğu ve Görsel Eşleştirme (AI Identify & Recognition)
    Route::get('/coins', [CoinApiController::class, 'index']);
    Route::post('/coins/identify', [\App\Http\Controllers\Api\CoinRecognitionController::class, 'recognize']);
    Route::post('/coins/recognize', [\App\Http\Controllers\Api\CoinRecognitionController::class, 'recognize']);
    Route::get('/coins/{slug}', [CoinApiController::class, 'show']);

    // 4. Koleksiyon Defteri (Sanctum Auth Korumalı)
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/my-collection', [UserCollectionApiController::class, 'index']);
        Route::post('/my-collection/add', [UserCollectionApiController::class, 'store']);
        Route::delete('/my-collection/{id}', [UserCollectionApiController::class, 'destroy']);
    });

});
