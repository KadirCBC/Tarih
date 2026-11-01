@extends('layouts.app')

@section('title', $ruler->name . ' (' . $ruler->title . ') Sikkeleri - Antik Nümizmatik')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 mb-8">
        <span class="text-xs font-mono text-amber-400 block mb-1">{{ $ruler->formatted_reign }} • {{ $ruler->period?->name }}</span>
        <h1 class="font-numis text-2xl sm:text-3xl font-bold text-slate-100 mb-1">{{ $ruler->name }}</h1>
        <p class="text-sm text-slate-400 mb-4 italic">{{ $ruler->title }}</p>
        <p class="text-slate-300 text-sm leading-relaxed max-w-4xl">{{ $ruler->biography }}</p>
    </div>

    <h2 class="font-numis text-xl font-bold text-slate-100 mb-6">{{ $ruler->name }} Adına Darbedilmiş Sikkeler ({{ $coins->total() }})</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($coins as $coin)
            <article class="bg-slate-900 border border-slate-800 hover:border-amber-500/50 rounded-2xl overflow-hidden shadow-lg transition duration-200 flex flex-col justify-between">
                <a href="{{ route('coins.show', $coin->slug) }}" class="aspect-[4/3] bg-slate-950 flex items-center justify-center p-4 border-b border-slate-800">
                    @if($coin->primaryImage)
                        <img src="{{ $coin->primaryImage->url }}" alt="{{ $coin->title }}" class="max-h-full max-w-full object-contain">
                    @else
                        <span class="text-xs text-slate-600 font-mono">Görsel Yok</span>
                    @endif
                </a>
                <div class="p-4 flex-1 flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-mono text-amber-400 font-semibold block mb-1">{{ $coin->catalog_reference }}</span>
                        <h3 class="font-numis font-bold text-slate-100 text-sm mb-2 line-clamp-2">
                            <a href="{{ route('coins.show', $coin->slug) }}" class="hover:text-amber-400 transition">
                                {{ $coin->title }}
                            </a>
                        </h3>
                    </div>
                    <div class="pt-3 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                        <span>{{ $coin->denomination }}</span>
                        <span class="font-mono text-slate-300">{{ $coin->weight_g ? $coin->weight_g . 'g' : '' }}</span>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $coins->links() }}
    </div>
</div>
@endsection

