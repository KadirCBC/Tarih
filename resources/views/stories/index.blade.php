@extends('layouts.app')

@section('title', 'Haftanın Sikkesi ve Nümizmatik Hikayeler - Antik Dünya Tarihi')
@section('meta_description', 'Nadir antik sikkelerin basım koşulları, askeri zaferleri ve nümizmatik gizemlerini anlatan editoryal hikaye arşivi.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-8">
        <h1 class="font-numis text-2xl sm:text-3xl font-bold text-slate-100 tracking-wide mb-2">
            Nümizmatik Hikayeler & Tarihi Arka Plan
        </h1>
        <p class="text-slate-400 text-sm">
            Her hafta nadir bir sikkenin darp edilme serüveni, askeri zaferleri ve nümizmatik gizemleri.
        </p>
    </div>

    <!-- 1. HAFTANIN SİKKESİ (HERO BANNER - GOOGLE DISCOVER UYUMLU) -->
    @if($featuredStory)
        <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 border border-amber-500/40 rounded-3xl overflow-hidden shadow-2xl mb-12 group">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
                <!-- Büyük Kapak Görseli (16:9 / Discover Formatı) -->
                <div class="lg:col-span-7 relative min-h-[300px] lg:min-h-[420px] bg-slate-950 overflow-hidden">
                    <img src="{{ $featuredStory->cover_image_url }}" 
                         alt="{{ $featuredStory->title }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                    <div class="absolute top-4 left-4">
                        <span class="bg-amber-500 text-slate-950 font-bold text-xs uppercase px-3 py-1.5 rounded-lg shadow-lg">
                            ⭐ HAFTANIN SİKKESİ
                        </span>
                    </div>
                </div>

                <!-- Hikaye Özeti -->
                <div class="lg:col-span-5 p-6 sm:p-8 flex flex-col justify-between">
                    <div>
                        <div class="text-xs text-amber-400/90 font-mono mb-2">
                            {{ $featuredStory->published_at?->format('d F Y') }} • {{ $featuredStory->coin?->catalog_reference }}
                        </div>
                        <h2 class="font-numis text-xl sm:text-2xl font-bold text-slate-100 leading-snug group-hover:text-amber-400 transition mb-3">
                            <a href="{{ route('stories.show', $featuredStory->slug) }}">
                                {{ $featuredStory->title }}
                            </a>
                        </h2>
                        <p class="text-slate-300 text-sm leading-relaxed mb-6">
                            {{ $featuredStory->summary }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                        <span class="text-xs text-slate-400 font-mono">
                            {{ number_format($featuredStory->view_count) }} Okunma
                        </span>
                        <a href="{{ route('stories.show', $featuredStory->slug) }}" class="inline-flex items-center gap-1.5 bg-amber-500 hover:bg-amber-450 text-slate-950 font-bold px-5 py-2.5 rounded-xl text-xs transition">
                            Hikayeyi Oku
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- 2. DİĞER EDİTORYAL HİKAYELER -->
    @if($stories->count() > 0)
        <h2 class="font-numis text-xl font-bold text-slate-100 mb-6 flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
            ARŞİVDEKİ DİĞER TARİHSEL SİKKE HİKAYELERİ
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($stories as $story)
                <article class="bg-slate-900 border border-slate-800 hover:border-amber-500/50 rounded-2xl overflow-hidden shadow-lg transition duration-200 group flex flex-col justify-between">
                    <a href="{{ route('stories.show', $story->slug) }}" class="aspect-[16/9] bg-slate-950 relative overflow-hidden block">
                        <img src="{{ $story->cover_image_url }}" alt="{{ $story->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                    </a>
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <span class="text-[11px] text-amber-400 font-mono block mb-1">
                                {{ $story->published_at?->format('d.m.Y') }} • {{ $story->coin?->catalog_reference }}
                            </span>
                            <h3 class="font-numis font-bold text-slate-100 text-base mb-2 group-hover:text-amber-400 transition line-clamp-2">
                                <a href="{{ route('stories.show', $story->slug) }}">
                                    {{ $story->title }}
                                </a>
                            </h3>
                            <p class="text-xs text-slate-400 line-clamp-3 leading-relaxed mb-4">
                                {{ $story->summary }}
                            </p>
                        </div>
                        <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                            <span>{{ $story->coin?->period?->name }}</span>
                            <a href="{{ route('stories.show', $story->slug) }}" class="text-amber-400 font-semibold hover:underline">
                                Oku &rarr;
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $stories->links() }}
        </div>
    @endif

</div>
@endsection

