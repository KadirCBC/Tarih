<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Standart Web Kullanıcıları için Rate Limiting (Dakikada 60 istek)
        RateLimiter::for('web', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        // 2. Standart API Kullanıcıları için Rate Limiting (Dakikada 60 istek)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // 3. Yapay Zeka Görsel Arama / Tanıma Ucu (/api/v1/coins/identify)
        // IP başına dakikada kesin 5 istek sınırı
        RateLimiter::for('ai_identification', function (Request $request) {
            return Limit::perMinute(5)
                ->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'success' => false,
                        'error_code' => 'AI_RATE_LIMIT_EXCEEDED',
                        'message' => 'Yapay zeka görsel tarama sınırına ulaştınız. IP başına dakikada maksimum 5 tarama yapılabilir. Lütfen bir süre bekleyin.',
                    ], 429, $headers);
                });
        });
    }
}

