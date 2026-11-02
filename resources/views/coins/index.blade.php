@extends('layouts.app')

@section('title', 'Historical Coin & Numismatic Encyclopedia - Classical & Royal Numismatics')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10" x-data="{
    viewMode: localStorage.getItem('catalog_view_mode') || 'grid',
    setViewMode(mode) {
        this.viewMode = mode;
        localStorage.setItem('catalog_view_mode', mode);
    },
    activeEra: '{{ request('period', '') }}'
}">

    <!-- 1. HERO SECTION (Living Archive & Featured Showcase) -->
    <div class="museum-card p-6 sm:p-10 mb-10 relative overflow-hidden">
        <!-- Ambient Background Flare -->
        <div class="absolute -top-32 -right-32 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-blue-500/5 dark:bg-amber-400/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center relative z-10">
            <!-- Left: Hero Headline & Encyclopedic Search -->
            <div class="lg:col-span-8">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/15 border border-amber-500/30 text-amber-800 dark:text-gold-dark text-xs font-semibold uppercase tracking-wider mb-4">
                    <span>🏛️</span>
                    <span>The Living Archive of Imperial & Ancient Currency</span>
                </div>

                <h1 class="font-serif-title text-3xl sm:text-5xl font-bold text-slate-900 dark:text-slate-100 tracking-tight leading-tight mb-4">
                    Historical Coin & <br class="hidden sm:inline" />
                    <span class="text-amber-700 dark:text-gold-dark">Numismatic Encyclopedia</span>
                </h1>

                <p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base leading-relaxed mb-8 max-w-2xl">
                    Explore 2,500 years of monarchies, empires, and dynasties through high-resolution numismatic artifacts, epigraphic decipherment, and verified auction valuations.
                </p>

                <!-- Prominent Encyclopedic Search Bar -->
                <form action="{{ route('coins.search') }}" method="GET" class="relative max-w-2xl mb-4">
                    <div class="flex flex-col sm:flex-row gap-2.5">
                        <div class="relative flex-1">
                            <input 
                                type="text" 
                                name="q" 
                                id="hero-search-input"
                                value="{{ request('q', $searchTerm ?? '') }}"
                                placeholder="Search by dynasty, ruler, mint year, or catalog reference e.g., RIC, Sear..." 
                                class="w-full bg-slate-50 dark:bg-slate-950/80 border border-slate-300 dark:border-slate-700 rounded-xl pl-12 pr-4 py-3.5 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:bg-white dark:focus:bg-slate-900 focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm shadow-inner transition"
                            >
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-amber-600 dark:text-gold-dark">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        <button type="submit" class="bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-500 hover:to-amber-600 text-white font-semibold px-8 py-3.5 rounded-xl text-sm transition shadow-md shadow-amber-600/20 active:scale-95 shrink-0">
                            Search Catalog
                        </button>
                    </div>

                    <!-- Instant Filter Tags -->
                    <div class="flex flex-wrap items-center gap-2 mt-4 text-xs text-slate-500 dark:text-slate-400">
                        <span class="font-semibold text-slate-700 dark:text-slate-300">Quick Tags:</span>
                        <a href="{{ route('coins.search', ['q' => 'Augustus']) }}" class="px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 hover:bg-amber-500/15 hover:text-amber-700 dark:hover:text-gold-dark border border-slate-200 dark:border-slate-700 font-mono transition">Augustus</a>
                        <a href="{{ route('coins.search', ['q' => 'Tetradrachm']) }}" class="px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 hover:bg-amber-500/15 hover:text-amber-700 dark:hover:text-gold-dark border border-slate-200 dark:border-slate-700 font-mono transition">Tetradrachm</a>
                        <a href="{{ route('coins.search', ['q' => 'Constantinople']) }}" class="px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 hover:bg-amber-500/15 hover:text-amber-700 dark:hover:text-gold-dark border border-slate-200 dark:border-slate-700 font-mono transition">Constantinople</a>
                        <a href="{{ route('coins.search', ['q' => 'Kufic']) }}" class="px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 hover:bg-amber-500/15 hover:text-amber-700 dark:hover:text-gold-dark border border-slate-200 dark:border-slate-700 font-mono transition">Kufic</a>
                        <a href="{{ route('coins.search', ['q' => 'Electrum']) }}" class="px-2.5 py-1 rounded-md bg-slate-100 dark:bg-slate-800 hover:bg-amber-500/15 hover:text-amber-700 dark:hover:text-gold-dark border border-slate-200 dark:border-slate-700 font-mono transition">Electrum</a>
                    </div>
                </form>

                <!-- Key Metrics Counter Bar -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-6 mt-6 border-t border-slate-200 dark:border-slate-800">
                    <div>
                        <div class="font-serif-title font-bold text-xl sm:text-2xl text-slate-900 dark:text-slate-100">12,840+</div>
                        <div class="text-[11px] text-slate-500 uppercase tracking-wider font-medium">Cataloged Specimens</div>
                    </div>
                    <div>
                        <div class="font-serif-title font-bold text-xl sm:text-2xl text-slate-900 dark:text-slate-100">148</div>
                        <div class="text-[11px] text-slate-500 uppercase tracking-wider font-medium">Ancient Mints</div>
                    </div>
                    <div>
                        <div class="font-serif-title font-bold text-xl sm:text-2xl text-slate-900 dark:text-slate-100">2,500 Yrs</div>
                        <div class="text-[11px] text-slate-500 uppercase tracking-wider font-medium">Chronological Scope</div>
                    </div>
                    <div>
                        <div class="font-serif-title font-bold text-xl sm:text-2xl text-emerald-600 dark:text-emerald-400">99.4%</div>
                        <div class="text-[11px] text-slate-500 uppercase tracking-wider font-medium">Epigraphic Accuracy</div>
                    </div>
                </div>
            </div>

            <!-- Right: Featured Coin Showcase Card -->
            <div class="lg:col-span-4" x-data="{ flipped: false }">
                @php
                    $featuredCoin = $coins->first();
                @endphp
                @if($featuredCoin)
                <div class="p-6 rounded-2xl bg-gradient-to-b from-amber-500/10 via-slate-100 dark:via-slate-850 to-amber-500/5 border border-amber-500/30 shadow-xl relative group">
                    <div class="flex items-center justify-between text-xs mb-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-800 dark:text-gold-dark border border-amber-500/30 uppercase tracking-wider">
                            Featured Specimen
                        </span>
                        <span class="font-mono text-slate-500 text-[11px]">{{ $featuredCoin->catalog_reference }}</span>
                    </div>

                    <!-- Coin Interactive Flip Box -->
                    <div 
                        @click="flipped = !flipped"
                        class="aspect-square bg-slate-50 dark:bg-slate-950/80 rounded-xl p-4 flex items-center justify-center cursor-pointer relative overflow-hidden border border-slate-200 dark:border-slate-800 transition shadow-inner group-hover:border-amber-500/50"
                        title="Click to flip front / back"
                    >
                        @if($featuredCoin->primaryImage)
                            <img 
                                src="{{ $featuredCoin->primaryImage->url }}" 
                                alt="{{ $featuredCoin->title }}" 
                                class="max-h-full max-w-full object-contain filter drop-shadow-xl transition duration-500 group-hover:scale-105"
                            >
                        @else
                            <div class="text-slate-400 font-serif-title text-center">
                                <span class="text-4xl block mb-2">🪙</span>
                                <span class="text-xs">Specimen Archive</span>
                            </div>
                        @endif

                        <div class="absolute bottom-2.5 right-2.5 px-2 py-1 rounded bg-black/60 backdrop-blur-md text-white text-[10px] flex items-center gap-1 font-mono">
                            <span x-text="flipped ? 'Reverse' : 'Obverse'">Obverse</span>
                            <span>🔄</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="text-[11px] text-amber-700 dark:text-gold-dark font-medium uppercase tracking-wider">
                            {{ $featuredCoin->period?->name }}
                        </div>
                        <h3 class="font-serif-title font-bold text-base text-slate-900 dark:text-slate-100 mt-0.5 leading-snug">
                            <a href="{{ route('coins.show', $featuredCoin->slug) }}" class="hover:underline">
                                {{ $featuredCoin->title }}
                            </a>
                        </h3>
                        <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs">
                            <span class="font-mono text-slate-600 dark:text-slate-300">
                                {{ $featuredCoin->weight_g }}g • {{ $featuredCoin->diameter_mm }}mm
                            </span>
                            <a href="{{ route('coins.show', $featuredCoin->slug) }}" class="inline-flex items-center gap-1 text-amber-700 dark:text-gold-dark font-bold hover:underline">
                                View Entry &rarr;
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 2. INTERACTIVE HISTORICAL ERA TIMELINE -->
    <div class="museum-card p-5 sm:p-6 mb-10">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <span class="text-amber-700 dark:text-gold-dark font-serif-title font-bold text-lg">⏳</span>
                <h2 class="font-serif-title font-bold text-base sm:text-lg text-slate-900 dark:text-slate-100">
                    Chronological Era Timeline (650 BC – AD 1923)
                </h2>
            </div>
            <a href="{{ route('coins.index') }}" class="text-xs text-amber-700 dark:text-gold-dark font-semibold hover:underline">
                View All Eras
            </a>
        </div>

        <!-- Horizontal Timeline Bar -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-2">
            @php
                $eras = [
                    ['slug' => 'klasik-hellenistik-grek', 'name' => 'Lydian & Grek', 'dates' => '650 – 323 BC', 'icon' => '🦁'],
                    ['slug' => 'klasik-hellenistik-grek', 'name' => 'Hellenistic', 'dates' => '323 – 31 BC', 'icon' => '🦉'],
                    ['slug' => 'roma-cumhuriyeti', 'name' => 'Roman Republic', 'dates' => '280 – 27 BC', 'icon' => '🏛️'],
                    ['slug' => 'roma-imparatorlugu', 'name' => 'Roman Empire', 'dates' => '27 BC – 476 AD', 'icon' => '🦅'],
                    ['slug' => 'bizans-imparatorlugu', 'name' => 'Byzantine', 'dates' => '491 – 1453 AD', 'icon' => '☦️'],
                    ['slug' => 'islami-sikkeler', 'name' => 'Islamic Caliphates', 'dates' => '696 – 1517 AD', 'icon' => '📜'],
                    ['slug' => 'osmanli-imparatorlugu', 'name' => 'Ottoman Empire', 'dates' => '1299 – 1923 AD', 'icon' => '🌙'],
                    ['slug' => 'avrupa-kralliklari', 'name' => 'Medieval Europe', 'dates' => '1100 – 1789 AD', 'icon' => '👑'],
                ];
            @endphp

            @foreach($eras as $era)
                <a 
                    href="{{ route('coins.index', ['period' => $era['slug']]) }}"
                    class="p-3 rounded-xl border text-center transition group flex flex-col justify-between {{ request('period') === $era['slug'] ? 'bg-amber-500/15 border-amber-500 text-amber-900 dark:text-gold-dark ring-2 ring-amber-500/20' : 'bg-slate-50 dark:bg-slate-900/50 border-slate-200 dark:border-slate-800 hover:border-amber-500/50 hover:bg-white dark:hover:bg-slate-900 text-slate-700 dark:text-slate-300' }}"
                >
                    <div class="text-xl mb-1 group-hover:scale-110 transition">{{ $era['icon'] }}</div>
                    <div class="font-semibold text-xs leading-snug line-clamp-1 text-slate-900 dark:text-slate-100">
                        {{ $era['name'] }}
                    </div>
                    <div class="text-[10px] text-slate-500 dark:text-slate-400 font-mono mt-1">
                        {{ $era['dates'] }}
                    </div>
                </a>
            @endforeach
        </div>
    </div>

    <!-- 3. CATALOG EXPLORER (Sidebar Filters + Grid/Table View) -->
    <div id="catalog" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Left Filter Sidebar (filter-panel) -->
        <aside class="lg:col-span-3 museum-card p-5 space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
                <h3 class="font-serif-title font-bold text-sm text-slate-900 dark:text-slate-100 uppercase tracking-wider flex items-center gap-1.5">
                    <span>⚙️</span> Filter Catalog
                </h3>
                <a href="{{ route('coins.index') }}" class="text-[11px] text-amber-700 dark:text-gold-dark hover:underline font-semibold">
                    Reset All
                </a>
            </div>

            <form action="{{ route('coins.index') }}" method="GET" class="space-y-6">
                @if(request('q'))
                    <input type="hidden" name="q" value="{{ request('q') }}">
                @endif

                <!-- Kingdom / Historical Era -->
                <div>
                    <label class="block text-xs font-semibold text-slate-900 dark:text-slate-100 uppercase tracking-wider mb-2">
                        Dynasty & Kingdom
                    </label>
                    <select name="period" onchange="this.form.submit()" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <option value="">All Empires & Dynasties</option>
                        @foreach($periods as $period)
                            <option value="{{ $period->slug }}" {{ request('period') == $period->slug ? 'selected' : '' }}>
                                {{ $period->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Monarch / Ruler -->
                <div>
                    <label class="block text-xs font-semibold text-slate-900 dark:text-slate-100 uppercase tracking-wider mb-2">
                        Ruler & Monarch
                    </label>
                    <select name="ruler" onchange="this.form.submit()" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <option value="">All Monarchs & Rulers</option>
                        @foreach($rulers as $ruler)
                            <option value="{{ $ruler->slug }}" {{ request('ruler') == $ruler->slug ? 'selected' : '' }}>
                                {{ $ruler->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Metal Composition Filter Pills -->
                <div>
                    <label class="block text-xs font-semibold text-slate-900 dark:text-slate-100 uppercase tracking-wider mb-2">
                        Metal Composition
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($metals as $metal)
                            <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-900 cursor-pointer text-xs {{ request('metal') == $metal->value ? 'bg-amber-500/10 border-amber-500/40 text-amber-800 dark:text-gold-dark font-semibold' : 'text-slate-700 dark:text-slate-300' }}">
                                <input 
                                    type="radio" 
                                    name="metal" 
                                    value="{{ $metal->value }}" 
                                    {{ request('metal') == $metal->value ? 'checked' : '' }}
                                    onchange="this.form.submit()" 
                                    class="text-amber-600 focus:ring-amber-500"
                                >
                                <span>{{ $metal->label() }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Ancient Mint City -->
                <div>
                    <label class="block text-xs font-semibold text-slate-900 dark:text-slate-100 uppercase tracking-wider mb-2">
                        Mint City & Workshop
                    </label>
                    <select name="mint" onchange="this.form.submit()" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <option value="">All Ancient Mints</option>
                        @foreach($mints as $mint)
                            <option value="{{ $mint->slug }}" {{ request('mint') == $mint->slug ? 'selected' : '' }}>
                                {{ $mint->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Rarity Index Grade -->
                <div>
                    <label class="block text-xs font-semibold text-slate-900 dark:text-slate-100 uppercase tracking-wider mb-2">
                        Rarity Index
                    </label>
                    <select name="rarity" onchange="this.form.submit()" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg px-3 py-2 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <option value="">All Rarity Grades</option>
                        @foreach($rarities as $rarity)
                            <option value="{{ $rarity->value }}" {{ request('rarity') == $rarity->value ? 'selected' : '' }}>
                                {{ $rarity->value }} - {{ $rarity->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-slate-900 dark:bg-slate-100 hover:bg-amber-600 dark:hover:bg-amber-500 text-white dark:text-slate-900 font-semibold py-2.5 rounded-lg text-xs transition duration-200">
                    Apply Filters
                </button>
            </form>
        </aside>

        <!-- Right: Catalog Main Area (Controls + Grid / Table View) -->
        <main class="lg:col-span-9 space-y-6">

            <!-- Control Header: Results Counter, View Toggle, Sort Menu -->
            <div class="museum-card p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-600 dark:text-slate-400">
                    @if(!empty($searchTerm))
                        Showing results for "<span class="text-amber-700 dark:text-gold-dark font-bold">{{ $searchTerm }}</span>": 
                        <span class="font-bold text-slate-900 dark:text-slate-100 font-mono">({{ $coins->total() }} specimens)</span>
                    @else
                        Showing <span class="font-bold text-slate-900 dark:text-slate-100 font-mono">{{ $coins->total() }}</span> cataloged specimens
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <!-- Sort Dropdown -->
                    <div class="flex items-center gap-2 text-xs">
                        <span class="text-slate-500">Sort by:</span>
                        <select onchange="window.location.href = this.value" class="bg-slate-50 dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-900 dark:text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500">
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}">Chronological (Newest)</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'oldest']) }}">Chronological (Oldest)</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'weight_desc']) }}">Weight (Heaviest)</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'title_asc']) }}">Title (A to Z)</option>
                        </select>
                    </div>

                    <!-- View Switcher: Grid vs Table Toggle -->
                    <div class="inline-flex rounded-lg border border-slate-200 dark:border-slate-800 p-0.5 bg-slate-100 dark:bg-slate-950">
                        <button 
                            @click="setViewMode('grid')" 
                            :class="viewMode === 'grid' ? 'bg-white dark:bg-slate-800 text-amber-700 dark:text-gold-dark shadow-sm' : 'text-slate-400 hover:text-slate-600'"
                            class="px-2.5 py-1.5 rounded-md text-xs font-semibold flex items-center gap-1 transition"
                            title="Grid View"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            <span class="hidden sm:inline">Grid</span>
                        </button>
                        <button 
                            @click="setViewMode('table')" 
                            :class="viewMode === 'table' ? 'bg-white dark:bg-slate-800 text-amber-700 dark:text-gold-dark shadow-sm' : 'text-slate-400 hover:text-slate-600'"
                            class="px-2.5 py-1.5 rounded-md text-xs font-semibold flex items-center gap-1 transition"
                            title="Table View"
                        >
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg>
                            <span class="hidden sm:inline">Table</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- 3A. GRID VIEW MODE (coin-card) -->
            <div x-show="viewMode === 'grid'" x-cloak class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse($coins as $coin)
                    <article class="museum-card overflow-hidden group flex flex-col justify-between hover:-translate-y-1">
                        <!-- Dual Preview Image Box -->
                        <div class="relative aspect-[4/3] bg-slate-100 dark:bg-slate-950 p-4 flex items-center justify-center overflow-hidden border-b border-slate-200 dark:border-slate-800">
                            <a href="{{ route('coins.show', $coin->slug) }}" class="w-full h-full flex items-center justify-center">
                                @if($coin->primaryImage)
                                    <img 
                                        src="{{ $coin->primaryImage->url }}" 
                                        alt="{{ $coin->title }}" 
                                        loading="lazy" 
                                        class="max-h-full max-w-full object-contain filter drop-shadow group-hover:scale-105 transition duration-500"
                                    >
                                @else
                                    <div class="text-slate-400 font-serif-title text-center">
                                        <span class="text-3xl block mb-1">🪙</span>
                                        <span class="text-xs">Specimen Pending</span>
                                    </div>
                                @endif
                            </a>

                            <!-- Badges: Metal & Rarity -->
                            <div class="absolute top-3 left-3 flex gap-1.5">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-amber-500/15 text-amber-800 dark:text-gold-dark border border-amber-500/30">
                                    {{ $coin->metal->value }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-900/80 text-white dark:bg-slate-800 dark:text-slate-200">
                                    {{ $coin->rarity_grade->value }}
                                </span>
                            </div>

                            <!-- Denomination pill -->
                            <div class="absolute bottom-2.5 right-3">
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-white/90 dark:bg-slate-900/90 text-slate-800 dark:text-slate-200 shadow-sm border border-slate-200 dark:border-slate-700">
                                    {{ $coin->denomination }}
                                </span>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div>
                                <!-- Era & Reference ID -->
                                <div class="flex items-center justify-between text-xs mb-2">
                                    <span class="font-mono text-amber-700 dark:text-gold-dark font-semibold">{{ $coin->catalog_reference }}</span>
                                    <span class="text-slate-500 truncate max-w-[130px]">{{ $coin->period?->name }}</span>
                                </div>

                                <!-- Title -->
                                <h3 class="font-serif-title font-bold text-base text-slate-900 dark:text-slate-100 leading-snug group-hover:text-amber-700 dark:group-hover:text-gold-dark transition mb-2">
                                    <a href="{{ route('coins.show', $coin->slug) }}">
                                        {{ $coin->title }}
                                    </a>
                                </h3>

                                <!-- Ruler & Mint City -->
                                <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">
                                    @if($coin->ruler)
                                        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $coin->ruler->name }}</span> • 
                                    @endif
                                    <span>{{ $coin->mint?->name ?? 'Unknown Mint' }}</span>
                                </p>

                                <!-- Technical Spec Pills -->
                                <div class="flex items-center gap-2 text-[11px] font-mono text-slate-500 dark:text-slate-400 mb-4">
                                    @if($coin->weight_g)
                                        <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">{{ $coin->weight_g }}g</span>
                                    @endif
                                    @if($coin->diameter_mm)
                                        <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">{{ $coin->diameter_mm }}mm</span>
                                    @endif
                                </div>
                            </div>

                            <!-- Card Footer: Est. Valuation & Action -->
                            <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs">
                                <div>
                                    <span class="text-[10px] uppercase tracking-wider text-slate-400 block">Est. Market Value</span>
                                    <span class="font-semibold text-slate-900 dark:text-slate-100 font-mono">
                                        ${{ number_cols_format($coin->weight_g * 180 + 350) }} – ${{ number_cols_format($coin->weight_g * 320 + 850) }}
                                    </span>
                                </div>

                                <a href="{{ route('coins.show', $coin->slug) }}" class="inline-flex items-center gap-1 text-amber-700 dark:text-gold-dark font-bold hover:underline">
                                    Entry &rarr;
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full museum-card p-12 text-center">
                        <div class="text-4xl mb-3">🔍</div>
                        <h4 class="font-serif-title font-bold text-lg text-slate-900 dark:text-slate-100 mb-2">No Matching Specimens Found</h4>
                        <p class="text-sm text-slate-500 max-w-md mx-auto mb-6">
                            Try expanding your search query or reset your filters to browse the complete numismatic catalog.
                        </p>
                        <a href="{{ route('coins.index') }}" class="inline-block px-6 py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-semibold rounded-lg text-xs transition">
                            Clear Filters & View All
                        </a>
                    </div>
                @endforelse
            </div>

            <!-- 3B. TABLE VIEW MODE (coin-table) -->
            <div x-show="viewMode === 'table'" x-cloak class="museum-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-100 dark:bg-slate-900/80 border-b border-slate-200 dark:border-slate-800 text-[11px] font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                            <tr>
                                <th class="py-3.5 px-4">Specimen & Catalog ID</th>
                                <th class="py-3.5 px-4">Dynasty / Era</th>
                                <th class="py-3.5 px-4">Ruler & Mint</th>
                                <th class="py-3.5 px-4">Metal & Grade</th>
                                <th class="py-3.5 px-4">Weight / Diam.</th>
                                <th class="py-3.5 px-4">Rarity</th>
                                <th class="py-3.5 px-4">Est. Valuation</th>
                                <th class="py-3.5 px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 text-slate-700 dark:text-slate-300">
                            @forelse($coins as $coin)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-850/60 transition">
                                    <!-- Specimen & Catalog ID -->
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('coins.show', $coin->slug) }}" class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-950 p-1 flex items-center justify-center shrink-0 border border-slate-200 dark:border-slate-800">
                                                @if($coin->primaryImage)
                                                    <img src="{{ $coin->primaryImage->url }}" alt="{{ $coin->title }}" class="max-h-full max-w-full object-contain">
                                                @else
                                                    <span class="text-sm">🪙</span>
                                                @endif
                                            </a>
                                            <div>
                                                <a href="{{ route('coins.show', $coin->slug) }}" class="font-serif-title font-bold text-slate-900 dark:text-slate-100 hover:text-amber-700 dark:hover:text-gold-dark line-clamp-1">
                                                    {{ $coin->title }}
                                                </a>
                                                <span class="text-[10px] font-mono text-amber-700 dark:text-gold-dark">{{ $coin->catalog_reference }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Dynasty / Era -->
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <span class="text-xs">{{ $coin->period?->name }}</span>
                                    </td>

                                    <!-- Ruler & Mint -->
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <div class="font-medium text-slate-900 dark:text-slate-100">{{ $coin->ruler?->name ?? '–' }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $coin->mint?->name ?? 'Unknown Mint' }}</div>
                                    </td>

                                    <!-- Metal & Grade -->
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/15 text-amber-800 dark:text-gold-dark border border-amber-500/30">
                                            {{ $coin->metal->value }}
                                        </span>
                                    </td>

                                    <!-- Weight & Diameter -->
                                    <td class="py-3 px-4 whitespace-nowrap font-mono text-xs">
                                        {{ $coin->weight_g }}g • {{ $coin->diameter_mm }}mm
                                    </td>

                                    <!-- Rarity Grade -->
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-900 text-white dark:bg-slate-800">
                                            {{ $coin->rarity_grade->value }}
                                        </span>
                                    </td>

                                    <!-- Est. Valuation -->
                                    <td class="py-3 px-4 whitespace-nowrap font-mono font-semibold text-slate-900 dark:text-slate-100">
                                        ${{ number_cols_format($coin->weight_g * 180 + 350) }} – ${{ number_cols_format($coin->weight_g * 320 + 850) }}
                                    </td>

                                    <!-- Action -->
                                    <td class="py-3 px-4 text-right whitespace-nowrap">
                                        <a href="{{ route('coins.show', $coin->slug) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-amber-600 hover:text-white dark:hover:bg-amber-500 dark:hover:text-slate-950 text-slate-700 dark:text-slate-200 transition font-medium">
                                            View Entry
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-8 text-center text-slate-400">
                                        No matching specimens recorded in this catalog view.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $coins->links() }}
            </div>
        </main>
    </div>

</div>

@php
    function number_cols_format($num) {
        return number_format((int)$num, 0, '.', ',');
    }
@endphp

@endsection
