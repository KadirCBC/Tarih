<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full bg-[#080C14] text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <title><?php echo $__env->yieldContent('title', 'Antik Sikke Ansiklopedisi ve Nümizmatik Müze Kataloğu'); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('meta_description', 'Akademik antik sikke kataloğu, Roma, Grek ve Bizans lejant çözümlemeleri, nadirlik dereceleri ve uluslararası müzayede referans değerleri.'); ?>">
    <link rel="canonical" href="<?php echo $__env->yieldContent('canonical_url', url()->current()); ?>">

    <!-- Open Graph / Sosyal Medya Meta Etiketleri -->
    <meta property="og:type" content="<?php echo $__env->yieldContent('og_type', 'website'); ?>">
    <meta property="og:title" content="<?php echo $__env->yieldContent('title', 'Antik Sikke Ansiklopedisi'); ?>">
    <meta property="og:description" content="<?php echo $__env->yieldContent('meta_description', 'Antik nümizmatik bilgi ve müze referans kataloğu.'); ?>">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <?php echo $__env->yieldContent('og_image'); ?>

    <!-- PWA & Mobile Web App Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0B0F17">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Nümizmatik">

    <!-- Tipografi: Cinzel (Tırnaklı Antik Lejant), Playfair Display & Inter (Gövde) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700;800;900&family=Cinzel+Decorative:wght@700&family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN & Genişletilmiş Tema Yapılandırması -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        // Antik Taş & Mermer Tonları
                        stone: {
                            950: '#080C14',
                            900: '#111622',
                            850: '#161E2E',
                            800: '#1F293D',
                        },
                        // Vurgu Renkleri: Antik Altın (#D4AF37)
                        gold: {
                            antique: '#D4AF37',
                            light: '#F3E5AB',
                            hover: '#E5C158',
                            burnished: '#B8860B',
                            dark: '#996515',
                        },
                        // Vurgu Renkleri: Eski Gümüş (#C0C0C0)
                        silver: {
                            antique: '#C0C0C0',
                            patina: '#94A3B8',
                            light: '#E2E8F0',
                        },
                        // Vurgu Renkleri: Bronz & Patina Yeşili (#2E8B57, #8B5A2B)
                        patina: {
                            green: '#2E8B57',
                            dark: '#1B4D3E',
                            teal: '#164E63',
                        },
                        bronze: {
                            antique: '#8B5A2B',
                            deep: '#5C3A21',
                            dark: '#3D2314',
                        },
                    },
                    fontFamily: {
                        numis: ['Cinzel', 'serif'],
                        serif: ['Cinzel', 'Playfair Display', 'Georgia', 'serif'],
                        decorative: ['Cinzel Decorative', 'serif'],
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                    boxShadow: {
                        'numis-gold': '0 0 25px -5px rgba(212, 175, 55, 0.25)',
                        'numis-patina': '0 0 25px -5px rgba(46, 139, 87, 0.25)',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js (Reaktif Mobil Menü, Açılır Pencereler & Etkileşim) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .font-legend {
            font-family: 'Cinzel', serif;
            letter-spacing: 0.08em;
        }
        .ancient-stone-bg {
            background-color: #080C14;
            background-image: radial-gradient(circle at 50% 0%, rgba(212, 175, 55, 0.05) 0%, transparent 60%);
        }
    </style>
</head>
<body class="h-full flex flex-col font-sans ancient-stone-bg selection:bg-amber-500/30 selection:text-amber-200">

    <!-- 1. Minimalist Bildirim & 2863 Sayılı Kanun Yasal Uyarı Şeridi -->
    <div class="bg-gradient-to-r from-stone-900 via-stone-950 to-stone-900 border-b border-amber-500/20 text-[11px] py-1.5 px-4 text-center text-slate-300 tracking-wide z-50">
        <div class="max-w-7xl mx-auto flex items-center justify-center gap-2">
            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-gold-antique border border-amber-500/30">
                2863 SAYILI KANUN
            </span>
            <span class="text-slate-400">
                Platformumuz akademik ve kültürel eğitim amaçlı nümizmatik arşividir. <strong class="text-slate-200">Ticari alım-satım kesinlikle yapılmamaktadır.</strong>
            </span>
        </div>
    </div>

    <!-- 2. Üst Menü (Navbar) - Alpine.js Destekli & Responsive -->
    <header 
        x-data="{ mobileMenuOpen: false, periodDropdownOpen: false }" 
        class="bg-stone-900/90 backdrop-blur-md border-b border-slate-800/80 sticky top-0 z-40 transition"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20 gap-4">

                <!-- Logo & Müze Markası -->
                <div class="flex items-center gap-3 shrink-0">
                    <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-amber-900/60 via-stone-800 to-amber-600/40 border border-gold-antique/50 flex items-center justify-center shadow-lg shadow-amber-950/50 group-hover:border-gold-antique transition">
                            <span class="text-gold-antique font-numis font-bold text-lg select-none">🏛️</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-numis font-extrabold text-base sm:text-lg tracking-wider text-slate-100 group-hover:text-gold-antique transition">
                                NÜMİZMATİK
                            </span>
                            <span class="text-[9px] uppercase tracking-widest text-gold-antique/80 font-mono -mt-1">
                                Antik Sikke Müze Kataloğu
                            </span>
                        </div>
                    </a>
                </div>

                <!-- Hızlı Lejant & Katalog Arama Çubuğu (Desktop) -->
                <div class="hidden lg:flex flex-1 max-w-md mx-4">
                    <form action="<?php echo e(route('home')); ?>" method="GET" class="w-full relative">
                        <input 
                            type="text" 
                            name="q" 
                            value="<?php echo e(request('q')); ?>"
                            placeholder="Sikke, lejant veya RIC referansı ara... (Örn: Trajan, Denarius)" 
                            class="w-full bg-stone-950/80 border border-slate-700/80 rounded-xl px-4 py-2 pl-10 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-gold-antique focus:ring-1 focus:ring-gold-antique transition"
                        >
                        <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="absolute right-2.5 top-2 px-1.5 py-0.5 rounded text-[10px] bg-slate-800 border border-slate-700 text-slate-400 font-mono select-none pointer-events-none">
                            Ctrl+K
                        </span>
                    </form>
                </div>

                <!-- Ana Navigasyon Bağlantıları (Desktop) -->
                <nav class="hidden md:flex items-center gap-5 text-xs font-medium text-slate-300">
                    
                    <!-- Dönemler Açılır Menüsü (Dropdown) -->
                    <div class="relative" @click.away="periodDropdownOpen = false">
                        <button 
                            @click="periodDropdownOpen = !periodDropdownOpen"
                            class="flex items-center gap-1.5 hover:text-gold-antique transition py-2"
                        >
                            <span>Dönemler</span>
                            <svg class="w-3.5 h-3.5 text-slate-400 transition transform" :class="{'rotate-180': periodDropdownOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div 
                            x-show="periodDropdownOpen" 
                            x-cloak
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute left-0 mt-2 w-56 bg-stone-900 border border-slate-700/80 rounded-xl shadow-2xl py-2 z-50 divide-y divide-slate-800"
                        >
                            <div class="py-1">
                                <a href="<?php echo e(route('home', ['period' => 'roma-imparatorlugu'])); ?>" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-300 hover:text-gold-antique hover:bg-stone-800/80 transition">
                                    <span class="text-amber-400">🦅</span>
                                    <span>Roma İmparatorluğu</span>
                                </a>
                                <a href="<?php echo e(route('home', ['period' => 'bizans-imparatorlugu'])); ?>" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-300 hover:text-gold-antique hover:bg-stone-800/80 transition">
                                    <span class="text-amber-400">☦️</span>
                                    <span>Bizans İmparatorluğu</span>
                                </a>
                                <a href="<?php echo e(route('home', ['period' => 'klasik-hellenistik-grek'])); ?>" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-300 hover:text-gold-antique hover:bg-stone-800/80 transition">
                                    <span class="text-amber-400">🦉</span>
                                    <span>Grek & Hellenistik</span>
                                </a>
                                <a href="<?php echo e(route('home', ['period' => 'osmanli-imparatorlugu'])); ?>" class="flex items-center gap-2 px-4 py-2 text-xs text-slate-300 hover:text-gold-antique hover:bg-stone-800/80 transition">
                                    <span class="text-amber-400">🌙</span>
                                    <span>İslami & Osmanlı</span>
                                </a>
                            </div>
                            <div class="pt-1">
                                <a href="<?php echo e(route('periods.index')); ?>" class="block px-4 py-1.5 text-[11px] text-gold-antique font-semibold hover:bg-stone-800/50">
                                    Tüm Dönemleri Gör &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="<?php echo e(route('epigraphy.index')); ?>" class="hover:text-gold-antique transition flex items-center gap-1">
                        <span>🔍</span> Lejant Çözücü
                    </a>

                    <a href="<?php echo e(route('quiz.index')); ?>" class="text-gold-antique hover:text-gold-light font-semibold flex items-center gap-1 transition">
                        <span>🎯</span> Günün Sikkesi
                    </a>

                    <a href="<?php echo e(route('stories.week')); ?>" class="hover:text-gold-antique transition flex items-center gap-1">
                        <span>⭐</span> Haftanın Sikkesi
                    </a>

                    <a href="<?php echo e(route('map.index')); ?>" class="hover:text-gold-antique transition flex items-center gap-1">
                        <span>🗺️</span> Darphane Haritası
                    </a>

                    <a href="<?php echo e(route('collection.index')); ?>" class="px-3 py-1.5 rounded-lg bg-stone-950 border border-slate-700 hover:border-gold-antique/60 text-slate-200 hover:text-gold-antique transition flex items-center gap-1.5">
                        <span class="text-amber-400">📖</span>
                        <span>Koleksiyonum</span>
                    </a>

                    <!-- Çok Dilli Seçici (i18n: TR, EN, DE) -->
                    <div class="relative group">
                        <button class="flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded bg-stone-950 hover:bg-stone-800 text-slate-200 border border-slate-700">
                            <span><?php echo e(strtoupper(app()->getLocale())); ?></span>
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="absolute right-0 mt-1 w-28 bg-stone-900 border border-slate-700 rounded-lg shadow-xl py-1 hidden group-hover:block z-50">
                            <a href="<?php echo e(route('locale.switch', 'tr')); ?>" class="block px-3 py-1.5 text-xs hover:bg-stone-800 text-slate-200 <?php echo e(app()->getLocale() === 'tr' ? 'text-gold-antique font-bold' : ''); ?>">🇹🇷 Türkçe</a>
                            <a href="<?php echo e(route('locale.switch', 'en')); ?>" class="block px-3 py-1.5 text-xs hover:bg-stone-800 text-slate-200 <?php echo e(app()->getLocale() === 'en' ? 'text-gold-antique font-bold' : ''); ?>">🇬🇧 English</a>
                            <a href="<?php echo e(route('locale.switch', 'de')); ?>" class="block px-3 py-1.5 text-xs hover:bg-stone-800 text-slate-200 <?php echo e(app()->getLocale() === 'de' ? 'text-gold-antique font-bold' : ''); ?>">🇩🇪 Deutsch</a>
                        </div>
                    </div>

                    <!-- Otomatik Para Birimi Seçici (USD, EUR, TRY) -->
                    <div class="relative group">
                        <button class="flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded bg-amber-500/10 hover:bg-amber-500/20 text-gold-antique border border-amber-500/30">
                            <span><?php echo e(session('user_currency', 'USD')); ?></span>
                            <svg class="w-3 h-3 text-gold-antique" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="absolute right-0 mt-1 w-28 bg-stone-900 border border-slate-700 rounded-lg shadow-xl py-1 hidden group-hover:block z-50">
                            <a href="<?php echo e(route('currency.switch', 'USD')); ?>" class="block px-3 py-1.5 text-xs hover:bg-stone-800 text-slate-200 <?php echo e(session('user_currency', 'USD') === 'USD' ? 'text-gold-antique font-bold' : ''); ?>">$ USD</a>
                            <a href="<?php echo e(route('currency.switch', 'EUR')); ?>" class="block px-3 py-1.5 text-xs hover:bg-stone-800 text-slate-200 <?php echo e(session('user_currency', 'EUR') === 'EUR' ? 'text-gold-antique font-bold' : ''); ?>">€ EUR</a>
                            <a href="<?php echo e(route('currency.switch', 'TRY')); ?>" class="block px-3 py-1.5 text-xs hover:bg-stone-800 text-slate-200 <?php echo e(session('user_currency', 'TRY') === 'TRY' ? 'text-gold-antique font-bold' : ''); ?>">₺ TRY</a>
                        </div>
                    </div>
                </nav>

                <!-- Mobil Hamburger Menü Butonu -->
                <div class="flex items-center md:hidden">
                    <button 
                        @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="p-2 rounded-lg bg-stone-950 border border-slate-700 text-slate-300 hover:text-gold-antique focus:outline-none"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobil Açılır Menü (Slide-Down Drawer) -->
        <div 
            x-show="mobileMenuOpen" 
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="md:hidden bg-stone-900 border-b border-slate-800 px-4 pt-3 pb-6 space-y-3"
        >
            <form action="<?php echo e(route('home')); ?>" method="GET" class="w-full relative mb-4">
                <input 
                    type="text" 
                    name="q" 
                    placeholder="Sikke veya lejant ara..." 
                    class="w-full bg-stone-950 border border-slate-700 rounded-xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-gold-antique"
                >
                <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </form>

            <a href="<?php echo e(route('home')); ?>" class="block px-3 py-2 rounded-lg text-sm font-medium hover:bg-stone-800 text-slate-200">🏛️ Katalog Ana Sayfa</a>
            <a href="<?php echo e(route('periods.index')); ?>" class="block px-3 py-2 rounded-lg text-sm font-medium hover:bg-stone-800 text-slate-200">👑 Tarihsel Dönemler</a>
            <a href="<?php echo e(route('epigraphy.index')); ?>" class="block px-3 py-2 rounded-lg text-sm font-medium hover:bg-stone-800 text-slate-200">🔍 Epigrafik Lejant Çözücü</a>
            <a href="<?php echo e(route('quiz.index')); ?>" class="block px-3 py-2 rounded-lg text-sm font-semibold hover:bg-stone-800 text-gold-antique">🎯 Günün Sikkesi Yarışması</a>
            <a href="<?php echo e(route('map.index')); ?>" class="block px-3 py-2 rounded-lg text-sm font-medium hover:bg-stone-800 text-slate-200">🗺️ Antik Darphane Haritası</a>
            <a href="<?php echo e(route('collection.index')); ?>" class="block px-3 py-2 rounded-lg text-sm font-medium bg-amber-500/10 border border-amber-500/30 text-gold-antique">📖 Kişisel Koleksiyonum</a>

            <div class="pt-3 border-t border-slate-800 flex items-center justify-between">
                <span class="text-xs text-slate-400">Dil / Para Birimi:</span>
                <div class="flex items-center gap-2">
                    <a href="<?php echo e(route('locale.switch', 'tr')); ?>" class="px-2 py-1 text-xs rounded bg-stone-950 border border-slate-700 text-slate-200">TR</a>
                    <a href="<?php echo e(route('locale.switch', 'en')); ?>" class="px-2 py-1 text-xs rounded bg-stone-950 border border-slate-700 text-slate-200">EN</a>
                    <a href="<?php echo e(route('currency.switch', 'USD')); ?>" class="px-2 py-1 text-xs rounded bg-amber-500/20 text-gold-antique font-bold">USD</a>
                    <a href="<?php echo e(route('currency.switch', 'TRY')); ?>" class="px-2 py-1 text-xs rounded bg-stone-950 border border-slate-700 text-slate-200">TRY</a>
                </div>
            </div>
        </div>
    </header>

    <!-- 3. Ana Gövde İçeriği -->
    <main class="flex-1">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- 4. "Haftanın Sikkesi" E-posta Bülteni Kayıt Bileşeni -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php echo $__env->make('components.newsletter-card', ['source' => 'global_layout'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <!-- 5. Alt Menü (Footer) - Hiyerarşik Dönemler & Akademik Kaynaklar -->
    <footer class="bg-stone-900 border-t border-slate-800 mt-16 text-slate-400 text-xs py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8">
            
            <!-- Sütun 1: Müze & Proje Tanımı -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-gold-antique font-numis font-bold text-base">🏛️</span>
                    <h3 class="font-numis font-bold text-slate-200 text-sm tracking-wider">
                        NÜMİZMATİK ANSİKLOPEDİSİ
                    </h3>
                </div>
                <p class="leading-relaxed text-slate-400 text-xs mb-4">
                    Antik Grek, Roma İmparatorluğu, Bizans ve İslami medeniyetlere ait sikkelerin tipolojik tasnifi, lejant çözümlemeleri ve akademik nümizmatik referans kütüphanesidir.
                </p>
                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-stone-950 border border-slate-700 text-[11px] text-slate-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Açık Akademik Arşiv</span>
                </div>
            </div>

            <!-- Sütun 2: Hiyerarşik Dönem Bağlantıları -->
            <div>
                <h4 class="font-semibold text-slate-200 text-sm mb-3 uppercase tracking-wider font-numis">Dönemler</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="<?php echo e(route('home', ['period' => 'roma-imparatorlugu'])); ?>" class="hover:text-gold-antique transition">Roma İmparatorluğu (M.Ö. 27 - M.S. 476)</a></li>
                    <li><a href="<?php echo e(route('home', ['period' => 'bizans-imparatorlugu'])); ?>" class="hover:text-gold-antique transition">Bizans İmparatorluğu (M.S. 491 - 1453)</a></li>
                    <li><a href="<?php echo e(route('home', ['period' => 'klasik-hellenistik-grek'])); ?>" class="hover:text-gold-antique transition">Klasik & Hellenistik Grek</a></li>
                    <li><a href="<?php echo e(route('home', ['period' => 'osmanli-imparatorlugu'])); ?>" class="hover:text-gold-antique transition">Osmanlı & İslami Sikkeler</a></li>
                    <li><a href="<?php echo e(route('periods.index')); ?>" class="text-gold-antique hover:underline transition font-medium">Tüm Dönem Kataloğu &rarr;</a></li>
                </ul>
            </div>

            <!-- Sütun 3: Akademik Kaynaklar & Linked Open Data -->
            <div>
                <h4 class="font-semibold text-slate-200 text-sm mb-3 uppercase tracking-wider font-numis">Akademik Standartlar</h4>
                <div class="flex flex-wrap gap-1.5 text-[11px] font-mono text-slate-300 mb-3">
                    <span class="bg-stone-950 border border-slate-700/80 px-2 py-1 rounded">RIC (Roman Imperial)</span>
                    <span class="bg-stone-950 border border-slate-700/80 px-2 py-1 rounded">RPC (Provincial)</span>
                    <span class="bg-stone-950 border border-slate-700/80 px-2 py-1 rounded">BMC (British Museum)</span>
                    <span class="bg-stone-950 border border-slate-700/80 px-2 py-1 rounded">Nomisma.org LOD</span>
                    <span class="bg-stone-950 border border-slate-700/80 px-2 py-1 rounded">ANS (Numismatics.org)</span>
                </div>
                <p class="text-[11px] text-slate-500 leading-normal">
                    Tüm sikke detay sayfaları IIIF Image API 3.0 ve Nomisma.org ontolojisine uygun JSON-LD formatında sunulmaktadır.
                </p>
            </div>

            <!-- Sütun 4: 2863 Sayılı Kanun & Hukuki Sorumluluk Reddi -->
            <div>
                <h4 class="font-semibold text-slate-200 text-sm mb-3 uppercase tracking-wider font-numis">Yasal Uyarı</h4>
                <p class="leading-relaxed text-slate-400 text-[11px]">
                    Sitemizde yer alan tüm sikke görselleri ve verileri müzeler, açık akademik arşivler ve kamuya açık uluslararası müzayede veritabanlarından derlenmiştir.
                </p>
                <div class="mt-2.5 p-2 bg-stone-950 border border-amber-500/20 rounded text-[11px] text-slate-300">
                    ⚠️ <strong>2863 Sayılı Kanun Uyarınca:</strong> Tarihi eserlerin ticareti veya aracılığı kesinlikle yasaktır ve sitemizde yapılmamaktadır.
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 pt-6 border-t border-slate-800 text-center text-slate-500 text-[11px]">
            &copy; <?php echo e(date('Y')); ?> Antik Sikke Bilgi ve Referans Kataloğu. Akademik ve Kültürel Miras Projesidir.
        </div>
    </footer>

    <!-- Otomatik Hukuki Aydınlatma ve Onay Modalı -->
    <?php echo $__env->make('components.legal-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Çevrimdışı / Çevrimiçi Ağ Durum Bildirimi (PWA) -->
    <div id="pwa-offline-pill" class="fixed bottom-4 left-4 z-50 bg-stone-900 text-gold-antique border border-amber-500/40 text-xs px-3.5 py-1.5 rounded-full shadow-2xl backdrop-blur-md hidden items-center gap-2 font-mono">
        <span class="w-2 h-2 rounded-full bg-gold-antique animate-pulse"></span>
        <span>Çevrimdışı Mod (Yerel SQLite)</span>
    </div>

    <!-- PWA & Anti-Scraping Koruması Scriptleri -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js')
                    .then((reg) => console.log('PWA Service Worker aktif:', reg.scope))
                    .catch((err) => console.warn('PWA Service Worker kaydı başarısız:', err));
            });
        }

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

        // Görsel Kopyalama ve Scraping Koruması
        document.addEventListener('contextmenu', function(e) {
            if (e.target && (e.target.tagName === 'IMG' || e.target.closest('.aspect-square') || e.target.closest('#ancient-map') || e.target.closest('#openseadragon-canvas'))) {
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
                toast.className = 'fixed bottom-6 right-6 z-50 bg-stone-900 border border-gold-antique/50 text-gold-light text-xs px-4 py-2.5 rounded-xl shadow-2xl backdrop-blur-md flex items-center gap-2.5 font-medium transition duration-300 transform translate-y-4 opacity-0 pointer-events-none';
                toast.innerHTML = '<span>🔒</span> <span>Bu nümizmatik arşiv görseli koruma altındadır. 2863 sayılı kanun uyarınca ticari amaçla kopyalanamaz.</span>';
                document.body.appendChild(toast);
            }
            toast.classList.remove('translate-y-4', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('translate-y-4', 'opacity-0');
            }, 3000);
        }
    </script>
</body>
</html>
<?php /**PATH C:\Users\taner\Documents\GitHub\Tarih\resources\views/layouts/app.blade.php ENDPATH**/ ?>