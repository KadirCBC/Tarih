<!DOCTYPE html>
<html lang="tr" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <title>@yield('title', 'Antik Sikke Ansiklopedisi ve Nümizmatik Referans Kataloğu')</title>
    <meta name="description" content="@yield('meta_description', 'Akademik antik sikke kataloğu, lejant çözümlemeleri, nadirlik dereceleri ve uluslararası müzayede referans değerleri.')">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('title', 'Antik Sikke Ansiklopedisi')">
    <meta property="og:description" content="@yield('meta_description', 'Antik nümizmatik bilgi ve referans kataloğu.')">
    <meta property="og:url" content="{{ url()->current() }}">
    @yield('og_image')

    <!-- PWA & Mobile Web App Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#d97706">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Nümizmatik">

    <!-- Google Fonts: Inter & Cinzel for Ancient Inscriptions -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;900&family=Inter:wght@300;400;500;600;700&family=Cinzel+Decorative:wght@700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        numis: ['Cinzel', 'serif'],
                        decorative: ['Cinzel Decorative', 'serif'],
                    },
                    colors: {
                        amber: {
                            450: '#f59e0b',
                            550: '#d97706',
                        },
                        bronze: {
                            800: '#5c3a21',
                            900: '#382212',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        .font-legend {
            font-family: 'Cinzel', serif;
            letter-spacing: 0.08em;
        }
    </style>

    @yield('schema_org')
</head>
<body class="min-h-full flex flex-col bg-slate-950 text-slate-200 antialiased selection:bg-amber-500 selection:text-slate-950">

    <!-- Üst Yasal Uyarı Şeridi -->
    <div class="bg-amber-950/70 border-b border-amber-500/20 px-4 py-1.5 text-xs text-amber-300 text-center flex items-center justify-center gap-2">
        <svg class="w-4 h-4 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <span><strong>2863 Sayılı Kanun Uyarınca:</strong> Bu platform kâr amacı gütmeyen, akademik araştırma ve nümizmatik eğitim arşividir. Kesinlikle alım-satım veya aracılık faaliyeti yapılmamaktadır.</span>
    </div>

    <!-- Header / Navbar -->
    <header class="sticky top-0 z-40 bg-slate-900/90 backdrop-blur-md border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-amber-600 via-yellow-500 to-amber-300 flex items-center justify-center shadow-lg shadow-amber-500/10 group-hover:scale-105 transition">
                    <span class="font-decorative text-slate-950 font-bold text-xl">N</span>
                </div>
                <div>
                    <span class="font-numis font-bold text-lg text-slate-100 tracking-wider block leading-tight group-hover:text-amber-400 transition">NÜMİZMATİK</span>
                    <span class="text-[10px] text-slate-400 tracking-widest uppercase">Antik Sikke Ansiklopedisi</span>
                </div>
            </a>

            <!-- Hızlı Arama Formu -->
            <form action="{{ route('coins.search') }}" method="GET" class="hidden md:flex flex-1 max-w-md mx-6">
                <div class="relative w-full">
                    <input type="text" 
                           name="q" 
                           value="{{ request('q', $searchTerm ?? '') }}"
                           placeholder="Lejant veya İmparator ara (örn: AVG, TRAIAN, RIC 207)..." 
                           class="w-full bg-slate-800/80 border border-slate-700 rounded-lg pl-10 pr-4 py-2 text-sm text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </form>

            <nav class="flex items-center gap-4 text-sm font-medium text-slate-300">
                <a href="{{ route('coins.index') }}" class="hover:text-amber-400 transition">Katalog</a>
                <a href="{{ route('map.index') }}" class="text-amber-300 hover:text-amber-400 font-semibold flex items-center gap-1 transition">
                    <span>🗺️</span> Harita
                </a>
                <a href="{{ route('calculator.index') }}" class="hover:text-amber-400 transition flex items-center gap-1">
                    <span>⏳</span> Tarih Hesaplayıcı
                </a>
                <a href="{{ route('identifications.index') }}" class="text-amber-300 hover:text-amber-400 font-semibold flex items-center gap-1 transition">
                    <span>🔍</span> Kulüp
                </a>
                <a href="{{ route('epigraphy.index') }}" class="hover:text-amber-400 transition">Lejant Çözücü</a>
                <a href="{{ route('stories.week') }}" class="text-amber-400 hover:text-amber-300 font-semibold flex items-center gap-1 transition">
                    <span>⭐</span> Haftanın Sikkesi
                </a>
                <a href="{{ route('periods.index') }}" class="hover:text-amber-400 transition">Dönemler</a>
                <a href="{{ route('rulers.index') }}" class="hover:text-amber-400 transition">Hükümdarlar</a>
                <a href="{{ route('collection.index') }}" class="text-slate-300 hover:text-amber-400 transition">Koleksiyonum</a>

                <!-- Çok Dilli Seçici (i18n: TR, EN, DE) -->
                <div class="relative group">
                    <button class="flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700">
                        <span>{{ strtoupper(app()->getLocale()) }}</span>
                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="absolute right-0 mt-1 w-28 bg-slate-800 border border-slate-700 rounded-lg shadow-xl py-1 hidden group-hover:block z-50">
                        <a href="{{ route('locale.switch', 'tr') }}" class="block px-3 py-1.5 text-xs hover:bg-slate-700 text-slate-200 {{ app()->getLocale() === 'tr' ? 'text-amber-400 font-bold' : '' }}">🇹🇷 Türkçe</a>
                        <a href="{{ route('locale.switch', 'en') }}" class="block px-3 py-1.5 text-xs hover:bg-slate-700 text-slate-200 {{ app()->getLocale() === 'en' ? 'text-amber-400 font-bold' : '' }}">🇬🇧 English</a>
                        <a href="{{ route('locale.switch', 'de') }}" class="block px-3 py-1.5 text-xs hover:bg-slate-700 text-slate-200 {{ app()->getLocale() === 'de' ? 'text-amber-400 font-bold' : '' }}">🇩🇪 Deutsch</a>
                    </div>
                </div>

                <!-- Otomatik Para Birimi Seçici (USD, EUR, TRY) -->
                <div class="relative group">
                    <button class="flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        <span>{{ session('user_currency', 'USD') }}</span>
                        <svg class="w-3 h-3 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div class="absolute right-0 mt-1 w-28 bg-slate-800 border border-slate-700 rounded-lg shadow-xl py-1 hidden group-hover:block z-50">
                        <a href="{{ route('currency.switch', 'USD') }}" class="block px-3 py-1.5 text-xs hover:bg-slate-700 text-slate-200 {{ session('user_currency', 'USD') === 'USD' ? 'text-amber-400 font-bold' : '' }}">$ USD</a>
                        <a href="{{ route('currency.switch', 'EUR') }}" class="block px-3 py-1.5 text-xs hover:bg-slate-700 text-slate-200 {{ session('user_currency') === 'EUR' ? 'text-amber-400 font-bold' : '' }}">€ EUR</a>
                        <a href="{{ route('currency.switch', 'TRY') }}" class="block px-3 py-1.5 text-xs hover:bg-slate-700 text-slate-200 {{ session('user_currency') === 'TRY' ? 'text-amber-400 font-bold' : '' }}">₺ TRY</a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Ana İçerik -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 border-t border-slate-800 mt-16 text-slate-400 text-xs py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="font-numis font-bold text-slate-200 text-sm tracking-wider mb-3">NÜMİZMATİK ANSİKLOPEDİSİ</h3>
                <p class="leading-relaxed">
                    Antik Grek, Roma, Bizans ve Anadolu medeniyetlerine ait sikkelerin tipolojik tasnifi, lejant çözümlemeleri ve akademik nümizmatik referans kütüphanesidir.
                </p>
            </div>
            <div>
                <h3 class="font-semibold text-slate-200 text-sm mb-3">Yasal Bildirim & Nümizmatik Mevzuat</h3>
                <p class="leading-relaxed text-slate-400">
                    Sitemizde yer alan tüm sikke görselleri ve verileri müzeler, açık akademik arşivler ve kamuya açık uluslararası müzayede veritabanlarından (RIC, RPC, BMC) derlenmiştir. 2863 sayılı Kültür ve Tabiat Varlıklarını Koruma Kanunu uyarınca tarihi eserlerin ticareti veya aracılığı kesinlikle yasaktır ve yapılmamaktadır.
                </p>
            </div>
            <div>
                <h3 class="font-semibold text-slate-200 text-sm mb-3">Referans Sistemleri</h3>
                <div class="flex flex-wrap gap-2 text-[11px] font-mono text-slate-300">
                    <span class="bg-slate-800 px-2 py-1 rounded">RIC (Roman Imperial Coinage)</span>
                    <span class="bg-slate-800 px-2 py-1 rounded">RPC (Roman Provincial)</span>
                    <span class="bg-slate-800 px-2 py-1 rounded">BMC (British Museum)</span>
                    <span class="bg-slate-800 px-2 py-1 rounded">Sear Byzantine</span>
                    <span class="bg-slate-800 px-2 py-1 rounded">Crawford (RRC)</span>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pt-6 border-t border-slate-800/80 text-center text-slate-500">
            &copy; {{ date('Y') }} Antik Sikke Bilgi ve Referans Kataloğu. Akademik ve Kültürel Miras Projesidir.
        </div>
    </footer>

    <!-- Otomatik Hukuki Aydınlatma ve Onay Modalı -->
    @include('components.legal-modal')

    <!-- Çevrimdışı / Çevrimiçi Ağ Durum Bildirimi -->
    <div id="pwa-offline-pill" class="fixed bottom-4 left-4 z-50 bg-amber-950/90 text-amber-200 border border-amber-500/40 text-xs px-3.5 py-1.5 rounded-full shadow-2xl backdrop-blur-md hidden items-center gap-2 font-mono">
        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
        <span>Çevrimdışı Mod (Yerel Önbellek)</span>
    </div>

    <!-- PWA Service Worker Kaydı -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js')
                    .then((reg) => console.log('PWA Service Worker aktif:', reg.scope))
                    .catch((err) => console.warn('PWA Service Worker kaydı başarısız:', err));
            });
        }

        // Çevrimdışı durum kontrolü
        function updateNetworkStatus() {
            const pill = document.getElementById('pwa-offline-pill');
            if (pill) {
                if (!navigator.onLine) {
                    pill.classList.remove('hidden');
                    pill.classList.add('flex');
                } else {
                    pill.classList.remove('flex');
                    pill.classList.add('hidden');
                }
            }
        }
        window.addEventListener('online', updateNetworkStatus);
        window.addEventListener('offline', updateNetworkStatus);
        updateNetworkStatus();
    </script>
</body>
</html>

