<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="h-full">
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
    <meta name="theme-color" id="meta-theme-color" content="#F5F5F7">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Nümizmatik">

    <!-- 🍎 Apple iOS Theme Controller (Sıfır Gecikme / Zero-FOUC) -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('numis_theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
                document.getElementById('meta-theme-color')?.setAttribute('content', '#070A11');
            } else {
                document.documentElement.classList.remove('dark');
                document.getElementById('meta-theme-color')?.setAttribute('content', '#F5F5F7');
            }
        })();
    </script>

    <!-- Tipografi: SF Pro / Inter & Cinzel Antik Lejant Başlıkları -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700;800;900&family=Cinzel+Decorative:wght@700&family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN & Genişletilmiş Tema Yapılandırması -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        ios: {
                            bg: '#F5F5F7',
                            card: '#FFFFFF',
                            darkBg: '#070A11',
                            darkCard: '#111827',
                        },
                        stone: {
                            950: '#070A11',
                            900: '#0F172A',
                            850: '#161E2E',
                            800: '#1F293D',
                        },
                        gold: {
                            antique: '#D4AF37',
                            light: '#F3E5AB',
                            hover: '#E5C158',
                            burnished: '#B8860B',
                            dark: '#996515',
                        },
                        silver: {
                            antique: '#C0C0C0',
                            patina: '#94A3B8',
                            light: '#E2E8F0',
                        },
                        patina: {
                            green: '#2E8B57',
                            dark: '#1B4D3E',
                            teal: '#164E63',
                        },
                        bronze: {
                            antique: '#8B5A2B',
                            deep: '#5C3A21',
                        },
                    },
                    fontFamily: {
                        numis: ['Cinzel', 'Playfair Display', 'serif'],
                        serif: ['Cinzel', 'Playfair Display', 'Georgia', 'serif'],
                        decorative: ['Cinzel Decorative', 'serif'],
                        sans: ['-apple-system', 'BlinkMacSystemFont', 'SF Pro Display', 'SF Pro Text', 'Inter', 'system-ui', 'sans-serif'],
                    },
                    boxShadow: {
                        'ios-glass': '0 10px 30px -5px rgba(0, 0, 0, 0.05), 0 0 1px 1px rgba(0, 0, 0, 0.04)',
                        'ios-card': '0 14px 34px -10px rgba(0, 0, 0, 0.07), inset 0 1px 0 rgba(255, 255, 255, 0.8)',
                        'ios-dark-card': '0 20px 45px -10px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.08)',
                        'numis-gold': '0 0 25px -5px rgba(212, 175, 55, 0.25)',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js (Reaktif Tema, Mobil Menü, Açılır Pencereler & Etkileşim) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>

    <!-- 🎨 iPhone Glassmorphism (Cam Efekti) & Kurumsal Tasarım Stilleri -->
    <style>
        [x-cloak] { display: none !important; }

        :root {
            --ios-bg: #F5F5F7;
            --ios-text: #0F172A;
            --ios-text-muted: #64748B;
            --ios-glass-bg: rgba(255, 255, 255, 0.78);
            --ios-glass-border: rgba(0, 0, 0, 0.07);
            --ios-glass-card-bg: rgba(255, 255, 255, 0.84);
            --ios-glass-card-border: rgba(255, 255, 255, 0.8);
            --ios-glass-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.04), 0 0 1px 1px rgba(0, 0, 0, 0.03);
            --ios-glass-highlight: inset 0 1px 0 0 rgba(255, 255, 255, 0.95);
        }

        html.dark {
            --ios-bg: #070A11;
            --ios-text: #F8FAFC;
            --ios-text-muted: #94A3B8;
            --ios-glass-bg: rgba(13, 18, 30, 0.72);
            --ios-glass-border: rgba(255, 255, 255, 0.09);
            --ios-glass-card-bg: rgba(17, 24, 39, 0.68);
            --ios-glass-card-border: rgba(255, 255, 255, 0.08);
            --ios-glass-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.6), 0 0 1px 1px rgba(255, 255, 255, 0.06);
            --ios-glass-highlight: inset 0 1px 0 0 rgba(255, 255, 255, 0.08);
        }

        body {
            background-color: var(--ios-bg);
            color: var(--ios-text);
            transition: background-color 0.35s cubic-bezier(0.4, 0, 0.2, 1), color 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Inter", system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Ambient Apple Işıltısı (Specular Ambient Glow) */
        .ios-ambient-glow {
            background-image: 
                radial-gradient(circle at 50% -10%, rgba(212, 175, 55, 0.10) 0%, transparent 45%),
                radial-gradient(circle at 85% 15%, rgba(99, 102, 241, 0.03) 0%, transparent 35%),
                radial-gradient(circle at 15% 35%, rgba(46, 139, 87, 0.03) 0%, transparent 35%);
            background-attachment: fixed;
        }
        html.dark .ios-ambient-glow {
            background-image: 
                radial-gradient(circle at 50% -10%, rgba(212, 175, 55, 0.08) 0%, transparent 45%),
                radial-gradient(circle at 85% 15%, rgba(56, 189, 248, 0.03) 0%, transparent 35%);
            background-attachment: fixed;
        }

        /* 📱 iOS Cam Efekti Çekirdek Sınıfları */
        .ios-glass {
            background: var(--ios-glass-bg) !important;
            backdrop-filter: blur(24px) saturate(190%);
            -webkit-backdrop-filter: blur(24px) saturate(190%);
            border: 1px solid var(--ios-glass-border) !important;
            box-shadow: var(--ios-glass-shadow);
        }

        .ios-glass-card {
            background: var(--ios-glass-card-bg) !important;
            backdrop-filter: blur(24px) saturate(190%);
            -webkit-backdrop-filter: blur(24px) saturate(190%);
            border: 1px solid var(--ios-glass-card-border) !important;
            box-shadow: var(--ios-glass-shadow), var(--ios-glass-highlight);
            border-radius: 1.5rem;
            transition: transform 0.25s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.25s cubic-bezier(0.2, 0.8, 0.2, 1), border-color 0.25s ease;
        }

        .ios-glass-card:hover {
            border-color: rgba(212, 175, 55, 0.45) !important;
        }

        .font-legend {
            font-family: 'Cinzel', serif;
            letter-spacing: 0.06em;
        }

        /* 💡 Beyaz Modda Dark Sınıflarının Otomatik Cam Efektine Dönüşümü */
        html:not(.dark) .bg-slate-900,
        html:not(.dark) .bg-stone-900 {
            background-color: rgba(255, 255, 255, 0.84) !important;
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-color: rgba(0, 0, 0, 0.07) !important;
            color: #0f172a !important;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.04), inset 0 1px 0 rgba(255, 255, 255, 0.95);
        }

        html:not(.dark) .bg-slate-950,
        html:not(.dark) .bg-stone-950 {
            background-color: rgba(243, 244, 246, 0.85) !important;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-color: rgba(0, 0, 0, 0.06) !important;
            color: #0f172a !important;
        }

        html:not(.dark) .bg-slate-800,
        html:not(.dark) .bg-stone-800 {
            background-color: rgba(229, 231, 235, 0.75) !important;
            color: #1e293b !important;
            border-color: rgba(0, 0, 0, 0.06) !important;
        }

        html:not(.dark) .text-slate-100,
        html:not(.dark) .text-slate-200,
        html:not(.dark) .text-white {
            color: #0f172a !important;
        }

        html:not(.dark) .text-slate-300,
        html:not(.dark) .text-slate-400 {
            color: #475569 !important;
        }

        html:not(.dark) .text-slate-500 {
            color: #64748b !important;
        }

        html:not(.dark) .border-slate-800,
        html:not(.dark) .border-slate-700 {
            border-color: rgba(0, 0, 0, 0.08) !important;
        }

        html:not(.dark) select,
        html:not(.dark) input:not([type="checkbox"]) {
            background-color: rgba(255, 255, 255, 0.9) !important;
            color: #0f172a !important;
            border-color: rgba(0, 0, 0, 0.12) !important;
        }
        html:not(.dark) select option {
            background-color: #ffffff !important;
            color: #0f172a !important;
        }

        /* Pürüzsüz Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.35);
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(212, 175, 55, 0.5);
        }
    </style>
</head>
<body 
    x-data="{
        currentTheme: localStorage.getItem('numis_theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),
        mobileMenuOpen: false,
        periodDropdownOpen: false,
        toggleTheme() {
            this.currentTheme = this.currentTheme === 'dark' ? 'light' : 'dark';
            localStorage.setItem('numis_theme', this.currentTheme);
            if (this.currentTheme === 'dark') {
                document.documentElement.classList.add('dark');
                document.getElementById('meta-theme-color')?.setAttribute('content', '#070A11');
            } else {
                document.documentElement.classList.remove('dark');
                document.getElementById('meta-theme-color')?.setAttribute('content', '#F5F5F7');
            }
        }
    }"
    class="min-h-full flex flex-col font-sans ios-ambient-glow selection:bg-amber-500/30 selection:text-amber-800 dark:selection:text-amber-200"
>

    <!-- 1. Apple Dynamic Island Tarzı 2863 Sayılı Kanun Yasal Uyarı Bildirim Kapsülü -->
    <div class="pt-2 px-4 pb-1 z-50">
        <div class="max-w-5xl mx-auto rounded-full py-1.5 px-4 ios-glass flex items-center justify-between gap-3 text-[11px] text-slate-600 dark:text-slate-300 tracking-wide shadow-sm">
            <div class="flex items-center gap-2 overflow-hidden truncate">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-500/30 shrink-0">
                    🏛️ 2863 SAYILI KANUN
                </span>
                <span class="truncate">
                    Akademik ve kültürel nümizmatik arşividir. <strong class="font-semibold text-slate-800 dark:text-slate-100">Ticari alım-satım kesinlikle yapılmamaktadır.</strong>
                </span>
            </div>

            <!-- Hızlı Güvenlik Rozeti -->
            <div class="hidden sm:flex items-center gap-1.5 text-[10px] text-emerald-600 dark:text-emerald-400 font-medium shrink-0">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                <span>Doğrulanmış Müze Standartları</span>
            </div>
        </div>
    </div>

    <!-- 2. Apple iPhone Tarzı Yüzen Cam (Frosted Glass) Üst Menü -->
    <header class="sticky top-2 z-40 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 mt-1 transition duration-300">
        <div class="ios-glass rounded-2xl sm:rounded-3xl px-4 sm:px-6 h-18 sm:h-20 flex items-center justify-between gap-3 sm:gap-4 shadow-lg shadow-black/[0.03] dark:shadow-black/40">

            <!-- Logo & Marka -->
            <div class="flex items-center gap-3 shrink-0">
                <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-tr from-amber-500/20 via-white/50 dark:via-slate-800 to-amber-600/30 border border-amber-500/40 flex items-center justify-center shadow-md group-hover:scale-105 group-hover:border-gold-antique transition duration-300">
                        <span class="text-gold-burnished dark:text-gold-antique font-numis font-bold text-lg select-none">🏛️</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-numis font-extrabold text-base sm:text-lg tracking-wider text-slate-900 dark:text-slate-100 group-hover:text-amber-600 dark:group-hover:text-gold-antique transition">
                            NÜMİZMATİK
                        </span>
                        <span class="text-[9px] uppercase tracking-widest text-amber-600 dark:text-gold-antique/90 font-mono -mt-1 font-semibold">
                            Müze Kataloğu
                        </span>
                    </div>
                </a>
            </div>

            <!-- Apple Spotlight Tarzı Arama Çubuğu (Desktop) -->
            <div class="hidden lg:flex flex-1 max-w-md mx-4">
                <form action="<?php echo e(route('home')); ?>" method="GET" class="w-full relative group">
                    <input 
                        type="text" 
                        name="q" 
                        value="<?php echo e(request('q')); ?>"
                        placeholder="Sikke, lejant veya RIC referansı ara... (Örn: Trajan, Denarius)" 
                        class="w-full bg-black/[0.04] dark:bg-white/[0.06] border border-black/[0.06] dark:border-white/[0.1] rounded-2xl px-4 py-2.5 pl-10 text-xs text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:bg-white dark:focus:bg-slate-900/90 focus:border-amber-500/70 focus:ring-2 focus:ring-amber-500/20 transition shadow-inner"
                    >
                    <svg class="w-4 h-4 text-slate-400 group-focus-within:text-amber-500 absolute left-3.5 top-3 pointer-events-none transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span class="absolute right-2.5 top-2 px-1.5 py-0.5 rounded-lg text-[10px] bg-black/[0.05] dark:bg-white/[0.1] border border-black/[0.05] dark:border-white/[0.1] text-slate-400 font-mono select-none pointer-events-none">
                        ⌘K
                    </span>
                </form>
            </div>

            <!-- Navigasyon Bağlantıları & Kontroller -->
            <nav class="hidden md:flex items-center gap-4 text-xs font-medium text-slate-700 dark:text-slate-300">
                
                <!-- Dönemler Açılır Menüsü (Dropdown) -->
                <div class="relative" @click.away="periodDropdownOpen = false">
                    <button 
                        @click="periodDropdownOpen = !periodDropdownOpen"
                        class="flex items-center gap-1.5 px-3 py-2 rounded-xl hover:bg-black/5 dark:hover:bg-white/5 transition"
                    >
                        <span>Dönemler</span>
                        <svg class="w-3.5 h-3.5 text-slate-400 transition transform duration-200" :class="{'rotate-180': periodDropdownOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div 
                        x-show="periodDropdownOpen" 
                        x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                        class="absolute left-0 mt-2 w-60 ios-glass rounded-2xl shadow-2xl py-2 z-50 divide-y divide-black/5 dark:divide-white/10"
                    >
                        <div class="py-1">
                            <a href="<?php echo e(route('home', ['period' => 'roma-imparatorlugu'])); ?>" class="flex items-center gap-2.5 px-4 py-2 text-xs hover:bg-black/5 dark:hover:bg-white/10 transition">
                                <span class="text-amber-500">🦅</span>
                                <span>Roma İmparatorluğu</span>
                            </a>
                            <a href="<?php echo e(route('home', ['period' => 'bizans-imparatorlugu'])); ?>" class="flex items-center gap-2.5 px-4 py-2 text-xs hover:bg-black/5 dark:hover:bg-white/10 transition">
                                <span class="text-amber-500">☦️</span>
                                <span>Bizans İmparatorluğu</span>
                            </a>
                            <a href="<?php echo e(route('home', ['period' => 'klasik-hellenistik-grek'])); ?>" class="flex items-center gap-2.5 px-4 py-2 text-xs hover:bg-black/5 dark:hover:bg-white/10 transition">
                                <span class="text-amber-500">🦉</span>
                                <span>Grek & Hellenistik</span>
                            </a>
                            <a href="<?php echo e(route('home', ['period' => 'osmanli-imparatorlugu'])); ?>" class="flex items-center gap-2.5 px-4 py-2 text-xs hover:bg-black/5 dark:hover:bg-white/10 transition">
                                <span class="text-amber-500">🌙</span>
                                <span>İslami & Osmanlı</span>
                            </a>
                        </div>
                        <div class="pt-1">
                            <a href="<?php echo e(route('periods.index')); ?>" class="block px-4 py-2 text-[11px] text-amber-600 dark:text-gold-antique font-semibold hover:bg-black/5 dark:hover:bg-white/10">
                                Tüm Dönem Kataloğu &rarr;
                            </a>
                        </div>
                    </div>
                </div>

                <a href="<?php echo e(route('epigraphy.index')); ?>" class="px-2.5 py-1.5 rounded-xl hover:bg-black/5 dark:hover:bg-white/5 transition flex items-center gap-1.5">
                    <span>🔍</span> Lejant Çözücü
                </a>

                <a href="<?php echo e(route('quiz.index')); ?>" class="px-2.5 py-1.5 rounded-xl text-amber-600 dark:text-gold-antique font-semibold hover:bg-amber-500/10 transition flex items-center gap-1.5">
                    <span>🎯</span> Günün Sikkesi
                </a>

                <a href="<?php echo e(route('map.index')); ?>" class="px-2.5 py-1.5 rounded-xl hover:bg-black/5 dark:hover:bg-white/5 transition flex items-center gap-1.5">
                    <span>🗺️</span> Darphaneler
                </a>

                <a href="<?php echo e(route('collection.index')); ?>" class="px-3 py-1.5 rounded-xl bg-black/5 dark:bg-white/10 border border-black/10 dark:border-white/10 hover:border-amber-500/50 text-slate-800 dark:text-slate-100 transition flex items-center gap-1.5 font-medium shadow-sm">
                    <span class="text-amber-500">📖</span>
                    <span>Koleksiyonum</span>
                </a>

                <!-- 🌓 Apple iPhone Tarzı Beyaz / Dark Tema Geçiş Anahtarı (Segmented Toggle) -->
                <div class="pl-1">
                    <button 
                        @click="toggleTheme()"
                        type="button"
                        title="Temayı Değiştir (Beyaz / Dark)"
                        class="relative inline-flex items-center h-8 p-1 rounded-full bg-black/5 dark:bg-white/10 border border-black/10 dark:border-white/15 backdrop-blur-xl transition duration-300 focus:outline-none"
                    >
                        <div 
                            class="absolute top-1 bottom-1 w-6 rounded-full bg-white dark:bg-slate-800 shadow-sm transition-all duration-300 ease-out border border-black/[0.05] dark:border-white/[0.15]"
                            :class="currentTheme === 'dark' ? 'left-8' : 'left-1'"
                        ></div>
                        <span class="relative z-10 w-6 h-6 flex items-center justify-center text-xs transition duration-200" :class="currentTheme === 'light' ? 'opacity-100 scale-100' : 'opacity-40 scale-90'">
                            ☀️
                        </span>
                        <span class="relative z-10 w-6 h-6 flex items-center justify-center text-xs transition duration-200" :class="currentTheme === 'dark' ? 'opacity-100 scale-100' : 'opacity-40 scale-90'">
                            🌙
                        </span>
                    </button>
                </div>

                <!-- Dil & Para Birimi Seçicileri -->
                <div class="flex items-center gap-1.5 pl-1">
                    <div class="relative group">
                        <button class="flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-black/5 dark:bg-white/10 text-slate-800 dark:text-slate-200 border border-black/5 dark:border-white/10">
                            <span><?php echo e(strtoupper(app()->getLocale())); ?></span>
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="absolute right-0 mt-1 w-28 ios-glass rounded-xl shadow-xl py-1 hidden group-hover:block z-50">
                            <a href="<?php echo e(route('locale.switch', 'tr')); ?>" class="block px-3 py-1.5 text-xs hover:bg-black/5 dark:hover:bg-white/10 text-slate-800 dark:text-slate-200 <?php echo e(app()->getLocale() === 'tr' ? 'text-amber-600 font-bold' : ''); ?>">🇹🇷 Türkçe</a>
                            <a href="<?php echo e(route('locale.switch', 'en')); ?>" class="block px-3 py-1.5 text-xs hover:bg-black/5 dark:hover:bg-white/10 text-slate-800 dark:text-slate-200 <?php echo e(app()->getLocale() === 'en' ? 'text-amber-600 font-bold' : ''); ?>">🇬🇧 English</a>
                            <a href="<?php echo e(route('locale.switch', 'de')); ?>" class="block px-3 py-1.5 text-xs hover:bg-black/5 dark:hover:bg-white/10 text-slate-800 dark:text-slate-200 <?php echo e(app()->getLocale() === 'de' ? 'text-amber-600 font-bold' : ''); ?>">🇩🇪 Deutsch</a>
                        </div>
                    </div>

                    <div class="relative group">
                        <button class="flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold rounded-lg bg-amber-500/15 text-amber-700 dark:text-amber-300 border border-amber-500/30">
                            <span><?php echo e(session('user_currency', 'USD')); ?></span>
                            <svg class="w-3 h-3 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div class="absolute right-0 mt-1 w-28 ios-glass rounded-xl shadow-xl py-1 hidden group-hover:block z-50">
                            <a href="<?php echo e(route('currency.switch', 'USD')); ?>" class="block px-3 py-1.5 text-xs hover:bg-black/5 dark:hover:bg-white/10 text-slate-800 dark:text-slate-200 <?php echo e(session('user_currency', 'USD') === 'USD' ? 'text-amber-600 font-bold' : ''); ?>">$ USD</a>
                            <a href="<?php echo e(route('currency.switch', 'EUR')); ?>" class="block px-3 py-1.5 text-xs hover:bg-black/5 dark:hover:bg-white/10 text-slate-800 dark:text-slate-200 <?php echo e(session('user_currency', 'EUR') === 'EUR' ? 'text-amber-600 font-bold' : ''); ?>">€ EUR</a>
                            <a href="<?php echo e(route('currency.switch', 'TRY')); ?>" class="block px-3 py-1.5 text-xs hover:bg-black/5 dark:hover:bg-white/10 text-slate-800 dark:text-slate-200 <?php echo e(session('user_currency', 'TRY') === 'TRY' ? 'text-amber-600 font-bold' : ''); ?>">₺ TRY</a>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Mobil Kontroller: Tema Butonu & Hamburger -->
            <div class="flex items-center gap-2 md:hidden">
                <!-- Mobil Tema Geçiş Butonu -->
                <button 
                    @click="toggleTheme()"
                    class="p-2 rounded-xl bg-black/5 dark:bg-white/10 border border-black/10 dark:border-white/10 text-slate-700 dark:text-slate-300 focus:outline-none"
                    aria-label="Tema Değiştir"
                >
                    <span x-show="currentTheme === 'light'">🌙</span>
                    <span x-show="currentTheme === 'dark'" x-cloak>☀️</span>
                </button>

                <!-- Mobil Menü Aç/Kapat -->
                <button 
                    @click="mobileMenuOpen = !mobileMenuOpen" 
                    class="p-2 rounded-xl bg-black/5 dark:bg-white/10 border border-black/10 dark:border-white/10 text-slate-700 dark:text-slate-300 focus:outline-none"
                    aria-label="Menü"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path x-show="mobileMenuOpen" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobil Açılır Menü (iOS Drawer) -->
        <div 
            x-show="mobileMenuOpen" 
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 -translate-y-2 scale-98"
            class="md:hidden mt-2 ios-glass rounded-2xl p-4 space-y-3 shadow-2xl"
        >
            <form action="<?php echo e(route('home')); ?>" method="GET" class="w-full relative mb-2">
                <input 
                    type="text" 
                    name="q" 
                    placeholder="Sikke veya lejant ara..." 
                    class="w-full bg-black/5 dark:bg-white/10 border border-black/10 dark:border-white/15 rounded-xl px-4 py-2.5 pl-10 text-xs text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:border-amber-500"
                >
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </form>

            <div class="space-y-1">
                <a href="<?php echo e(route('home')); ?>" class="block px-3 py-2 rounded-xl text-sm font-medium hover:bg-black/5 dark:hover:bg-white/10 text-slate-800 dark:text-slate-200">🏛️ Katalog Ana Sayfa</a>
                <a href="<?php echo e(route('periods.index')); ?>" class="block px-3 py-2 rounded-xl text-sm font-medium hover:bg-black/5 dark:hover:bg-white/10 text-slate-800 dark:text-slate-200">👑 Tarihsel Dönemler</a>
                <a href="<?php echo e(route('epigraphy.index')); ?>" class="block px-3 py-2 rounded-xl text-sm font-medium hover:bg-black/5 dark:hover:bg-white/10 text-slate-800 dark:text-slate-200">🔍 Epigrafik Lejant Çözücü</a>
                <a href="<?php echo e(route('quiz.index')); ?>" class="block px-3 py-2 rounded-xl text-sm font-semibold hover:bg-amber-500/10 text-amber-600 dark:text-gold-antique">🎯 Günün Sikkesi Yarışması</a>
                <a href="<?php echo e(route('map.index')); ?>" class="block px-3 py-2 rounded-xl text-sm font-medium hover:bg-black/5 dark:hover:bg-white/10 text-slate-800 dark:text-slate-200">🗺️ Antik Darphane Haritası</a>
                <a href="<?php echo e(route('collection.index')); ?>" class="block px-3 py-2 rounded-xl text-sm font-medium bg-amber-500/15 border border-amber-500/30 text-amber-700 dark:text-gold-antique">📖 Kişisel Koleksiyonum</a>
            </div>

            <div class="pt-3 border-t border-black/10 dark:border-white/10 flex items-center justify-between">
                <span class="text-xs text-slate-500">Dil / Para Birimi:</span>
                <div class="flex items-center gap-1.5">
                    <a href="<?php echo e(route('locale.switch', 'tr')); ?>" class="px-2 py-1 text-xs rounded-lg bg-black/5 dark:bg-white/10 border border-black/5 dark:border-white/10">TR</a>
                    <a href="<?php echo e(route('locale.switch', 'en')); ?>" class="px-2 py-1 text-xs rounded-lg bg-black/5 dark:bg-white/10 border border-black/5 dark:border-white/10">EN</a>
                    <a href="<?php echo e(route('currency.switch', 'USD')); ?>" class="px-2 py-1 text-xs rounded-lg bg-amber-500/20 text-amber-700 dark:text-gold-antique font-bold">USD</a>
                    <a href="<?php echo e(route('currency.switch', 'TRY')); ?>" class="px-2 py-1 text-xs rounded-lg bg-black/5 dark:bg-white/10 border border-black/5 dark:border-white/10">TRY</a>
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

    <!-- 5. Alt Menü (Footer) - iOS Frosted Glass & Kurumsal Bilgiler -->
    <footer class="mt-20 border-t border-black/[0.08] dark:border-white/[0.08] text-xs py-14 ios-glass">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8">
            
            <!-- Sütun 1: Müze & Proje Tanımı -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-amber-600 dark:text-gold-antique font-numis font-bold text-base">🏛️</span>
                    <h3 class="font-numis font-bold text-slate-900 dark:text-slate-100 text-sm tracking-wider">
                        NÜMİZMATİK ANSİKLOPEDİSİ
                    </h3>
                </div>
                <p class="leading-relaxed text-slate-600 dark:text-slate-400 text-xs mb-4">
                    Antik Grek, Roma İmparatorluğu, Bizans ve İslami medeniyetlere ait sikkelerin tipolojik tasnifi, lejant çözümlemeleri ve akademik nümizmatik referans kütüphanesidir.
                </p>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-black/5 dark:bg-white/10 border border-black/10 dark:border-white/10 text-[11px] text-slate-700 dark:text-slate-300 font-medium shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Açık Akademik Arşiv</span>
                </div>
            </div>

            <!-- Sütun 2: Hiyerarşik Dönem Bağlantıları -->
            <div>
                <h4 class="font-semibold text-slate-900 dark:text-slate-100 text-sm mb-3 uppercase tracking-wider font-numis">Dönemler</h4>
                <ul class="space-y-2 text-xs text-slate-600 dark:text-slate-400">
                    <li><a href="<?php echo e(route('home', ['period' => 'roma-imparatorlugu'])); ?>" class="hover:text-amber-600 dark:hover:text-gold-antique transition">Roma İmparatorluğu (M.Ö. 27 - M.S. 476)</a></li>
                    <li><a href="<?php echo e(route('home', ['period' => 'bizans-imparatorlugu'])); ?>" class="hover:text-amber-600 dark:hover:text-gold-antique transition">Bizans İmparatorluğu (M.S. 491 - 1453)</a></li>
                    <li><a href="<?php echo e(route('home', ['period' => 'klasik-hellenistik-grek'])); ?>" class="hover:text-amber-600 dark:hover:text-gold-antique transition">Klasik & Hellenistik Grek</a></li>
                    <li><a href="<?php echo e(route('home', ['period' => 'osmanli-imparatorlugu'])); ?>" class="hover:text-amber-600 dark:hover:text-gold-antique transition">Osmanlı & İslami Sikkeler</a></li>
                    <li><a href="<?php echo e(route('periods.index')); ?>" class="text-amber-600 dark:text-gold-antique hover:underline transition font-semibold">Tüm Dönem Kataloğu &rarr;</a></li>
                </ul>
            </div>

            <!-- Sütun 3: Akademik Kaynaklar & Linked Open Data -->
            <div>
                <h4 class="font-semibold text-slate-900 dark:text-slate-100 text-sm mb-3 uppercase tracking-wider font-numis">Akademik Standartlar</h4>
                <div class="flex flex-wrap gap-1.5 text-[11px] font-mono text-slate-700 dark:text-slate-300 mb-3">
                    <span class="bg-black/5 dark:bg-white/10 border border-black/10 dark:border-white/10 px-2.5 py-1 rounded-lg">RIC (Roman Imperial)</span>
                    <span class="bg-black/5 dark:bg-white/10 border border-black/10 dark:border-white/10 px-2.5 py-1 rounded-lg">RPC (Provincial)</span>
                    <span class="bg-black/5 dark:bg-white/10 border border-black/10 dark:border-white/10 px-2.5 py-1 rounded-lg">BMC (British Museum)</span>
                    <span class="bg-black/5 dark:bg-white/10 border border-black/10 dark:border-white/10 px-2.5 py-1 rounded-lg">Nomisma.org LOD</span>
                    <span class="bg-black/5 dark:bg-white/10 border border-black/10 dark:border-white/10 px-2.5 py-1 rounded-lg">ANS (Numismatics.org)</span>
                </div>
                <p class="text-[11px] text-slate-500 leading-normal">
                    Tüm sikke detay sayfaları IIIF Image API 3.0 ve Nomisma.org ontolojisine uygun JSON-LD formatında sunulmaktadır.
                </p>
            </div>

            <!-- Sütun 4: 2863 Sayılı Kanun & Hukuki Sorumluluk Reddi -->
            <div>
                <h4 class="font-semibold text-slate-900 dark:text-slate-100 text-sm mb-3 uppercase tracking-wider font-numis">Yasal Uyarı</h4>
                <p class="leading-relaxed text-slate-600 dark:text-slate-400 text-[11px] mb-3">
                    Sitemizde yer alan tüm sikke verileri açık akademik arşivler ve kamuya açık uluslararası müzayede veritabanlarından derlenmiştir.
                </p>
                <div class="p-3 bg-amber-500/10 border border-amber-500/30 rounded-2xl text-[11px] text-slate-700 dark:text-slate-300 shadow-sm">
                    ⚠️ <strong>2863 Sayılı Kanun Uyarınca:</strong> Tarihi eserlerin ticareti veya aracılığı kesinlikle yasaktır ve sitemizde yapılmamaktadır.
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 pt-6 border-t border-black/[0.08] dark:border-white/[0.08] text-center text-slate-500 text-[11px]">
            &copy; <?php echo e(date('Y')); ?> Antik Sikke Bilgi ve Referans Kataloğu. Apple iOS Kurumsal Cam Tasarımı. Tüm hakları saklıdır.
        </div>
    </footer>

    <!-- Otomatik Hukuki Aydınlatma ve Onay Modalı -->
    <?php echo $__env->make('components.legal-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Çevrimdışı / Çevrimiçi Ağ Durum Bildirimi (PWA) -->
    <div id="pwa-offline-pill" class="fixed bottom-4 left-4 z-50 ios-glass text-amber-600 dark:text-gold-antique border border-amber-500/40 text-xs px-4 py-2 rounded-full shadow-2xl hidden items-center gap-2 font-mono">
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
                toast.className = 'fixed bottom-6 right-6 z-50 ios-glass border border-gold-antique/50 text-slate-800 dark:text-gold-light text-xs px-4 py-3 rounded-2xl shadow-2xl flex items-center gap-2.5 font-medium transition duration-300 transform translate-y-4 opacity-0 pointer-events-none';
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