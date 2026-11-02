@extends('layouts.app')

@section('title', 'Antik Sikke Kataloğu - Nümizmatik Bilgi ve Referans Arşivi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">

    <!-- 🍎 Apple Hero & Arama Kartı (Cam Efekti) -->
    <div class="ios-glass-card p-6 sm:p-10 mb-8 sm:mb-10 relative overflow-hidden">
        <!-- iOS Ambient Refraction Accents -->
        <div class="absolute -top-24 -right-24 w-72 h-72 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-blue-500/5 dark:bg-amber-400/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-3xl relative z-10">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/15 border border-amber-500/30 text-amber-700 dark:text-gold-antique text-xs font-semibold uppercase tracking-wider mb-3">
                <span>🏛️</span>
                <span>Akademik Nümizmatik Ansiklopedisi</span>
            </div>

            <h1 class="font-numis text-2xl sm:text-4xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight mb-3">
                Antik Sikke Kataloğu
            </h1>
            <p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base leading-relaxed mb-6 font-normal">
                Epigrafik lejant çözümlemeleri, mikroskobik darp özellikleri, nadirlik dereceleri ve küresel müzayede değerleme referanslarıyla antik medeniyetlerin nümizmatik arşivi.
            </p>

            <!-- Apple Spotlight Tarzı Arama Alanı -->
            <form action="{{ route('coins.search') }}" method="GET" class="relative">
                <div class="flex flex-col sm:flex-row gap-2.5">
                    <div class="relative flex-1">
                        <input type="text" 
                               name="q" 
                               value="{{ request('q', $searchTerm ?? '') }}"
                               placeholder="Lejant, İmparator veya RIC no girin (örn: AVG, TRAIAN, Denarius)..." 
                               class="w-full bg-white/70 dark:bg-black/30 border border-black/10 dark:border-white/15 rounded-2xl pl-12 pr-4 py-3.5 text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:bg-white dark:focus:bg-black/60 focus:ring-2 focus:ring-amber-500/30 text-sm shadow-inner transition">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-amber-600 dark:text-gold-antique">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <button type="submit" class="bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold px-7 py-3.5 rounded-2xl text-sm transition shadow-lg shadow-amber-500/25 active:scale-95 shrink-0">
                        Katalogda Ara
                    </button>
                </div>

                <!-- Hızlı Arama Kapsülleri (iOS Tags) -->
                <div class="flex flex-wrap items-center gap-2 mt-4 text-xs text-slate-500 dark:text-slate-400">
                    <span class="font-medium text-slate-700 dark:text-slate-300">Popüler Aramalar:</span>
                    <a href="{{ route('coins.search', ['q' => 'AVG']) }}" class="px-2.5 py-1 rounded-full bg-black/5 dark:bg-white/10 hover:bg-amber-500/15 hover:text-amber-600 dark:hover:text-gold-antique transition font-mono">AVG</a>
                    <a href="{{ route('coins.search', ['q' => 'TRAIAN']) }}" class="px-2.5 py-1 rounded-full bg-black/5 dark:bg-white/10 hover:bg-amber-500/15 hover:text-amber-600 dark:hover:text-gold-antique transition font-mono">TRAIAN</a>
                    <a href="{{ route('coins.search', ['q' => 'CAESAR']) }}" class="px-2.5 py-1 rounded-full bg-black/5 dark:bg-white/10 hover:bg-amber-500/15 hover:text-amber-600 dark:hover:text-gold-antique transition font-mono">CAESAR</a>
                    <a href="{{ route('coins.search', ['q' => 'RIC']) }}" class="px-2.5 py-1 rounded-full bg-black/5 dark:bg-white/10 hover:bg-amber-500/15 hover:text-amber-600 dark:hover:text-gold-antique transition font-mono">RIC</a>
                    <a href="{{ route('coins.search', ['q' => 'Denarius']) }}" class="px-2.5 py-1 rounded-full bg-black/5 dark:bg-white/10 hover:bg-amber-500/15 hover:text-amber-600 dark:hover:text-gold-antique transition font-mono">Denarius</a>
                </div>
            </form>
        </div>
    </div>

    <!-- 🎛️ Apple Tarzı Filtre Kontrol Çubuğu (Cam Efekti) -->
    <div class="ios-glass rounded-2xl sm:rounded-3xl p-4 sm:p-5 mb-8 shadow-sm">
        <form action="{{ route('coins.index') }}" method="GET" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @if(request('q'))
                <input type="hidden" name="q" value="{{ request('q') }}">
            @endif

            <!-- Dönem -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Dönem</label>
                <select name="period" onchange="this.form.submit()" class="w-full rounded-xl px-3 py-2 text-xs border focus:outline-none focus:ring-2 focus:ring-amber-500/30 transition">
                    <option value="">Tüm Dönemler</option>
                    @foreach($periods as $period)
                        <option value="{{ $period->slug }}" {{ request('period') == $period->slug ? 'selected' : '' }}>
                            {{ $period->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Hükümdar -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Hükümdar</label>
                <select name="ruler" onchange="this.form.submit()" class="w-full rounded-xl px-3 py-2 text-xs border focus:outline-none focus:ring-2 focus:ring-amber-500/30 transition">
                    <option value="">Tümü</option>
                    @foreach($rulers as $ruler)
                        <option value="{{ $ruler->slug }}" {{ request('ruler') == $ruler->slug ? 'selected' : '' }}>
                            {{ $ruler->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Maden -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Maden</label>
                <select name="metal" onchange="this.form.submit()" class="w-full rounded-xl px-3 py-2 text-xs border focus:outline-none focus:ring-2 focus:ring-amber-500/30 transition">
                    <option value="">Tümü</option>
                    @foreach($metals as $metal)
                        <option value="{{ $metal->value }}" {{ request('metal') == $metal->value ? 'selected' : '' }}>
                            {{ $metal->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Darphane -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Darphane</label>
                <select name="mint" onchange="this.form.submit()" class="w-full rounded-xl px-3 py-2 text-xs border focus:outline-none focus:ring-2 focus:ring-amber-500/30 transition">
                    <option value="">Tümü</option>
                    @foreach($mints as $mint)
                        <option value="{{ $mint->slug }}" {{ request('mint') == $mint->slug ? 'selected' : '' }}>
                            {{ $mint->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Nadirlik -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Nadirlik</label>
                <select name="rarity" onchange="this.form.submit()" class="w-full rounded-xl px-3 py-2 text-xs border focus:outline-none focus:ring-2 focus:ring-amber-500/30 transition">
                    <option value="">Tümü</option>
                    @foreach($rarities as $rarity)
                        <option value="{{ $rarity->value }}" {{ request('rarity') == $rarity->value ? 'selected' : '' }}>
                            {{ $rarity->value }} - {{ $rarity->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Sıfırla / Filtre Temizle -->
            <div class="flex items-end">
                <a href="{{ route('coins.index') }}" class="w-full bg-black/5 dark:bg-white/10 hover:bg-black/10 dark:hover:bg-white/20 text-slate-700 dark:text-slate-300 text-xs py-2 px-3 rounded-xl text-center transition font-semibold block">
                    Filtreleri Temizle
                </a>
            </div>
        </form>
    </div>

    <!-- Sonuç Başlığı / İstatistik -->
    <div class="flex items-center justify-between mb-6 px-1">
        <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-300">
            @if(!empty($searchTerm))
                "<span class="text-amber-600 dark:text-gold-antique font-bold">{{ $searchTerm }}</span>" için bulunan sonuçlar: <span class="text-slate-900 dark:text-slate-100 font-mono font-bold">({{ $coins->total() }})</span>
            @else
                Katalog Kayıtları <span class="text-slate-500 font-mono">({{ $coins->total() }})</span>
            @endif
        </h2>
    </div>

    <!-- 🪙 Apple iOS Sikke Kartları Izgarası (Grid) -->
    @if($coins->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($coins as $coin)
                <article class="ios-glass-card overflow-hidden group flex flex-col hover:-translate-y-1">
                    <!-- Görsel Alanı -->
                    <a href="{{ route('coins.show', $coin->slug) }}" class="relative aspect-[4/3] bg-black/[0.02] dark:bg-black/30 flex items-center justify-center p-4 overflow-hidden border-b border-black/[0.06] dark:border-white/[0.08]">
                        @if($coin->primaryImage)
                            <img src="{{ $coin->primaryImage->url }}" 
                                 alt="{{ $coin->title }}" 
                                 loading="lazy" 
                                 class="max-h-full max-w-full object-contain group-hover:scale-105 transition duration-500 drop-shadow-md">
                        @else
                            <div class="text-slate-400 flex flex-col items-center">
                                <svg class="w-12 h-12 mb-2 text-slate-400/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9" stroke-width="2"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v5l3 3" />
                                </svg>
                                <span class="text-xs font-mono">Görsel Bekleniyor</span>
                            </div>
                        @endif

                        <!-- Metal & Nadirlik Rozeti (iOS Glass Badge) -->
                        <div class="absolute top-3 left-3 flex gap-1.5">
                            <span class="ios-glass font-bold text-[10px] uppercase px-2.5 py-0.5 rounded-full text-amber-700 dark:text-amber-300 shadow-sm">
                                {{ $coin->metal->value }}
                            </span>
                            <span class="bg-amber-500/20 backdrop-blur-md border border-amber-500/40 text-amber-800 dark:text-amber-300 font-mono text-[10px] font-bold px-2.5 py-0.5 rounded-full shadow-sm">
                                {{ $coin->rarity_grade->value }}
                            </span>
                        </div>

                        <!-- Nominal Değer -->
                        <div class="absolute bottom-2.5 right-3">
                            <span class="ios-glass text-slate-800 dark:text-slate-200 text-[11px] font-medium px-2.5 py-0.5 rounded-full shadow-sm">
                                {{ $coin->denomination }}
                            </span>
                        </div>
                    </a>

                    <!-- Bilgi Alanı -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <!-- Referans & Dönem -->
                            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 mb-2">
                                <span class="font-mono text-amber-600 dark:text-gold-antique font-bold">{{ $coin->catalog_reference }}</span>
                                <span class="font-medium truncate max-w-[140px]">{{ $coin->period?->name }}</span>
                            </div>

                            <!-- Başlık -->
                            <h3 class="font-numis font-bold text-slate-900 dark:text-slate-100 text-base leading-snug group-hover:text-amber-600 dark:group-hover:text-gold-antique transition duration-200 mb-2">
                                <a href="{{ route('coins.show', $coin->slug) }}">
                                    {{ $coin->title }}
                                </a>
                            </h3>

                            <!-- Hükümdar / Darphane -->
                            <p class="text-xs text-slate-600 dark:text-slate-400 mb-3">
                                @if($coin->ruler)
                                    <span class="font-medium">{{ $coin->ruler->name }}</span> • 
                                @endif
                                <span>{{ $coin->mint?->name ?? 'Darphane Bilinmiyor' }}</span>
                            </p>

                            <!-- Lejant Özeti (Apple Snippet Box) -->
                            @if($coin->obverse_legend)
                                <div class="bg-black/[0.03] dark:bg-white/[0.04] border border-black/[0.06] dark:border-white/[0.08] rounded-xl p-2.5 text-[11px] text-slate-700 dark:text-slate-300 font-legend truncate mb-3">
                                    <span class="text-slate-400 text-[10px] block font-sans uppercase font-bold">Ön Lejant:</span>
                                    {{ $coin->obverse_legend }}
                                </div>
                            @endif
                        </div>

                        <!-- Alt Bilgi: Ağırlık / Değerleme -->
                        <div class="pt-3 border-t border-black/[0.06] dark:border-white/[0.08] flex items-center justify-between text-xs">
                            <span class="text-slate-500 dark:text-slate-400 font-mono font-medium">
                                {{ $coin->weight_g ? $coin->weight_g . ' g' : '' }}
                                {{ $coin->diameter_mm ? '• ' . $coin->diameter_mm . ' mm' : '' }}
                            </span>

                            <a href="{{ route('coins.show', $coin->slug) }}" class="text-amber-600 dark:text-gold-antique font-bold hover:underline inline-flex items-center gap-1 group-hover:translate-x-0.5 transition">
                                Detay
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <!-- Sayfalama -->
        <div class="mt-10">
            {{ $coins->links() }}
        </div>
    @else
        <!-- Boş Sonuç Durumu -->
        <div class="ios-glass-card p-12 text-center max-w-xl mx-auto my-12">
            <div class="w-16 h-16 rounded-3xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center mx-auto mb-4 text-amber-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="font-numis text-lg font-bold text-slate-900 dark:text-slate-100 mb-2">Eşleşen Sikke Bulunamadı</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">
                Arama filtrenizi genişletmeyi veya lejantın sadece bir kısmını yazmayı deneyebilirsiniz (örn: "AVG", "CAES", "RIC").
            </p>
            <a href="{{ route('coins.index') }}" class="inline-block bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-6 py-3 rounded-2xl text-xs transition shadow-lg shadow-amber-500/20">
                Tüm Kataloğu Görüntüle
            </a>
        </div>
    @endif

</div>
@endsection
