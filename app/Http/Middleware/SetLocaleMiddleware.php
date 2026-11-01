<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Desteklenen sistem dilleri:
     * - tr: Türkçe (Varsayılan)
     * - en: English (Küresel Nümizmatik Standardı)
     * - de: Deutsch (Avrupa Müzayede Ekosistemi)
     */
    public const SUPPORTED_LOCALES = ['tr', 'en', 'de'];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;

        // 1. URL Query Parametresi (?lang=en veya ?locale=de)
        $requestedLocale = $request->query('lang') ?: $request->query('locale');
        if ($requestedLocale && in_array(strtolower($requestedLocale), self::SUPPORTED_LOCALES, true)) {
            $locale = strtolower($requestedLocale);
            Session::put('locale', $locale);
        }

        // 2. Özel HTTP Başlığı (Mobil uygulama için: X-Locale veya Accept-Language)
        if (!$locale && $request->hasHeader('X-Locale')) {
            $headerLocale = strtolower($request->header('X-Locale'));
            if (in_array($headerLocale, self::SUPPORTED_LOCALES, true)) {
                $locale = $headerLocale;
            }
        }

        // 3. Oturumda kayıtlı tercih
        if (!$locale && Session::has('locale')) {
            $sessionLocale = Session::get('locale');
            if (in_array($sessionLocale, self::SUPPORTED_LOCALES, true)) {
                $locale = $sessionLocale;
            }
        }

        // 4. Tarayıcı Accept-Language başlığı
        if (!$locale && $request->header('Accept-Language')) {
            $browserLocale = substr($request->server('HTTP_ACCEPT_LANGUAGE', 'tr'), 0, 2);
            if (in_array(strtolower($browserLocale), self::SUPPORTED_LOCALES, true)) {
                $locale = strtolower($browserLocale);
            }
        }

        // 5. Varsayılan dil (tr)
        $locale = $locale ?: config('app.locale', 'tr');

        App::setLocale($locale);

        $response = $next($request);

        // API istekleri için Content-Language başlığı ekle
        if (method_exists($response, 'header')) {
            $response->header('Content-Language', $locale);
        }

        return $response;
    }
}

