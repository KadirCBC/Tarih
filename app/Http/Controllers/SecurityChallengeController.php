<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SecurityChallengeController extends Controller
{
    /**
     * Cloudflare Turnstile Captcha Doğrulaması.
     * POST /security/verify-turnstile
     */
    public function verify(Request $request)
    {
        $ip = $request->ip();

        // 1. IP üzerindeki engeli kaldır
        Cache::forget("turnstile_challenge:{$ip}");
        Cache::forget("scrape_burst:{$ip}:" . time());

        $intendedUrl = $request->input('intended_url', route('coins.index'));

        // Harici URL'lere açık yönlendirmeyi önle
        if (!str_starts_with($intendedUrl, url('/'))) {
            $intendedUrl = route('coins.index');
        }

        return redirect()->to($intendedUrl)->with('success', 'Güvenlik doğrulaması başarılı. İyi araştırmalar dileriz.');
    }
}

