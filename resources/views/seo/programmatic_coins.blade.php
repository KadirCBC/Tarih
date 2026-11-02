@extends('layouts.app')

@section('title', $pageTitle)
@section('meta_description', $metaDescription)
@section('canonical_url', url("/{$slug}"))

@section('schema_org')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "CollectionPage",
    "name": "{{ $pageTitle }}",
    "description": "{{ $metaDescription }}",
    "url": "{{ url("/{$slug}") }}",
    "mainEntity": {
        "@@type": "ItemList",
        "numberOfItems": {{ $coins->total() }},
        "itemListElement": [
            @foreach($coins as $index => $coin)
            {
                "@@type": "ListItem",
                "position": {{ $index + 1 }},
                "url": "{{ route('coins.show', $coin->slug) }}",
                "name": "{{ $coin->title }}"
            }@if(!$loop->last),@endif
            @endforeach
        ]
    }
}
</script>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Ekmek Kırıntısı -->
    <nav class="flex items-center text-xs text-slate-400 mb-6 space-x-2">
        <a href="{{ route('home') }}" class="hover:text-amber-400 transition">Ana Sayfa</a>
        <span>/</span>
        <a href="{{ route('coins.index') }}" class="hover:text-amber-400 transition">Katalog</a>
        <span>/</span>
        <span class="text-slate-200">{{ $h1Title }}</span>
    </nav>

    <!-- Programatik Başlık & Açıklama Bloğu (SEO Odaklı İçerik) -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 mb-8 shadow-xl">
        <div class="flex items-center gap-2 mb-3">
            <span class="bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold px-3 py-1 rounded-md">
                {{ $metal->label() }}
            </span>
            <span class="text-xs text-slate-400 font-mono">
                Toplam {{ $coins->total() }} Sikke Tipi
            </span>
        </div>

        <h1 class="font-numis text-2xl sm:text-3xl font-bold text-slate-100 mb-3 tracking-wide">
            {{ $h1Title }}
        </h1>

        <p class="text-slate-300 text-sm leading-relaxed max-w-4xl mb-4">
            {{ $entityContext }}
        </p>

        <div class="flex flex-wrap gap-2 text-xs text-slate-400">
            <span class="text-slate-500">İlgili Filtreler:</span>
            <a href="{{ route('coins.index', ['metal' => $metal->value]) }}" class="bg-slate-800 hover:bg-slate-700 text-amber-400/90 px-2.5 py-1 rounded transition">
                Tüm {{ $metal->value }} Sikkeleri
            </a>
            @if($period)
                <a href="{{ route('periods.show', $period->slug) }}" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-2.5 py-1 rounded transition">
                    {{ $period->name }} Dönemi
                </a>
            @endif
            @if($ruler)
                <a href="{{ route('rulers.show', $ruler->slug) }}" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-2.5 py-1 rounded transition">
                    {{ $ruler->name }}
                </a>
            @endif
        </div>
    </div>

    <!-- Filtrelenmiş Sikke Listesi Gridi -->
    @if($coins->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($coins as $coin)
                <article class="bg-slate-900 border border-slate-800 hover:border-amber-500/50 rounded-2xl overflow-hidden shadow-lg transition duration-200 group flex flex-col justify-between">
                    <a href="{{ route('coins.show', $coin->slug) }}" class="aspect-[4/3] bg-slate-950 flex items-center justify-center p-4 border-b border-slate-800">
                        @if($coin->primaryImage)
                            <img src="{{ $coin->primaryImage->url }}" alt="{{ $coin->title }}" loading="lazy" class="max-h-full max-w-full object-contain group-hover:scale-105 transition duration-300">
                        @else
                            <span class="text-xs text-slate-600 font-mono">Görsel Yok</span>
                        @endif
                    </a>
                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <span class="font-mono text-amber-400 font-bold text-xs block mb-1">{{ $coin->catalog_reference }}</span>
                            <h3 class="font-numis font-bold text-slate-100 text-sm mb-2 group-hover:text-amber-400 transition line-clamp-2">
                                <a href="{{ route('coins.show', $coin->slug) }}">
                                    {{ $coin->title }}
                                </a>
                            </h3>
                        </div>
                        <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                            <span>{{ $coin->denomination }}</span>
                            <span class="font-mono text-slate-300">{{ $coin->weight_g ? $coin->weight_g . ' g' : '' }}</span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $coins->links() }}
        </div>
    @else
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-8 text-center text-slate-400">
            Bu kategoride henüz sikke kaydı eklenmemiştir.
        </div>
    @endif

</div>
@endsection

