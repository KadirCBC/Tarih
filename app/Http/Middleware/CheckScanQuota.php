<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckScanQuota
{
    /**
     * Günlük yapay zeka sikke tanıma tarama limitini denetler.
     * Ücretsiz kullanıcılar için günde 3 tarama; Nümizmat Pro için sınırsız.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('sanctum') ?? $request->user();

        // Nümizmat Pro aboneleri kotaya takılmaz
        if ($user && $user->isPro()) {
            return $next($request);
        }

        // Günlük takip anahtarı (Kullanıcı ID veya Misafir IP)
        $today = now()->format('Y-m-d');
        $identifier = $user ? "user:{$user->id}" : "ip:" . $request->ip();
        $cacheKey = "scan_quota:{$identifier}:{$today}";

        $dailyLimit = 3;
        $currentUsage = (int) Cache::get($cacheKey, 0);

        if ($currentUsage >= $dailyLimit) {
            return response()->json([
                'success' => false,
                'error_code' => 'QUOTA_EXCEEDED',
                'message' => 'Günlük 3 ücretsiz sikke tarama limitiniz doldu. Nümizmat Pro\'ya geçerek sınırsız tarama ve müzayede arşivine erişebilirsiniz.',
                'upgrade_required' => true,
                'current_tier' => 'free',
                'daily_limit' => $dailyLimit,
                'used_scans' => $currentUsage,
                'resets_at' => now()->endOfDay()->toIso8601String(),
                'plans' => [
                    'monthly' => ['price' => '$4.99', 'period' => 'month'],
                    'yearly' => ['price' => '$39.99', 'period' => 'year', 'discount' => '33% İndirim'],
                ],
            ], 429);
        }

        $response = $next($request);

        // İstek başarılı olduysa (2xx) kullanım sayacını artır
        if ($response->isSuccessful()) {
            if (!Cache::has($cacheKey)) {
                Cache::put($cacheKey, 1, now()->endOfDay());
            } else {
                Cache::increment($cacheKey);
            }
        }

        // Kalan hak başlıklarını ekle
        $remaining = max(0, $dailyLimit - ($currentUsage + 1));
        $response->headers->set('X-Scan-Limit', (string) $dailyLimit);
        $response->headers->set('X-Scan-Remaining', (string) $remaining);

        return $response;
    }
}

