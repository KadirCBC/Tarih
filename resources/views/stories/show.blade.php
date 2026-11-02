@extends('layouts.app')

@section('title', $story->title . ' - Antik Nümizmatik Hikayesi')
@section('meta_description', Str::limit(strip_tags($story->summary), 155))
@section('canonical_url', route('stories.show', $story->slug))

@section('og_image')
    <meta property="og:image" content="{{ $story->cover_image_url }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="675">
    <meta property="og:type" content="article">
    <meta property="article:published_time" content="{{ $story->published_at?->toIso8601String() }}">
@endsection

@section('schema_org')
<!-- Google Discover & NewsArticle Schema Markup -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "Article",
    "mainEntityOfPage": {
        "@@type": "WebPage",
        "@@id": "{{ route('stories.show', $story->slug) }}"
    },
    "headline": "{{ $story->title }}",
    "description": "{{ addslashes($story->summary) }}",
    "image": [
        "{{ $story->cover_image_url }}"
    ],
    "datePublished": "{{ $story->published_at?->toIso8601String() }}",
    "dateModified": "{{ $story->updated_at?->toIso8601String() }}",
    "author": {
        "@@type": "Organization",
        "name": "Nümizmatik Ansiklopedisi Editoryal Kurulu",
        "url": "{{ route('home') }}"
    },
    "publisher": {
        "@@type": "Organization",
        "name": "Antik Sikke Bilgi ve Referans Kataloğu",
        "logo": {
            "@@type": "ImageObject",
            "url": "{{ url('/images/logo.png') }}"
        }
    },
    "about": {
        "@@type": "VisualArtifact",
        "name": "{{ $story->coin?->title }}",
        "identifier": "{{ $story->coin?->catalog_reference }}"
    }
}
</script>
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Ekmek Kırıntısı -->
    <nav class="flex items-center text-xs text-slate-400 mb-6 space-x-2">
        <a href="{{ route('home') }}" class="hover:text-amber-400 transition">Ana Sayfa</a>
        <span>/</span>
        <a href="{{ route('stories.index') }}" class="hover:text-amber-400 transition">Hikayeler</a>
        <span>/</span>
        <span class="text-slate-200 truncate">{{ $story->title }}</span>
    </nav>

    <!-- Makale Başlığı ve Meta Bilgileri -->
    <header class="mb-8">
        @if($story->is_coin_of_the_week)
            <div class="inline-block bg-amber-500 text-slate-950 font-bold text-xs uppercase px-3 py-1 rounded-md mb-3">
                ⭐ HAFTANIN SİKKESİ
            </div>
        @endif

        <h1 class="font-numis text-2xl sm:text-4xl font-bold text-slate-100 leading-tight mb-4">
            {{ $story->title }}
        </h1>

        <div class="flex items-center gap-4 text-xs text-slate-400 border-b border-slate-800 pb-4">
            <span>Yayın: <strong class="text-slate-200">{{ $story->published_at?->format('d F Y') }}</strong></span>
            <span>•</span>
            <span>Okunma: <strong class="text-slate-200 font-mono">{{ number_format($story->view_count) }}</strong></span>
            <span>•</span>
            <span class="text-amber-400 font-mono">{{ $story->coin?->catalog_reference }}</span>
        </div>
    </header>

    <!-- Büyük Kapak Görseli (Google Discover Uyumlu 16:9) -->
    <div class="aspect-[16/9] w-full rounded-2xl overflow-hidden bg-slate-950 border border-slate-800 shadow-2xl mb-8">
        <img src="{{ $story->cover_image_url }}" 
             alt="{{ $story->title }}" 
             class="w-full h-full object-cover">
    </div>

    <!-- Hikaye Kancası / Özet -->
    <div class="bg-slate-900 border-l-4 border-amber-500 p-5 rounded-r-xl text-slate-200 text-base leading-relaxed italic mb-8">
        {{ $story->summary }}
    </div>

    <!-- Ana Makale İçeriği -->
    <article class="prose prose-invert prose-amber max-w-none text-slate-300 text-base leading-relaxed mb-12 whitespace-pre-line">
        {{ $story->content }}
    </article>

    <!-- İlgili Sikkenin Kataloğa Bağlantı Kartı -->
    @if($story->coin)
        @php $c = $story->coin; @endphp
        <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 border border-amber-500/30 rounded-2xl p-6 mb-12 shadow-xl flex flex-col sm:flex-row items-center gap-6">
            <div class="w-24 h-24 shrink-0 rounded-xl bg-slate-950 p-2 border border-slate-800 flex items-center justify-center">
                @if($c->primaryImage)
                    <img src="{{ $c->primaryImage->url }}" alt="{{ $c->title }}" class="max-h-full max-w-full object-contain">
                @else
                    <span class="text-xs text-slate-600 font-mono">Görsel</span>
                @endif
            </div>
            <div class="flex-1 text-center sm:text-left">
                <span class="text-xs font-mono text-amber-400 font-bold block mb-1">{{ $c->catalog_reference }}</span>
                <h3 class="font-numis text-lg font-bold text-slate-100 mb-1">{{ $c->title }}</h3>
                <p class="text-xs text-slate-400">
                    {{ $c->period?->name }} • {{ $c->ruler?->name ?? 'Darbedici' }} • {{ $c->metal->label() }}
                </p>
            </div>
            <a href="{{ route('coins.show', $c->slug) }}" class="bg-amber-500 hover:bg-amber-450 text-slate-950 font-bold px-6 py-3 rounded-xl text-xs transition shrink-0 shadow-lg shadow-amber-500/20">
                Katalog Kaydını İncele &rarr;
            </a>
        </div>
    @endif

    <!-- Diğer Hikayeler -->
    @if($relatedStories->count() > 0)
        <div class="pt-8 border-t border-slate-800">
            <h3 class="font-numis text-lg font-bold text-slate-200 mb-6">Diğer Nümizmatik Hikayeler</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                @foreach($relatedStories as $related)
                    <a href="{{ route('stories.show', $related->slug) }}" class="bg-slate-900 border border-slate-800 hover:border-amber-500/50 rounded-xl p-4 transition group flex flex-col justify-between">
                        <div>
                            <span class="text-[10px] text-amber-400 font-mono block mb-1">{{ $related->coin?->catalog_reference }}</span>
                            <h4 class="font-numis text-xs font-bold text-slate-100 group-hover:text-amber-400 transition line-clamp-2 mb-2">
                                {{ $related->title }}
                            </h4>
                        </div>
                        <span class="text-[11px] text-slate-500">Oku &rarr;</span>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endsection

