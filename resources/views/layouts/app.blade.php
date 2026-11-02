<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <title>@yield('title', 'Historical Coin & Numismatic Encyclopedia - Classical & Royal Numismatics')</title>
    <meta name="description" content="@yield('meta_description', 'Comprehensive corporate encyclopedic archive of historical, ancient, and royal coins. Epigraphic decipherment, technical specifications, and verified auction valuations.')">
    <link rel="canonical" href="@yield('canonical_url', url()->current())">

    <!-- Open Graph / Sosyal Medya Meta Etiketleri -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('title', 'Historical Coin & Numismatic Encyclopedia')">
    <meta property="og:description" content="@yield('meta_description', 'Classical, ancient and imperial numismatic reference catalog.')">
    <meta property="og:url" content="{{ url()->current() }}">
    @yield('og_image')

    <!-- PWA & Mobile Web App Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" id="meta-theme-color" content="#F8FAFC">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Numismatics">

    <!-- 🌓 Zero-FOUC Theme Init (Light / Dark) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('numis_theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
                document.getElementById('meta-theme-color')?.setAttribute('content', '#090D16');
            } else {
                document.documentElement.classList.remove('dark');
                document.getElementById('meta-theme-color')?.setAttribute('content', '#F8FAFC');
            }
        })();
    </script>

    <!-- Tipografi: Playfair Display & Cinzel (Serif) + Poppins & Inter (Sans-Serif) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700;800;900&family=Cinzel+Decorative:wght@700&family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,800;1,400;1,600&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">

    <!-- Leaflet Harita Desteği (Detail Map View) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        gold: {
                            corporate: '#C5A059',
                            hover: '#B89047',
                            light: '#F5E6C8',
                            dark: '#D4AF37',
                            burnished: '#99732B',
                        },
                        slate: {
                            850: '#131B2E',
                            950: '#090D16',
                        }
                    },
                    fontFamily: {
                        serif: ['Playfair Display', 'Cinzel', 'Georgia', 'serif'],
                        numis: ['Cinzel', 'Playfair Display', 'serif'],
                        sans: ['Poppins', 'Inter', '-apple-system', 'BlinkMacSystemFont', 'system-ui', 'sans-serif'],
                        mono: ['JetBrains Mono', 'Fira Code', 'Consolas', 'monospace'],
                    },
                    boxShadow: {
                        'museum-light': '0 4px 20px -2px rgba(15, 23, 42, 0.05), 0 0 1px 1px rgba(15, 23, 42, 0.03)',
                        'museum-dark': '0 10px 30px -5px rgba(0, 0, 0, 0.5), 0 0 1px 1px rgba(255, 255, 255, 0.08)',
                        'gold-glow': '0 0 25px -4px rgba(197, 160, 89, 0.35)',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        /* V0 Numismatic Corporate Theme Tokens */
        :root {
            --bg-canvas: #F8FAFC;
            --bg-card: #FFFFFF;
            --bg-card-subtle: #F1F5F9;
            --text-heading: #0F172A;
            --text-body: #334155;
            --text-muted: #64748B;
            --border-primary: #E2E8F0;
            --border-subtle: #EDF2F7;
            --gold-accent: #C5A059;
            --gold-badge-bg: rgba(197, 160, 89, 0.12);
            --gold-badge-border: rgba(197, 160, 89, 0.3);
            --header-glass: rgba(255, 255, 255, 0.88);
            --card-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06), 0 1px 2px 0 rgba(15, 23, 42, 0.04);
        }

        html.dark {
            --bg-canvas: #090D16;
            --bg-card: #131B2E;
            --bg-card-subtle: #0E1524;
            --text-heading: #F8FAFC;
            --text-body: #CBD5E1;
            --text-muted: #94A3B8;
            --border-primary: #1E293B;
            --border-subtle: rgba(255, 255, 255, 0.06);
            --gold-accent: #D4AF37;
            --gold-badge-bg: rgba(212, 175, 55, 0.15);
            --gold-badge-border: rgba(212, 175, 55, 0.35);
            --header-glass: rgba(9, 13, 22, 0.88);
            --card-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.55), 0 0 1px 1px rgba(255, 255, 255, 0.07);
        }

        body {
            background-color: var(--bg-canvas);
            color: var(--text-body);
            font-family: 'Poppins', 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
            -webkit-font-smoothing: antialiased;
        }

        .museum-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-primary);
            box-shadow: var(--card-shadow);
            border-radius: 1rem;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .museum-card:hover {
            border-color: var(--gold-accent);
        }

        .header-glass {
            background-color: var(--header-glass);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid var(--border-primary);
        }

        .font-numis {
            font-family: 'Cinzel', 'Playfair Display', serif;
            letter-spacing: 0.04em;
        }

        .font-serif-title {
            font-family: 'Playfair Display', 'Cinzel', serif;
        }

        /* Ambient Museum Glow */
        .ambient-museum {
            background-image: 
                radial-gradient(circle at 50% 0%, rgba(197, 160, 89, 0.08) 0%, transparent 60%),
                radial-gradient(circle at 90% 10%, rgba(99, 102, 241, 0.03) 0%, transparent 40%);
            background-attachment: fixed;
        }
        html.dark .ambient-museum {
            background-image: 
                radial-gradient(circle at 50% 0%, rgba(212, 175, 55, 0.07) 0%, transparent 55%),
                radial-gradient(circle at 90% 10%, rgba(56, 189, 248, 0.03) 0%, transparent 40%);
            background-attachment: fixed;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.4);
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: var(--gold-accent);
        }
    </style>
</head>
<body 
    x-data="{
        theme: localStorage.getItem('numis_theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),
        mobileMenuOpen: false,
        megaMenuOpen: false,
        toggleTheme() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('numis_theme', this.theme);
            if (this.theme === 'dark') {
                document.documentElement.classList.add('dark');
                document.getElementById('meta-theme-color')?.setAttribute('content', '#090D16');
            } else {
                document.documentElement.classList.remove('dark');
                document.getElementById('meta-theme-color')?.setAttribute('content', '#F8FAFC');
            }
        }
    }"
    class="min-h-full flex flex-col ambient-museum selection:bg-amber-500/25 selection:text-amber-900 dark:selection:text-amber-200"
>

    <!-- 1. Top Bar: 2863 Sayılı Kanun & Institutional Museum Banner (Full Width) -->
    <div class="border-b border-slate-200/80 dark:border-slate-800/80 bg-slate-100/90 dark:bg-slate-950/90 text-[11px] py-1.5 px-6 lg:px-10 transition">
        <div class="w-full flex items-center justify-between gap-4">
            <div class="flex items-center gap-2 overflow-hidden truncate">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/15 text-amber-800 dark:text-amber-300 border border-amber-500/30 font-mono tracking-wider shrink-0">
                    🏛️ 2863 SAYILI KANUN UYARINCA
                </span>
                <span class="text-slate-600 dark:text-slate-400 truncate">
                    Platformumuz akademik, bilimsel araştırma ve nümizmatik eğitim amaçlıdır. <strong class="text-slate-900 dark:text-slate-200 font-semibold">Tarihi eserlerin ticari alım-satımı kesinlikle yapılmamaktadır.</strong>
                </span>
            </div>
            <div class="hidden md:flex items-center gap-3 text-[11px] text-slate-500 dark:text-slate-400 shrink-0 font-medium">
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <span>Nomisma.org LOD & IIIF 3.0 Standardı</span>
                </span>
                <span>•</span>
                <a href="{{ route('epigraphy.index') }}" class="hover:text-amber-600 dark:hover:text-gold-dark transition">Lejant Çözücü</a>
                <span>•</span>
                <a href="{{ route('quiz.index') }}" class="hover:text-amber-600 dark:hover:text-gold-dark transition">Günün Sikkesi</a>
            </div>
        </div>
    </div>

    <!-- 2. Corporate Navigation Header (site-header - Exact V0 Full Width Design) -->
    <header class="sticky top-0 z-40 bg-white/95 dark:bg-[#090D16]/95 backdrop-blur-md border-b border-slate-200/80 dark:border-slate-800 transition duration-200">
        <div class="w-full px-6 lg:px-10">
            <div class="flex items-center justify-between h-16 gap-6">

                <!-- Left: Circular N Badge + Numisma Brand + Menu Links -->
                <div class="flex items-center gap-7">
                    <!-- Brand: Circular Gold Badge 'N' + Numisma -->
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 group shrink-0">
                        <div class="w-8 h-8 rounded-full border border-amber-600/70 dark:border-amber-400/70 flex items-center justify-center font-serif font-bold text-sm text-amber-700 dark:text-amber-400 select-none group-hover:border-amber-700 transition">
                            N
                        </div>
                        <span class="font-serif text-xl font-bold tracking-tight text-slate-800 dark:text-slate-100 group-hover:text-amber-700 dark:group-hover:text-amber-400 transition">
                            Numisma
                        </span>
                    </a>

                    <!-- Nav Links: Eras, Kingdoms, Catalogue -->
                    <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-700 dark:text-slate-300">
                        
                        <!-- Eras Dropdown -->
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button 
                                @click="open = !open" 
                                type="button"
                                class="flex items-center gap-1 hover:text-slate-900 dark:hover:text-white transition py-2 cursor-pointer font-medium"
                            >
                                <span>Eras</span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div 
                                x-show="open" 
                                x-cloak
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 translate-y-1"
                                class="absolute left-0 mt-2 w-72 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl py-2.5 z-50 divide-y divide-slate-100 dark:divide-slate-800"
                            >
                                <div class="px-4 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    Kronolojik Dönemler
                                </div>
                                <div class="py-1 space-y-0.5">
                                    <a href="{{ route('home') }}#catalog" class="flex items-center justify-between px-4 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition">
                                        <span class="font-medium">Lydia & Anadolu</span>
                                        <span class="text-[10px] font-mono text-slate-400">M.Ö. 650–546</span>
                                    </a>
                                    <a href="{{ route('home') }}#catalog" class="flex items-center justify-between px-4 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition">
                                        <span class="font-medium">Arkaik & Klasik Grek</span>
                                        <span class="text-[10px] font-mono text-slate-400">M.Ö. 600–323</span>
                                    </a>
                                    <a href="{{ route('home') }}#catalog" class="flex items-center justify-between px-4 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition">
                                        <span class="font-medium">Hellenistik Krallıklar</span>
                                        <span class="text-[10px] font-mono text-slate-400">M.Ö. 323–31</span>
                                    </a>
                                    <a href="{{ route('home') }}#catalog" class="flex items-center justify-between px-4 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition">
                                        <span class="font-medium">Roma Cumhuriyeti</span>
                                        <span class="text-[10px] font-mono text-slate-400">M.Ö. 509–27</span>
                                    </a>
                                    <a href="{{ route('home') }}#catalog" class="flex items-center justify-between px-4 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition">
                                        <span class="font-medium">Roma İmparatorluğu</span>
                                        <span class="text-[10px] font-mono text-slate-400">M.Ö. 27–476</span>
                                    </a>
                                    <a href="{{ route('home') }}#catalog" class="flex items-center justify-between px-4 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition">
                                        <span class="font-medium">Bizans İmparatorluğu</span>
                                        <span class="text-[10px] font-mono text-slate-400">M.S. 491–1453</span>
                                    </a>
                                    <a href="{{ route('home') }}#catalog" class="flex items-center justify-between px-4 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition">
                                        <span class="font-medium">İslami Hilafetler</span>
                                        <span class="text-[10px] font-mono text-slate-400">M.S. 661–1258</span>
                                    </a>
                                    <a href="{{ route('home') }}#catalog" class="flex items-center justify-between px-4 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition">
                                        <span class="font-medium">Osmanlı İmparatorluğu</span>
                                        <span class="text-[10px] font-mono text-slate-400">M.S. 1299–1922</span>
                                    </a>
                                </div>
                                <div class="pt-2 px-4 pb-1">
                                    <a href="{{ route('home') }}#catalog" class="text-amber-700 dark:text-gold-dark hover:underline font-semibold block text-[11px]">
                                        Tüm Dönemleri Gör &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Kingdoms Dropdown -->
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button 
                                @click="open = !open" 
                                type="button"
                                class="flex items-center gap-1 hover:text-slate-900 dark:hover:text-white transition py-2 cursor-pointer font-medium"
                            >
                                <span>Kingdoms</span>
                                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div 
                                x-show="open" 
                                x-cloak
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 translate-y-1"
                                class="absolute left-0 mt-2 w-64 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-xl py-2.5 z-50 divide-y divide-slate-100 dark:divide-slate-800"
                            >
                                <div class="px-4 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    Hanedanlar & Krallıklar
                                </div>
                                <div class="py-1 space-y-0.5">
                                    <a href="{{ route('coins.search', ['q' => 'Augustus']) }}" class="block px-4 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition font-medium">Julio-Claudian Hanedanı</a>
                                    <a href="{{ route('coins.search', ['q' => 'Trajan']) }}" class="block px-4 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition font-medium">Nerva-Antonin Hanedanı</a>
                                    <a href="{{ route('coins.search', ['q' => 'Constantine']) }}" class="block px-4 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition font-medium">Konstantin Hanedanı</a>
                                    <a href="{{ route('coins.search', ['q' => 'Alexander']) }}" class="block px-4 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition font-medium">Makedon Krallığı</a>
                                    <a href="{{ route('coins.search', ['q' => 'Fatih']) }}" class="block px-4 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 transition font-medium">Osmanlı Sultanları</a>
                                </div>
                            </div>
                        </div>

                        <!-- Catalogue -->
                        <a href="{{ route('home') }}#catalog" class="hover:text-slate-900 dark:hover:text-white transition font-medium">
                            Catalogue
                        </a>
                    </nav>
                </div>

                <!-- Right: Search Bar + Theme Toggle Button (Exact Image 1) -->
                <div class="flex items-center gap-3">
                    
                    <!-- Search Input (Exact replica of Image 1) -->
                    <form action="{{ route('coins.search') }}" method="GET" class="relative">
                        <input 
                            type="text" 
                            name="q" 
                            value="{{ request('q') }}"
                            placeholder="Search coins, rulers, legends..." 
                            class="w-56 sm:w-72 md:w-80 lg:w-96 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg pl-9 pr-3 py-1.5 text-xs text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 transition shadow-sm"
                        >
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </form>

                    <!-- Theme Toggle: Crescent Moon in Light Mode, Sun in Dark Mode -->
                    <button 
                        @click="toggleTheme()" 
                        type="button"
                        class="p-2 text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-100 transition cursor-pointer"
                        title="Toggle Light/Dark Theme"
                        aria-label="Theme"
                    >
                        <svg x-show="theme !== 'dark'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        <svg x-show="theme === 'dark'" x-cloak class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>

                    <!-- Mobile Drawer Toggle Button -->
                    <button 
                        @click="mobileMenuOpen = !mobileMenuOpen" 
                        type="button"
                        class="md:hidden p-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg"
                        aria-label="Menu"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                </div>

            </div>
        </div>

        <!-- Mobile Drawer -->
        <div 
            x-show="mobileMenuOpen" 
            x-cloak
            x-transition
            class="md:hidden border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-6 py-4 space-y-3 shadow-xl"
        >
            <a href="{{ route('home') }}#catalog" class="block px-3 py-2 rounded-lg text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-800 dark:text-slate-200">Catalogue</a>
            <a href="{{ route('periods.index') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-800 dark:text-slate-200">Eras (Kronolojik Dönemler)</a>
            <a href="{{ route('epigraphy.index') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-800 dark:text-slate-200">Lejant Çözücü</a>
            <a href="{{ route('collection.index') }}" class="block px-3 py-2 rounded-lg text-sm font-semibold text-amber-700 dark:text-gold-dark hover:bg-amber-500/10">Kişisel Koleksiyonum</a>
        </div>
    </header>

    <!-- 3. Ana Gövde İçeriği -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- 4. Bülten Kayıt Kartı -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
        @include('components.newsletter-card', ['source' => 'global_layout'])
    </div>

    <!-- 5. Corporate Museum Footer (site-footer) -->
    <footer class="mt-20 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-xs py-14 transition duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-10">
            
            <!-- Column 1: Museum Identity -->
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-amber-700 dark:text-gold-dark font-numis font-bold text-xl">🏛️</span>
                    <h3 class="font-numis font-bold text-slate-900 dark:text-slate-100 text-sm tracking-wider">
                        HISTORICAL COIN ENCYCLOPEDIA
                    </h3>
                </div>
                <p class="leading-relaxed text-slate-600 dark:text-slate-400 text-xs mb-4">
                    Klasik Grek, Roma İmparatorluğu, Bizans, İslami ve Avrupa monarşilerine ait antik sikkelerin bilimsel tasnifi, epigrafik çözümlemeleri ve doğrulanmış müzayede satış referansları ansiklopedisi.
                </p>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-[11px] text-emerald-700 dark:text-emerald-400 font-medium">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Uluslararası Açık Akademik Arşiv</span>
                </div>
            </div>

            <!-- Column 2: Historical Eras -->
            <div>
                <h4 class="font-semibold text-slate-900 dark:text-slate-100 text-sm mb-4 uppercase tracking-wider font-numis">Kronolojik Dönemler</h4>
                <ul class="space-y-2.5 text-xs text-slate-600 dark:text-slate-400">
                    <li><a href="{{ route('home', ['period' => 'roma-imparatorlugu']) }}" class="hover:text-amber-700 dark:hover:text-gold-dark transition">Roma İmparatorluğu (M.Ö. 27 – M.S. 476)</a></li>
                    <li><a href="{{ route('home', ['period' => 'bizans-imparatorlugu']) }}" class="hover:text-amber-700 dark:hover:text-gold-dark transition">Bizans İmparatorluğu (M.S. 491 – 1453)</a></li>
                    <li><a href="{{ route('home', ['period' => 'klasik-hellenistik-grek']) }}" class="hover:text-amber-700 dark:hover:text-gold-dark transition">Klasik & Hellenistik Grek</a></li>
                    <li><a href="{{ route('home', ['period' => 'osmanli-imparatorlugu']) }}" class="hover:text-amber-700 dark:hover:text-gold-dark transition">Osmanlı & İslami Sikkeler</a></li>
                    <li><a href="{{ route('periods.index') }}" class="text-amber-700 dark:text-gold-dark hover:underline transition font-semibold">Tüm Dönem Kataloğu &rarr;</a></li>
                </ul>
            </div>

            <!-- Column 3: Academic Standards & LOD -->
            <div>
                <h4 class="font-semibold text-slate-900 dark:text-slate-100 text-sm mb-4 uppercase tracking-wider font-numis">Akademik Standartlar</h4>
                <div class="flex flex-wrap gap-1.5 text-[11px] font-mono text-slate-700 dark:text-slate-300 mb-4">
                    <span class="bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 px-2.5 py-1 rounded">RIC (Roman Imperial)</span>
                    <span class="bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 px-2.5 py-1 rounded">RPC (Provincial)</span>
                    <span class="bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 px-2.5 py-1 rounded">BMC (British Museum)</span>
                    <span class="bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 px-2.5 py-1 rounded">Nomisma.org LOD</span>
                    <span class="bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-800 px-2.5 py-1 rounded">ANS (Numismatics.org)</span>
                </div>
                <p class="text-[11px] text-slate-500 leading-normal">
                    Kataloğumuz IIIF Image API 3.0 yüksek çözünürlük mikroskobik inceleme ve Nomisma ontolojisi Linked Open Data standardına tam uyumludur.
                </p>
            </div>

            <!-- Column 4: Legal 2863 Compliance -->
            <div>
                <h4 class="font-semibold text-slate-900 dark:text-slate-100 text-sm mb-4 uppercase tracking-wider font-numis">Yasal Sorumluluk Reddi</h4>
                <p class="leading-relaxed text-slate-600 dark:text-slate-400 text-[11px] mb-3">
                    Sitede sunulan tüm veriler ve görseller uluslararası açık nümizmatik arşivleri, müzeler ve açık müzayede veritabanlarından akademik atıf ilkeleriyle derlenmiştir.
                </p>
                <div class="p-3 bg-amber-500/10 border border-amber-500/30 rounded-xl text-[11px] text-slate-700 dark:text-slate-300 leading-relaxed">
                    ⚠️ <strong>2863 Sayılı Kanun Kapsamında:</strong> Platform üzerinde kültür varlığı alım-satımı veya definecilik aracılığı kesinlikle yasaktır ve teknik olarak engellenmiştir.
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 pt-6 border-t border-slate-200 dark:border-slate-800 text-center text-slate-500 text-[11px]">
            &copy; {{ date('Y') }} Historical Coin & Numismatic Encyclopedia. Tüm hakları saklıdır. Akademik ve kültürel miras projesidir.
        </div>
    </footer>

    <!-- Otomatik Hukuki Aydınlatma ve Onay Modalı -->
    @include('components.legal-modal')

    <!-- PWA Offline Pill -->
    <div id="pwa-offline-pill" class="fixed bottom-4 left-4 z-50 museum-card px-4 py-2 text-amber-700 dark:text-gold-dark text-xs hidden items-center gap-2 font-mono">
        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
        <span>Çevrimdışı Mod (Yerel SQLite)</span>
    </div>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js').catch(console.warn);
            });
        }

        // Image copy protection toast
        document.addEventListener('contextmenu', function(e) {
            if (e.target && (e.target.tagName === 'IMG' || e.target.closest('.aspect-square') || e.target.closest('#openseadragon-canvas'))) {
                e.preventDefault();
                showImageProtectionToast();
                return false;
            }
        });

        function showImageProtectionToast() {
            let toast = document.getElementById('image-protection-toast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'image-protection-toast';
                toast.className = 'fixed bottom-6 right-6 z-50 museum-card border-amber-500/50 text-slate-900 dark:text-slate-100 text-xs px-4 py-3 shadow-2xl flex items-center gap-2.5 font-medium transition duration-300 transform translate-y-4 opacity-0 pointer-events-none';
                toast.innerHTML = '<span>🔒</span> <span>Bu nümizmatik arşiv görseli koruma altındadır. 2863 sayılı kanun uyarınca ticari amaçla kopyalanamaz.</span>';
                document.body.appendChild(toast);
            }
            toast.classList.remove('translate-y-4', 'opacity-0');
            setTimeout(() => toast.classList.add('translate-y-4', 'opacity-0'), 3000);
        }
    </script>
</body>
</html>
