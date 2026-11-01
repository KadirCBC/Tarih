@extends('layouts.app')

@section('title', 'Antik Sikke Kataloğu - Nümizmatik Bilgi ve Referans Arşivi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Başlık & Arama Alanı -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 rounded-2xl p-6 sm:p-8 border border-slate-800 shadow-xl mb-8">
        <div class="max-w-3xl">
            <h1 class="font-numis text-2xl sm:text-3xl font-bold text-slate-100 tracking-wide mb-2">
                Antik Sikke Kataloğu
            </h1>
            <p class="text-slate-400 text-sm leading-relaxed mb-6">
                Lejant çözümlemeleri, ağırlık, çap, nadirlik derecesi ve uluslararası müzayede değerleme referanslarıyla antik dünya nümizmatik arşivi.
            </p>

            <!-- Arama Kutusu -->
            <form action="{{ route('coins.search') }}" method="GET" class="relative">
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="relative flex-1">
                        <input type="text" 
                               name="q" 
                               value="{{ request('q', $searchTerm ?? '') }}"
                               placeholder="Lejant, İmparator veya RIC no girin (örn: AVG, TRAIAN, RIC)..." 
                               class="w-full bg-slate-950/90 border border-slate-700/80 rounded-xl pl-11 pr-4 py-3 text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <button type="submit" class="bg-amber-500 hover:bg-amber-450 text-slate-950 font-semibold px-6 py-3 rounded-xl text-sm transition shadow-lg shadow-amber-500/20">
                        Katalogda Ara
                    </button>
                </div>

                <!-- Hızlı Arama İpuçları -->
                <div class="flex flex-wrap items-center gap-2 mt-3 text-xs text-slate-400">
                    <span class="text-slate-500">Hızlı Aramalar:</span>
                    <a href="{{ route('coins.search', ['q' => 'AVG']) }}" class="bg-slate-800 hover:bg-slate-700 text-amber-400/90 px-2 py-0.5 rounded transition">AVG</a>
                    <a href="{{ route('coins.search', ['q' => 'TRAIAN']) }}" class="bg-slate-800 hover:bg-slate-700 text-amber-400/90 px-2 py-0.5 rounded transition">TRAIAN</a>
                    <a href="{{ route('coins.search', ['q' => 'CAESAR']) }}" class="bg-slate-800 hover:bg-slate-700 text-amber-400/90 px-2 py-0.5 rounded transition">CAESAR</a>
                    <a href="{{ route('coins.search', ['q' => 'RIC']) }}" class="bg-slate-800 hover:bg-slate-700 text-amber-400/90 px-2 py-0.5 rounded transition">RIC</a>
                    <a href="{{ route('coins.search', ['q' => 'Denarius']) }}" class="bg-slate-800 hover:bg-slate-700 text-amber-400/90 px-2 py-0.5 rounded transition">Denarius</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Filtre Çubuğu -->
    <div class="bg-slate-900 border border-slate-800 rounded-xl p-4 mb-8">
        <form action="{{ route('coins.index') }}" method="GET" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @if(request('q'))
                <input type="hidden" name="q" value="{{ request('q') }}">
            @endif

            <!-- Dönem -->
            <div>
                <label class="block text-[11px] font-medium text-slate-400 uppercase tracking-wider mb-1">Dönem</label>
                <select name="period" onchange="this.form.submit()" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <option value="">Tümü</option>
                    @foreach($periods as $period)
                        <option value="{{ $period->slug }}" {{ request('period') == $period->slug ? 'selected' : '' }}>
                            {{ $period->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Hükümdar -->
            <div>
                <label class="block text-[11px] font-medium text-slate-400 uppercase tracking-wider mb-1">Hükümdar</label>
                <select name="ruler" onchange="this.form.submit()" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 focus:outline-none focus:ring-1 focus:ring-amber-500">
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
                <label class="block text-[11px] font-medium text-slate-400 uppercase tracking-wider mb-1">Maden</label>
                <select name="metal" onchange="this.form.submit()" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 focus:outline-none focus:ring-1 focus:ring-amber-500">
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
                <label class="block text-[11px] font-medium text-slate-400 uppercase tracking-wider mb-1">Darphane</label>
                <select name="mint" onchange="this.form.submit()" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 focus:outline-none focus:ring-1 focus:ring-amber-500">
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
                <label class="block text-[11px] font-medium text-slate-400 uppercase tracking-wider mb-1">Nadirlik</label>
                <select name="rarity" onchange="this.form.submit()" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-slate-200 focus:outline-none focus:ring-1 focus:ring-amber-500">
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
                <a href="{{ route('coins.index') }}" class="w-full bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs py-1.5 px-3 rounded-lg text-center transition block">
                    Filtreleri Temizle
                </a>
            </div>
        </form>
    </div>

    <!-- Sonuç Başlığı / İstatistik -->
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-sm font-semibold text-slate-300">
            @if(!empty($searchTerm))
                "<span class="text-amber-400">{{ $searchTerm }}</span>" için bulunan sonuçlar: <span class="text-slate-100 font-mono">({{ $coins->total() }})</span>
            @else
                Katalog Kayıtları <span class="text-slate-500 font-mono">({{ $coins->total() }})</span>
            @endif
        </h2>
    </div>

    <!-- Sikke Kartları Izgarası (Grid) -->
    @if($coins->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($coins as $coin)
                <article class="bg-slate-900 border border-slate-800 hover:border-amber-500/50 rounded-2xl overflow-hidden shadow-lg transition duration-200 group flex flex-col">
                    <!-- Görsel Alanı -->
                    <a href="{{ route('coins.show', $coin->slug) }}" class="relative aspect-[4/3] bg-slate-950 flex items-center justify-center p-4 overflow-hidden border-b border-slate-800">
                        @if($coin->primaryImage)
                            <img src="{{ $coin->primaryImage->url }}" 
                                 alt="{{ $coin->title }}" 
                                 loading="lazy" 
                                 class="max-h-full max-w-full object-contain group-hover:scale-105 transition duration-300">
                        @else
                            <div class="text-slate-600 flex flex-col items-center">
                                <svg class="w-12 h-12 mb-2 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9" stroke-width="2"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v5l3 3" />
                                </svg>
                                <span class="text-xs font-mono">Görsel Bekleniyor</span>
                            </div>
                        @endif

                        <!-- Metal & Nadirlik Rozeti -->
                        <div class="absolute top-3 left-3 flex gap-1.5">
                            <span class="bg-slate-900/90 backdrop-blur-md border border-slate-700 text-amber-400 font-semibold text-[10px] uppercase px-2 py-0.5 rounded-md">
                                {{ $coin->metal->value }}
                            </span>
                            <span class="bg-amber-950/90 border border-amber-500/30 text-amber-300 font-mono text-[10px] font-bold px-2 py-0.5 rounded-md">
                                {{ $coin->rarity_grade->value }}
                            </span>
                        </div>

                        <!-- Nominal Değer -->
                        <div class="absolute bottom-2 right-3">
                            <span class="bg-slate-950/80 backdrop-blur-sm text-slate-300 text-[11px] font-medium px-2 py-0.5 rounded">
                                {{ $coin->denomination }}
                            </span>
                        </div>
                    </a>

                    <!-- Bilgi Alanı -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <!-- Referans & Dönem -->
                            <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                                <span class="font-mono text-amber-500/90 font-semibold">{{ $coin->catalog_reference }}</span>
                                <span>{{ $coin->period?->name }}</span>
                            </div>

                            <!-- Başlık -->
                            <h3 class="font-numis font-bold text-slate-100 text-base leading-snug group-hover:text-amber-400 transition mb-2">
                                <a href="{{ route('coins.show', $coin->slug) }}">
                                    {{ $coin->title }}
                                </a>
                            </h3>

                            <!-- Hükümdar / Darphane -->
                            <p class="text-xs text-slate-400 mb-3">
                                @if($coin->ruler)
                                    <span>{{ $coin->ruler->name }}</span> • 
                                @endif
                                <span>{{ $coin->mint?->name ?? 'Darphane Bilinmiyor' }}</span>
                            </p>

                            <!-- Lejant Özeti -->
                            @if($coin->obverse_legend)
                                <div class="bg-slate-950/70 border border-slate-800/80 rounded-lg p-2 text-[11px] text-slate-400 font-legend truncate mb-3">
                                    <span class="text-slate-500 text-[10px] block font-sans">Ön Lejant:</span>
                                    {{ $coin->obverse_legend }}
                                </div>
                            @endif
                        </div>

                        <!-- Alt Bilgi: Ağırlık / Değerleme -->
                        <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-xs">
                            <span class="text-slate-400 font-mono">
                                {{ $coin->weight_g ? $coin->weight_g . ' g' : '' }}
                                {{ $coin->diameter_mm ? '• ' . $coin->diameter_mm . ' mm' : '' }}
                            </span>

                            <a href="{{ route('coins.show', $coin->slug) }}" class="text-amber-400 font-semibold hover:underline inline-flex items-center gap-1">
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
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-12 text-center max-w-xl mx-auto my-12">
            <div class="w-16 h-16 rounded-full bg-slate-800 flex items-center justify-center mx-auto mb-4 text-slate-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="font-numis text-lg font-bold text-slate-200 mb-2">Eşleşen Sikke Bulunamadı</h3>
            <p class="text-sm text-slate-400 mb-6">
                Arama filtrenizi genişletmeyi veya lejantın sadece bir kısmını yazmayı deneyebilirsiniz (örn: "AVG", "CAES", "RIC").
            </p>
            <a href="{{ route('coins.index') }}" class="inline-block bg-amber-500 hover:bg-amber-450 text-slate-950 font-semibold px-5 py-2.5 rounded-xl text-xs transition">
                Tüm Kataloğu Görüntüle
            </a>
        </div>
    @endif

</div>
@endsection

