<!DOCTYPE html>
<html lang="tr" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Güvenlik Doğrulaması - Tarih Nümizmatik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Cloudflare Turnstile Script -->
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</head>
<body class="h-full flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-slate-900 border border-slate-800 rounded-2xl p-8 shadow-2xl text-center">
        <!-- İkon -->
        <div class="w-16 h-16 mx-auto mb-4 bg-amber-500/10 border border-amber-500/30 rounded-2xl flex items-center justify-center text-amber-400">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>

        <h1 class="text-xl font-bold text-white mb-2">Güvenlik Doğrulaması</h1>
        <p class="text-xs text-slate-400 mb-6 leading-relaxed">
            Nümizmatik veritabanımızı otomatik kazıma (scraping) ve bot saldırılarına karşı korumak için kısa bir doğrulama yapmanız gerekmektedir.
        </p>

        <!-- Turnstile Formu -->
        <form action="{{ route('security.verify-turnstile') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="intended_url" value="{{ $intended_url ?? url('/') }}">

            <!-- Cloudflare Turnstile Widget (Demo Key veya Canlı Key) -->
            <div class="flex justify-center my-4">
                <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.key', '1x00000000000000000000AA') }}" data-theme="dark"></div>
            </div>

            <!-- Fallback Buton (Turnstile otomatik veya manuel onay) -->
            <button type="submit" class="w-full py-2.5 px-4 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl text-xs transition shadow-lg shadow-amber-500/20">
                Doğrulamayı Onayla ve Devam Et
            </button>
        </form>

        <div class="mt-6 pt-4 border-t border-slate-800 text-[11px] text-slate-500 flex items-center justify-between">
            <span>IP: {{ $ip ?? request()->ip() }}</span>
            <span>Cloudflare Turnstile Koruması</span>
        </div>
    </div>
</body>
</html>

