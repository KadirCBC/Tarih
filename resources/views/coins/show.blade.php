@extends('layouts.app')

@section('title', $coin->title . ' - Nümizmatik Katalog Referansı')
@section('meta_description', Str::limit(strip_tags($coin->historical_notes ?? ($coin->denomination . ' - ' . $coin->period?->name . ' dönemi sikkesi. Ön lejant: ' . $coin->obverse_legend)), 155))

@section('og_image')
    @if($coin->primaryImage)
        <meta property="og:image" content="{{ $coin->primaryImage->url }}">
    @endif
@endsection

@section('schema_org')
    <!-- JSON-LD Dinamik Schema Markup (VisualArtifact & BreadcrumbList) -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@graph": [
            {
                "@type": "VisualArtifact",
                "@id": "{{ url()->current() }}#artifact",
                "name": "{{ $coin->title }}",
                "headline": "{{ $coin->title }}",
                "description": "{{ addslashes(Str::limit(strip_tags($coin->historical_notes ?? $coin->obverse_desc), 250)) }}",
                "artform": "Coin",
                "material": "{{ $coin->metal->label() }}",
                "depth": "{{ $coin->diameter_mm }} mm",
                "weight": "{{ $coin->weight_g }} g",
                "accessMode": "visual",
                @if($coin->primaryImage)
                "image": "{{ $coin->primaryImage->url }}",
                @endif
                "temporalCoverage": "{{ $coin->period?->formatted_date_range }}",
                "creator": {
                    "@type": "Person",
                    "name": "{{ $coin->ruler?->name ?? 'Bilinmeyen Darbedici' }}"
                },
                "locationCreated": {
                    "@type": "Place",
                    "name": "{{ $coin->mint?->name ?? 'Antik Darphane' }}"
                },
                "identifier": "{{ $coin->catalog_reference }}",
                "inLanguage": "tr"
            },
            {
                "@type": "BreadcrumbList",
                "@id": "{{ url()->current() }}#breadcrumb",
                "itemListElement": [
                    {
                        "@type": "ListItem",
                        "position": 1,
                        "name": "Katalog",
                        "item": "{{ route('coins.index') }}"
                    },
                    {
                        "@type": "ListItem",
                        "position": 2,
                        "name": "{{ $coin->period?->name ?? 'Dönem' }}",
                        "item": "{{ $coin->period ? route('periods.show', $coin->period->slug) : '#' }}"
                    },
                    {
                        "@type": "ListItem",
                        "position": 3,
                        "name": "{{ $coin->title }}"
                    }
                ]
            }
        ]
    }
    </script>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Ekmek Kırıntısı (Breadcrumbs) -->
    <nav class="flex items-center text-xs text-slate-400 mb-6 space-x-2">
        <a href="{{ route('home') }}" class="hover:text-amber-400 transition">Ana Sayfa</a>
        <span>/</span>
        <a href="{{ route('coins.index') }}" class="hover:text-amber-400 transition">Katalog</a>
        <span>/</span>
        @if($coin->period)
            <a href="{{ route('periods.show', $coin->period->slug) }}" class="hover:text-amber-400 transition">{{ $coin->period->name }}</a>
            <span>/</span>
        @endif
        <span class="text-slate-200 truncate max-w-xs">{{ $coin->title }}</span>
    </nav>

    @if(session('concordance_info'))
        <div class="bg-emerald-950/80 border border-emerald-500/50 rounded-xl p-4 mb-6 shadow-lg flex items-center gap-3 text-emerald-200 text-sm">
            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span><strong>Akıllı Katalog Çözümleyici:</strong> {{ session('concordance_info') }}</span>
        </div>
    @endif

    <!-- 1. YASAL UYARI ROZETİ (2863 Sayılı Kanun Uyarısı) -->
    <div class="bg-gradient-to-r from-amber-950/80 via-slate-900 to-amber-950/80 border border-amber-500/40 rounded-xl p-4 mb-8 shadow-lg flex items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-amber-500/10 border border-amber-500/30 flex items-center justify-center shrink-0 text-amber-400">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <h4 class="text-xs font-bold text-amber-300 uppercase tracking-wider">Kültürel Miras & Akademik Referans Bildirimi</h4>
                <p class="text-xs text-slate-300">
                    Bu sayfa <strong>2863 sayılı Kültür ve Tabiat Varlıklarını Koruma Kanunu</strong> kapsamında akademik/eğitim amaçlı nümizmatik arşiv kaydıdır. <strong>Alım-satım yapılmaz.</strong>
                </p>
            </div>
        </div>
        <span class="hidden sm:inline-block bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[11px] font-mono font-semibold px-3 py-1 rounded-md shrink-0">
            Arşiv Kaydı #{{ str_pad($coin->id, 6, '0', STR_PAD_LEFT) }}
        </span>
    </div>

    <!-- Başlık & Temel Künye Bloğu -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-slate-800">
            <div>
                <!-- Rozetler -->
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="bg-amber-500/10 text-amber-400 border border-amber-500/30 text-xs font-bold px-2.5 py-0.5 rounded-md">
                        {{ $coin->metal->label() }}
                    </span>
                    <span class="bg-slate-800 text-slate-200 text-xs font-mono px-2.5 py-0.5 rounded-md">
                        {{ $coin->denomination }}
                    </span>
                    <span class="bg-purple-950/80 border border-purple-500/30 text-purple-300 font-mono text-xs font-bold px-2.5 py-0.5 rounded-md">
                        Nadirlik: {{ $coin->rarity_grade->value }} ({{ $coin->rarity_grade->label() }})
                    </span>
                </div>

                <!-- Sikke Başlığı -->
                <h1 class="font-numis text-2xl sm:text-3xl lg:text-4xl font-bold text-slate-100 tracking-wide mb-2">
                    {{ $coin->title }}
                </h1>

                <!-- Katalog Numarası -->
                <p class="text-sm text-slate-400 font-mono">
                    Standart Referans: <span class="text-amber-400 font-bold">{{ $coin->catalog_reference }}</span>
                </p>
            </div>

            <!-- Görüntülenme Sayacı -->
            <div class="text-right text-xs text-slate-400 flex lg:flex-col items-center lg:items-end justify-between gap-1">
                <span>Ziyaret Sayısı</span>
                <span class="text-xl font-bold font-mono text-slate-200">{{ number_format($coin->view_count) }}</span>
            </div>
        </div>

        <!-- Temel Teknik Parametreler Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 pt-6">
            <div class="bg-slate-950/60 border border-slate-800 rounded-xl p-3.5">
                <span class="text-[10px] uppercase font-medium text-slate-400 block mb-1">Dönem</span>
                <span class="font-semibold text-slate-200 text-sm block truncate" title="{{ $coin->period?->name }}">
                    {{ $coin->period?->name ?? '—' }}
                </span>
                <span class="text-[10px] text-slate-400 block font-mono">{{ $coin->period?->formatted_date_range }}</span>
            </div>

            <div class="bg-slate-950/60 border border-slate-800 rounded-xl p-3.5">
                <span class="text-[10px] uppercase font-medium text-slate-400 block mb-1">Hükümdar / Otorite</span>
                <span class="font-semibold text-amber-400 text-sm block truncate">
                    {{ $coin->ruler?->name ?? 'Senato / Şehir Devleti' }}
                </span>
                <span class="text-[10px] text-slate-400 block truncate">{{ $coin->ruler?->title }}</span>
            </div>

            <div class="bg-slate-950/60 border border-slate-800 rounded-xl p-3.5">
                <span class="text-[10px] uppercase font-medium text-slate-400 block mb-1">Antik Darphane</span>
                <span class="font-semibold text-slate-200 text-sm block truncate">
                    {{ $coin->mint?->name ?? 'Bilinmiyor' }}
                </span>
                <div class="flex items-center justify-between mt-0.5">
                    <span class="text-[10px] text-slate-400 block truncate">{{ $coin->mint?->location }}</span>
                    @if($coin->mint)
                        <a href="{{ route('map.index', ['mint' => $coin->mint->slug]) }}" class="text-[10px] text-amber-400 hover:text-amber-300 font-semibold flex items-center gap-0.5 shrink-0 ml-1 transition" title="Haritada Konumu İncele">
                            <span>🗺️ Harita</span>
                        </a>
                    @endif
                </div>
            </div>

            <div class="bg-slate-950/60 border border-slate-800 rounded-xl p-3.5">
                <span class="text-[10px] uppercase font-medium text-slate-400 block mb-1">Ağırlık</span>
                <span class="font-bold text-slate-100 text-sm font-mono block">
                    {{ $coin->weight_g ? $coin->weight_g . ' g' : '—' }}
                </span>
                <span class="text-[10px] text-slate-400 block">Gram Ağırlığı</span>
            </div>

            <div class="bg-slate-950/60 border border-slate-800 rounded-xl p-3.5">
                <span class="text-[10px] uppercase font-medium text-slate-400 block mb-1">Çap</span>
                <span class="font-bold text-slate-100 text-sm font-mono block">
                    {{ $coin->diameter_mm ? $coin->diameter_mm . ' mm' : '—' }}
                </span>
                <span class="text-[10px] text-slate-400 block">Milimetre Çapı</span>
            </div>

            <div class="bg-slate-950/60 border border-slate-800 rounded-xl p-3.5">
                <span class="text-[10px] uppercase font-medium text-slate-400 block mb-1">Maden</span>
                <span class="font-semibold text-amber-300 text-sm block capitalize">
                    {{ $coin->metal->value }}
                </span>
                <span class="text-[10px] text-slate-400 block">{{ $coin->denomination }}</span>
            </div>
        </div>
    </div>

    <!-- 2. ÖN VE ARKA YÜZ KARŞILAŞTIRMA BLOĞU -->
    <div class="mb-12">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-numis text-xl font-bold text-slate-100 tracking-wider flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                ÖN VE ARKA YÜZ LEJANT ÇÖZÜMLEMESİ
            </h2>
            <span class="text-xs text-slate-400">Akademik Epigrafi ve Tasvir İncelemesi</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- ÖN YÜZ (AVERS) -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden flex flex-col shadow-xl">
                <div class="bg-slate-850 px-6 py-3 border-b border-slate-800 flex items-center justify-between">
                    <span class="font-numis text-sm font-bold text-amber-400 tracking-wider uppercase">ÖN YÜZ (AVERS)</span>
                    <span class="text-xs text-slate-400 font-mono">Obverse</span>
                </div>

                <!-- Görsel -->
                <div class="aspect-square bg-slate-950 p-6 flex items-center justify-center border-b border-slate-800">
                    @php
                        $obverseImg = $coin->images->firstWhere('image_type.value', 'obverse') ?? $coin->primaryImage;
                    @endphp
                    @if($obverseImg)
                        <img src="{{ $obverseImg->url }}" 
                             alt="{{ $coin->title }} - Ön Yüz" 
                             class="max-h-full max-w-full object-contain rounded-lg shadow-2xl hover:scale-105 transition duration-300">
                    @else
                        <div class="text-slate-600 text-xs text-center font-mono">
                            Ön yüz detay fotoğrafı eklenmemiş
                        </div>
                    @endif
                </div>

                <!-- Açıklama ve Çözümleme -->
                <div class="p-6 space-y-4 flex-1 flex flex-col justify-between">
                    <div>
                        <!-- Orijinal Lejant Metni -->
                        <div class="mb-4">
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-amber-400/90 block mb-1">
                                Orijinal Sikke Yazısı (Lejant):
                            </span>
                            <div class="bg-slate-950 border border-slate-800 rounded-xl p-3.5 text-slate-100 font-legend text-base tracking-widest break-words selection:bg-amber-500/30">
                                {{ $coin->obverse_legend ?? 'YAZISIZ (ANEPIGRAPHIC)' }}
                            </div>
                        </div>

                        <!-- Türkçe Akademik Çeviri -->
                        @if($coin->obverse_legend_trans)
                            <div class="mb-4">
                                <span class="text-[11px] font-semibold uppercase tracking-wider text-emerald-400 block mb-1">
                                    Lejant Türkçe Çözümlemesi:
                                </span>
                                <div class="bg-emerald-950/20 border border-emerald-500/20 rounded-xl p-3.5 text-sm text-slate-200 leading-relaxed italic">
                                    "{{ $coin->obverse_legend_trans }}"
                                </div>
                            </div>
                        @endif

                        <!-- Tasvir Açıklaması -->
                        <div>
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">
                                Ön Yüz Tasvir İkonografisi:
                            </span>
                            <p class="text-sm text-slate-300 leading-relaxed">
                                {{ $coin->obverse_desc ?? 'Tasvir açıklaması girilmemiş.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ARKA YÜZ (REVERS) -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden flex flex-col shadow-xl">
                <div class="bg-slate-850 px-6 py-3 border-b border-slate-800 flex items-center justify-between">
                    <span class="font-numis text-sm font-bold text-amber-400 tracking-wider uppercase">ARKA YÜZ (REVERS)</span>
                    <span class="text-xs text-slate-400 font-mono">Reverse</span>
                </div>

                <!-- Görsel -->
                <div class="aspect-square bg-slate-950 p-6 flex items-center justify-center border-b border-slate-800">
                    @php
                        $reverseImg = $coin->images->firstWhere('image_type.value', 'reverse') ?? $coin->primaryImage;
                    @endphp
                    @if($reverseImg)
                        <img src="{{ $reverseImg->url }}" 
                             alt="{{ $coin->title }} - Arka Yüz" 
                             class="max-h-full max-w-full object-contain rounded-lg shadow-2xl hover:scale-105 transition duration-300">
                    @else
                        <div class="text-slate-600 text-xs text-center font-mono">
                            Arka yüz detay fotoğrafı eklenmemiş
                        </div>
                    @endif
                </div>

                <!-- Açıklama ve Çözümleme -->
                <div class="p-6 space-y-4 flex-1 flex flex-col justify-between">
                    <div>
                        <!-- Orijinal Lejant Metni -->
                        <div class="mb-4">
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-amber-400/90 block mb-1">
                                Orijinal Sikke Yazısı (Lejant):
                            </span>
                            <div class="bg-slate-950 border border-slate-800 rounded-xl p-3.5 text-slate-100 font-legend text-base tracking-widest break-words selection:bg-amber-500/30">
                                {{ $coin->reverse_legend ?? 'YAZISIZ (ANEPIGRAPHIC)' }}
                            </div>
                        </div>

                        <!-- Türkçe Akademik Çeviri -->
                        @if($coin->reverse_legend_trans)
                            <div class="mb-4">
                                <span class="text-[11px] font-semibold uppercase tracking-wider text-emerald-400 block mb-1">
                                    Lejant Türkçe Çözümlemesi:
                                </span>
                                <div class="bg-emerald-950/20 border border-emerald-500/20 rounded-xl p-3.5 text-sm text-slate-200 leading-relaxed italic">
                                    "{{ $coin->reverse_legend_trans }}"
                                </div>
                            </div>
                        @endif

                        <!-- Tasvir Açıklaması -->
                        <div>
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">
                                Arka Yüz Tasvir İkonografisi:
                            </span>
                            <p class="text-sm text-slate-300 leading-relaxed">
                                {{ $coin->reverse_desc ?? 'Tasvir açıklaması girilmemiş.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ÇAPRAZ REFERANS (CONCORDANCE) & VARYANT TABLOSU -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <!-- Çapraz Referanslar (Concordance) -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <h3 class="font-numis text-base font-bold text-slate-100 tracking-wider flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        ÇAPRAZ REFERANS TABLOSU (CONCORDANCE)
                    </h3>
                    <span class="text-xs text-slate-400 font-mono">{{ $coin->references->count() }} Kayıt</span>
                </div>
                <p class="text-xs text-slate-400 mb-4 leading-relaxed">
                    Bu sikke tipi farklı uluslararası akademik nümizmatik kataloglarda aşağıdaki referans numaralarıyla taranabilir:
                </p>

                <div class="space-y-2.5">
                    @forelse($coin->references as $ref)
                        <div class="flex items-center justify-between bg-slate-950 border border-slate-800/80 rounded-xl p-3 hover:border-amber-500/40 transition">
                            <div class="flex items-center gap-3">
                                <span class="bg-amber-500/10 border border-amber-500/30 text-amber-300 font-mono text-xs font-bold px-2.5 py-1 rounded-md">
                                    {{ $ref->catalog_system }}
                                </span>
                                <div>
                                    <span class="font-mono text-sm font-semibold text-slate-100">{{ $ref->reference_number }}</span>
                                    @if($ref->notes)
                                        <p class="text-[11px] text-slate-400">{{ $ref->notes }}</p>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('coins.search', ['q' => $ref->catalog_system . ' ' . $ref->reference_number]) }}" 
                               class="text-xs text-amber-400 hover:text-amber-300 font-medium hover:underline flex items-center gap-1">
                                <span>Arama</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    @empty
                        <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 text-center text-xs text-slate-400 font-mono">
                            Temel katalog referansı: {{ $coin->catalog_reference }}
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Tipolojik Varyantlar (Variants) -->
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <h3 class="font-numis text-base font-bold text-slate-100 tracking-wider flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        TİPOLOJİK VARYANTLAR & LEJANT KIRILMALARI
                    </h3>
                    <span class="text-xs text-slate-400 font-mono">{{ $coin->variants->count() }} Varyasyon</span>
                </div>
                <p class="text-xs text-slate-400 mb-4 leading-relaxed">
                    Aynı darp serisinin küçük lejant kırılması, darphane monogramı ve portre detay varyasyonları:
                </p>

                <div class="space-y-3">
                    @forelse($coin->variants as $variant)
                        <div class="bg-slate-950 border border-slate-800/80 rounded-xl p-3.5 hover:border-purple-500/40 transition">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-mono text-xs font-bold text-purple-300 bg-purple-950/60 border border-purple-500/30 px-2 py-0.5 rounded">
                                    {{ $variant->variant_code }}
                                </span>
                                @if($variant->rarity_shift)
                                    <span class="text-[11px] font-mono text-amber-400 font-semibold">
                                        Nadirlik: {{ $variant->rarity_shift }}
                                    </span>
                                @endif
                            </div>
                            @if($variant->obverse_diff)
                                <p class="text-xs text-slate-300 leading-snug mb-1">
                                    <strong class="text-slate-400">Ön Yüz:</strong> {{ $variant->obverse_diff }}
                                </p>
                            @endif
                            @if($variant->reverse_diff)
                                <p class="text-xs text-slate-300 leading-snug">
                                    <strong class="text-slate-400">Arka Yüz:</strong> {{ $variant->reverse_diff }}
                                </p>
                            @endif
                        </div>
                    @empty
                        <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 text-center text-xs text-slate-400 font-mono">
                            Bu tip için kayıtlı alt varyant bulunmuyor (Standart ana tip).
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- 3. İNTERAKTİF KONDİSYON & PİYASA DEĞER TABLOSU -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 mb-12 shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="font-numis text-xl font-bold text-slate-100 tracking-wider flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    ULUSLARARASI MÜZAYEDE & KONDİSYON REFERANS TABLOSU
                </h3>
                <p class="text-xs text-slate-400 mt-1">
                    CNG, Heritage, Leu Numismatik ve Gorny & Mosch gibi global müzayedelerin gerçekleşen çekiç fiyatları baz alınmıştır.
                </p>
            </div>
            <span class="text-[11px] bg-slate-800 border border-slate-700 text-slate-300 px-3 py-1.5 rounded-lg font-mono self-start sm:self-auto">
                Para Birimi: USD ($)
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-800 text-slate-400 text-xs uppercase tracking-wider">
                        <th class="py-3 px-4">Kondisyon Derecesi</th>
                        <th class="py-3 px-4">Açıklama / Standart</th>
                        <th class="py-3 px-4 text-right">Referans Değer Bandı (USD)</th>
                        <th class="py-3 px-4 text-right">Son Güncelleme</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @foreach($conditionGrades as $grade)
                        @php
                            $val = $valuationMap[$grade->value] ?? null;
                        @endphp
                        <tr class="hover:bg-slate-850/60 transition group">
                            <!-- Kondisyon Kısaltması -->
                            <td class="py-4 px-4 font-bold font-mono">
                                <span class="inline-block px-2.5 py-1 rounded text-xs
                                    {{ $grade->value === 'UNC' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : '' }}
                                    {{ $grade->value === 'XF' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : '' }}
                                    {{ $grade->value === 'VF' ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : '' }}
                                    {{ $grade->value === 'Fine' ? 'bg-slate-800 text-slate-300' : '' }}
                                    {{ $grade->value === 'Poor' ? 'bg-stone-800 text-stone-400' : '' }}">
                                    {{ $grade->value }}
                                </span>
                            </td>

                            <!-- Kondisyon Tanımı -->
                            <td class="py-4 px-4 text-slate-300">
                                {{ $grade->label() }}
                            </td>

                            <!-- Değer Bandı -->
                            <td class="py-4 px-4 text-right font-mono font-bold text-slate-100 text-base">
                                @php
                                    $isProUser = auth()->user()?->isPro() ?? false;
                                    $isGradeLocked = !$isProUser && in_array($grade->value, ['VF', 'XF', 'UNC']);
                                @endphp
                                @if($isGradeLocked)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-sans font-semibold bg-amber-500/10 border border-amber-500/30 text-amber-300 px-2.5 py-1 rounded-md" title="Bu kondisyon verisi Nümizmat Pro üyelerine açıktır">
                                        <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                        </svg>
                                        Pro Üyelik
                                    </span>
                                @elseif($val)
                                    <span class="text-amber-400">
                                        ${{ number_format($val->min_value_usd, 0) }}
                                    </span>
                                    <span class="text-slate-500 font-normal mx-1">-</span>
                                    <span class="text-amber-400">
                                        ${{ number_format($val->max_value_usd, 0) }}
                                    </span>
                                    <span class="text-xs text-slate-400 font-normal ml-1">USD</span>
                                @else
                                    <span class="text-slate-600 font-normal text-xs font-sans">Müzayede Verisi Yok</span>
                                @endif
                            </td>

                            <!-- Tarih -->
                            <td class="py-4 px-4 text-right text-xs text-slate-400 font-mono">
                                {{ $val?->last_updated_at?->format('d.m.Y') ?? '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 pt-4 border-t border-slate-800 text-[11px] text-slate-400 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
            <span>* Değerler tahmini referans bantlarıdır; son 24 aydaki uluslararası satışların uç değerleri elenerek medyan bazında konsolide edilmiştir.</span>
            <span class="font-semibold text-slate-400">Piyasa Kaynağı: Heritage / CNG / Leu / Gorny & Mosch</span>
        </div>

        <!-- SON 24 AYDAKİ GERÇEKLEŞEN AÇIK ARTIRMA SATIŞLARI (AUCTION RECORDS) -->
        <div class="mt-8 pt-6 border-t border-slate-800">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-sm font-bold text-slate-200 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    KAMUYA AÇIK GERÇEKLEŞEN SATIŞ ARŞİVİ (SON 24 AY)
                </h4>
                <span class="text-xs text-slate-400 font-mono">{{ $coin->auctionRecords->count() }} Satış Kaydı</span>
            </div>

            @if($coin->auctionRecords->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-800/80 text-slate-400 uppercase tracking-wider">
                                <th class="py-2.5 px-3">Müzayede Evi & Satış</th>
                                <th class="py-2.5 px-3">Lot No</th>
                                <th class="py-2.5 px-3">Kondisyon</th>
                                <th class="py-2.5 px-3">Tarih</th>
                                <th class="py-2.5 px-3 text-right">Çekiç Fiyatı</th>
                                <th class="py-2.5 px-3 text-right">Net Gerçekleşen (USD)</th>
                                <th class="py-2.5 px-3 text-center">Kaynak</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 font-mono">
                            @foreach($coin->auctionRecords as $auction)
                                <tr class="hover:bg-slate-850/40 transition">
                                    <td class="py-2.5 px-3 font-sans font-medium text-slate-200">
                                        {{ $auction->auction_house }}
                                        <span class="block text-[11px] text-slate-400">{{ $auction->sale_name_or_number }}</span>
                                    </td>
                                    <td class="py-2.5 px-3 text-slate-300">{{ $auction->lot_number }}</td>
                                    <td class="py-2.5 px-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-amber-300 border border-slate-700">
                                            {{ $auction->condition_grade?->value ?? $auction->condition_grade }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 text-slate-400">{{ $auction->sale_date?->format('d.m.Y') }}</td>
                                    <td class="py-2.5 px-3 text-right text-slate-400">${{ number_format($auction->hammer_price_usd, 0) }}</td>
                                    <td class="py-2.5 px-3 text-right text-emerald-400 font-bold">${{ number_format($auction->realized_price_usd, 0) }}</td>
                                    <td class="py-2.5 px-3 text-center font-sans">
                                        @if($auction->source_url)
                                            <a href="{{ $auction->source_url }}" target="_blank" rel="noopener noreferrer" class="text-amber-400 hover:text-amber-300 hover:underline text-[11px]">
                                                Lot Gör ↗
                                            </a>
                                        @else
                                            <span class="text-slate-600">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 text-center text-xs text-slate-400 font-mono">
                    Bu sikke için son 24 aya ait açık artırma satış kaydı henüz arşivlenmemiş.
                </div>
            @endif
        </div>

        <!-- Şahsi Koleksiyona Ekleme Formu -->
        <div class="mt-6 pt-6 border-t border-slate-800">
            <h4 class="text-sm font-bold text-amber-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Bu Sikkeyi Şahsi Koleksiyon Defterime Ekle
            </h4>
            <form action="{{ route('collection.store', $coin->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                @csrf
                <div>
                    <label class="block text-[11px] text-slate-400 mb-1">Elinizdeki Kondisyon *</label>
                    <select name="user_condition" required class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-2 text-xs text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        @foreach($conditionGrades as $grade)
                            <option value="{{ $grade->value }}" {{ $grade->value === 'VF' ? 'selected' : '' }}>
                                {{ $grade->value }} - {{ $grade->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[11px] text-slate-400 mb-1">Edinme Bedeli (USD, Opsiyonel)</label>
                    <input type="number" step="0.01" name="purchase_price_usd" placeholder="Örn: 350.00" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-2 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-[11px] text-slate-400 mb-1">Sikkenizin Fotoğrafı (Opsiyonel)</label>
                    <input type="file" name="custom_image" accept="image/*" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-slate-400 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:bg-slate-800 file:text-slate-300">
                </div>
                <div>
                    <label class="block text-[11px] text-slate-400 mb-1">Kişisel Notunuz</label>
                    <input type="text" name="notes" placeholder="Örn: 2024 İstanbul müzayedesinden alındı" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-2 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-amber-500 hover:bg-amber-450 text-slate-950 font-bold py-2 px-4 rounded-lg text-xs transition">
                        Envanterime Kaydet
                    </button>
                </div>
                <div class="sm:col-span-2 lg:col-span-5 pt-2">
                    <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-300 select-none">
                        <input type="checkbox" name="legal_declaration" value="1" class="rounded border-slate-700 bg-slate-950 text-amber-500 focus:ring-amber-500">
                        <span>Bu görselin yasal haklarına sahibim / kişisel koleksiyon veya açık kaynak amaçlı ekliyorum (2863 sayılı kanun hükümleri saklıdır).</span>
                    </label>
                </div>
            </form>
        </div>
    </div>

    <!-- 4. TARİHSEL VE NÜMİZMATİK NOTLAR -->
    @if($coin->historical_notes)
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 mb-12 shadow-xl">
            <h3 class="font-numis text-xl font-bold text-slate-100 tracking-wider mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                TARİHSEL BAĞLAM VE NÜMİZMATİK NOTLAR
            </h3>
            <div class="prose prose-invert max-w-none text-slate-300 text-sm leading-relaxed whitespace-pre-line">
                {{ $coin->historical_notes }}
            </div>
        </div>
    @endif

    <!-- 5. FİZİKSEL SAPMA VE SAHTELİK DOĞRULAMA ARACI -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 mb-12 shadow-xl" id="deviationCalculator">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-6 border-b border-slate-800">
            <div>
                <h3 class="font-numis text-xl font-bold text-slate-100 tracking-wider flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                    </svg>
                    FİZİKSEL SAPMA VE SAHTELİK DOĞRULAMA ARACI
                </h3>
                <p class="text-xs text-slate-400 mt-1">
                    Elinizdeki sikkenin hassas terazi ağırlığı ve kumpas çapını girerek orijinal darp standartlarıyla anlık karşılaştırın.
                </p>
            </div>
            <div class="flex items-center gap-2 font-mono text-xs">
                <span class="bg-slate-950 border border-slate-800 px-3 py-1.5 rounded-lg text-amber-400">
                    Ref. Ağırlık: <strong>{{ $coin->weight_g ? $coin->weight_g . 'g' : '—' }}</strong>
                </span>
                <span class="bg-slate-950 border border-slate-800 px-3 py-1.5 rounded-lg text-slate-300">
                    Ref. Çap: <strong>{{ $coin->diameter_mm ? $coin->diameter_mm . 'mm' : '—' }}</strong>
                </span>
            </div>
        </div>

        <!-- İnteraktif Giriş Formu -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                    Hassas Terazi ile Ölçülen Ağırlık (Gram) *
                </label>
                <div class="relative">
                    <input type="number" 
                           id="inputWeight" 
                           step="0.01" 
                           placeholder="Örn: {{ $coin->weight_g ?? '3.40' }}" 
                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-slate-100 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono text-sm">
                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 font-mono text-xs">g</span>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                    Kumpas ile Ölçülen Çap (Milimetre, Opsiyonel)
                </label>
                <div class="relative">
                    <input type="number" 
                           id="inputDiameter" 
                           step="0.1" 
                           placeholder="Örn: {{ $coin->diameter_mm ?? '19.0' }}" 
                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-slate-100 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono text-sm">
                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 font-mono text-xs">mm</span>
                </div>
            </div>
        </div>

        <!-- Canlı Teşhis Sonuç Kutusu -->
        <div id="diagnosticAlertBox" class="hidden rounded-xl p-5 border text-sm transition-all duration-300 mb-6">
            <div class="flex items-start gap-3">
                <div id="diagnosticIcon" class="shrink-0 mt-0.5"></div>
                <div>
                    <h4 id="diagnosticTitle" class="font-bold text-sm mb-1"></h4>
                    <p id="diagnosticMessage" class="text-xs leading-relaxed"></p>
                    <div id="diagnosticStats" class="mt-3 flex flex-wrap gap-2 text-[11px] font-mono"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. SAHTE NASIL ANLAŞILIR EĞİTİCİ REHBER BİLEŞENİ -->
    <div class="mb-12">
        @include('components.fake-coin-guide')
    </div>

    <!-- Hesaplayıcı JavaScript Mantığı -->
    <script>
        (function() {
            const refWeight = {{ (float) ($coin->weight_g ?? 0) }};
            const refDiameter = {{ (float) ($coin->diameter_mm ?? 0) }};

            const inputWeight = document.getElementById('inputWeight');
            const inputDiameter = document.getElementById('inputDiameter');
            const alertBox = document.getElementById('diagnosticAlertBox');
            const titleEl = document.getElementById('diagnosticTitle');
            const msgEl = document.getElementById('diagnosticMessage');
            const statsEl = document.getElementById('diagnosticStats');
            const iconEl = document.getElementById('diagnosticIcon');

            function calculateDeviation() {
                const weightVal = parseFloat(inputWeight.value);
                const diameterVal = parseFloat(inputDiameter.value);

                if (!weightVal || isNaN(weightVal) || weightVal <= 0 || refWeight <= 0) {
                    alertBox.classList.add('hidden');
                    return;
                }

                alertBox.classList.remove('hidden');

                const weightDev = (((weightVal - refWeight) / refWeight) * 100);
                const absWeightDev = Math.abs(weightDev);

                let diameterDev = 0;
                let hasDiameter = false;
                if (diameterVal && !isNaN(diameterVal) && diameterVal > 0 && refDiameter > 0) {
                    diameterDev = (((diameterVal - refDiameter) / refDiameter) * 100);
                    hasDiameter = true;
                }
                const absDiameterDev = Math.abs(diameterDev);

                // CSS sınıflarını temizle
                alertBox.className = 'rounded-xl p-5 border text-sm transition-all duration-300 mb-6';

                // Normal Tolerans (<= %8)
                if (absWeightDev <= 8.0 && (!hasDiameter || absDiameterDev <= 6.0)) {
                    alertBox.classList.add('bg-emerald-950/40', 'border-emerald-500/40', 'text-emerald-200');
                    iconEl.innerHTML = `<svg class="w-6 h-6 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>`;
                    titleEl.textContent = 'Normal Darp Tolerans Aralığında';
                    msgEl.textContent = `Girdiğiniz ağırlık (${weightVal}g), orijinal darp standardına (${refWeight}g) oldukça yakındır (%${weightDev > 0 ? '+' : ''}${weightDev.toFixed(1)} sapma). Antik çekiç darp toleransları dahilindedir.`;
                } 
                // Hafif Aşınma / Tolerans (%8 - %15)
                else if (absWeightDev <= 15.0 && (!hasDiameter || absDiameterDev <= 10.0)) {
                    alertBox.classList.add('bg-amber-950/40', 'border-amber-500/40', 'text-amber-200');
                    iconEl.innerHTML = `<svg class="w-6 h-6 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>`;
                    titleEl.textContent = 'Hafif Tolerans / Aşınma Sapması';
                    msgEl.textContent = `Girdiğiniz ağırlıkta %${weightDev > 0 ? '+' : ''}${weightDev.toFixed(1)} oranında sapma tespit edildi. Dolaşım aşınması veya antik flan kesimindeki doğal toleranslar bu farka yol açmış olabilir.`;
                } 
                // Kritik Sapma / Sahtelik Uyarısı (> %15)
                else {
                    alertBox.classList.add('bg-red-950/50', 'border-red-500/50', 'text-red-200');
                    iconEl.innerHTML = `<svg class="w-6 h-6 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>`;
                    titleEl.textContent = 'Yüksek Sapma / Sahtelik & Döküm Uyarısı!';

                    if (weightDev < -15.0) {
                        msgEl.textContent = `Dikkat: Girdiğiniz ağırlık (${weightVal}g), orijinal darp standartlarının (${refWeight}g) oldukça altındadır (%${weightDev.toFixed(1)} sapma). Sikke döküm replika, kalay/kurşun dolgulu taklit veya antik dönemde aşırı traşlanmış (clipped) olabilir.`;
                    } else {
                        msgEl.textContent = `Dikkat: Girdiğiniz ağırlık (${weightVal}g), orijinal darp standartlarının (${refWeight}g) oldukça üzerindedir (%+${weightDev.toFixed(1)} sapma). Orijinal sikkelerden silikon kalıp alınarak dökülen replikalar genellikle standarttan daha kalın ve ağır çıkmaktadır.`;
                    }
                }

                // İstatistik Rozetleri
                statsEl.innerHTML = `
                    <span class="bg-black/40 px-2.5 py-1 rounded">Ağırlık Sapması: ${weightDev > 0 ? '+' : ''}${weightDev.toFixed(1)}%</span>
                    ${hasDiameter ? `<span class="bg-black/40 px-2.5 py-1 rounded">Çap Sapması: ${diameterDev > 0 ? '+' : ''}${diameterDev.toFixed(1)}%</span>` : ''}
                `;
            }

            inputWeight.addEventListener('input', calculateDeviation);
            inputDiameter.addEventListener('input', calculateDeviation);
        })();
    </script>

    <!-- 5. BENZER / İLGİLİ SİKKELER -->
    @if($similarCoins->count() > 0)
        <div>
            <h3 class="font-numis text-xl font-bold text-slate-100 tracking-wider mb-6 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                DÖNEMİN DİĞER TİPOLOJİK SİKKELERİ
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($similarCoins as $similar)
                    <a href="{{ route('coins.show', $similar->slug) }}" class="bg-slate-900 border border-slate-800 hover:border-amber-500/50 rounded-xl overflow-hidden p-4 group transition flex flex-col justify-between">
                        <div class="aspect-square bg-slate-950 rounded-lg p-3 flex items-center justify-center mb-3">
                            @if($similar->primaryImage)
                                <img src="{{ $similar->primaryImage->url }}" alt="{{ $similar->title }}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition">
                            @else
                                <span class="text-xs text-slate-600 font-mono">Görsel Yok</span>
                            @endif
                        </div>
                        <div>
                            <span class="text-[11px] font-mono text-amber-400 block mb-1">{{ $similar->catalog_reference }}</span>
                            <h4 class="font-numis text-xs font-bold text-slate-200 group-hover:text-amber-400 transition line-clamp-2">
                                {{ $similar->title }}
                            </h4>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection

