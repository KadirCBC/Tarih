<?php

use App\Http\Controllers\CoinController;
use App\Http\Controllers\PeriodController;
use App\Http\Controllers\RulerController;
use App\Http\Controllers\UserCollectionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CoinController::class, 'index'])->name('home');
Route::get('/search', [CoinController::class, 'search'])->name('coins.search');

Route::prefix('coins')->name('coins.')->group(function () {
    Route::get('/', [CoinController::class, 'index'])->name('index');
    Route::get('/search', [CoinController::class, 'search'])->name('search');
    Route::get('/{coin:slug}', [CoinController::class, 'show'])->name('show');
});

Route::prefix('periods')->name('periods.')->group(function () {
    Route::get('/', [PeriodController::class, 'index'])->name('index');
    Route::get('/{period:slug}', [PeriodController::class, 'show'])->name('show');
});

Route::prefix('rulers')->name('rulers.')->group(function () {
    Route::get('/', [RulerController::class, 'index'])->name('index');
    Route::get('/{ruler:slug}', [RulerController::class, 'show'])->name('show');
});

Route::middleware('auth')->prefix('collection')->name('collection.')->group(function () {
    Route::get('/', [UserCollectionController::class, 'index'])->name('index');
    Route::post('/{coin}', [UserCollectionController::class, 'store'])->name('store');
    Route::delete('/{userCollection}', [UserCollectionController::class, 'destroy'])->name('destroy');
});
