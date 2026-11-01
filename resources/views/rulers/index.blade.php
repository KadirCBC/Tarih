@extends('layouts.app')

@section('title', 'Antik Hükümdarlar & Darbediciler - Nümizmatik Ansiklopedisi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="font-numis text-2xl sm:text-3xl font-bold text-slate-100 tracking-wide mb-2">
            Antik Hükümdarlar ve Otoriteler
        </h1>
        <p class="text-slate-400 text-sm">
            Adlarına para darbedilmiş Roma imparatorları, Helenistik krallar ve Bizans hükümdarları.
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($rulers as $ruler)
            <a href="{{ route('rulers.show', $ruler->slug) }}" class="bg-slate-900 border border-slate-800 hover:border-amber-500/50 rounded-2xl p-5 transition group flex flex-col justify-between">
                <div>
                    <span class="text-[11px] font-mono text-amber-400 block mb-1">{{ $ruler->formatted_reign }}</span>
                    <h2 class="font-numis font-bold text-slate-100 text-base mb-1 group-hover:text-amber-400 transition">
                        {{ $ruler->name }}
                    </h2>
                    <span class="text-xs text-slate-400 block mb-3 italic">{{ $ruler->title }}</span>
                    <span class="text-[11px] bg-slate-800 text-slate-300 px-2 py-0.5 rounded inline-block">
                        {{ $ruler->period?->name }}
                    </span>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-800 text-right text-xs text-slate-400 font-mono">
                    {{ $ruler->coins_count }} Sikke Kaydı &rarr;
                </div>
            </a>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $rulers->links() }}
    </div>
</div>
@endsection

