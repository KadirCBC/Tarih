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
                <span class="text-[10px] text-slate-400 block truncate">{{ $coin->mint?->location }}</span>
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
                                @if($val)
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
            <span>* Değerler tahmini referans bantlarıdır; sikkenin lekesizliği, patinası, merkezlemesi (centering) ve nadirliğine göre müzayedelerde farklılık gösterebilir.</span>
            <span class="font-semibold text-slate-400">Piyasa Kaynağı: NumisBids / CoinArchives Entegrasyonu</span>
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

