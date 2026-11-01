<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AntiScrapeBotProtectionMiddleware
{
    /**
     * Saniyede 10'dan fazla katalog isteği yapan şüpheli botları tespit edip
     * Cloudflare Turnstile Captcha doğrulamasına yönlendirir.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // Doğrulama sayfası veya static asset isteklerini hariç tut
        if ($request->is('security/verify-turnstile', 'turnstile-challenge', 'up', 'health')) {
            return $next($request);
        }

        // 1. Kullanıcının aktif bir Captcha engeli var mı?
        $challengeKey = "turnstile_challenge:{$ip}";
        if (Cache::get($challengeKey)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'BOT_SCRAPING_CHALLENGE_REQUIRED',
                    'message' => 'Olağandışı yüksek frekanslı istek tespit edildi. Lütfen tarayıcınızda Cloudflare doğrulamasını tamamlayın.',
                    'challenge_url' => url('/security/verify-turnstile'),
                ], 429);
            }

            return response()->view('security.turnstile_challenge', [
                'intended_url' => $request->fullUrl(),
                'ip' => $ip,
            ], 429);
        }

        // 2. Anlık Saniyelik İstek Sayacı (Rolling 1-Second Burst Window)
        $currentSecond = time();
        $burstKey = "scrape_burst:{$ip}:{$currentSecond}";
        $requestCount = Cache::increment($burstKey);

        if ($requestCount === 1) {
            Cache::put($burstKey, 1, 2); // 2 saniye sonra otomatik silinsin
        }

        // Eğer tek bir saniye içinde 10'dan fazla katalog/sayfa isteği gelirse bot alarmı tetiklenir
        if ($requestCount > 10) {
            Log::warning("AntiScrape: Şüpheli bot kazıma davranışı tespit edildi. IP: {$ip}, İstek Hızı: {$requestCount}/sn. Turnstile Captcha tetiklendi.");

            // 15 dakika boyunca Captcha zorunlu kılınır
            Cache::put($challengeKey, true, now()->addMinutes(15));

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error_code' => 'BOT_SCRAPING_CHALLENGE_REQUIRED',
                    'message' => 'Olağandışı yüksek frekanslı istek tespit edildi. Lütfen tarayıcınızda Cloudflare doğrulamasını tamamlayın.',
                    'challenge_url' => url('/security/verify-turnstile'),
                ], 429);
            }

            return response()->view('security.turnstile_challenge', [
                'intended_url' => $request->fullUrl(),
                'ip' => $ip,
            ], 429);
        }

        return $next($request);
    }
}

