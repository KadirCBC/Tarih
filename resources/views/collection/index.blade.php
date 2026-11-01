@extends('layouts.app')

@section('title', 'Kişisel Nümizmatik Koleksiyonum & Portföy Değerim')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Üst Başlık ve PDF İndirme Butonu -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="font-numis text-2xl sm:text-3xl font-bold text-slate-100 tracking-wide mb-1">
                Şahsi Sikke Koleksiyon Defterim
            </h1>
            <p class="text-xs text-slate-400">
                Dijital nümizmatik arşiviniz, elinizdeki kondisyon dereceleri ve tahmini müzayede portföy değeriniz.
            </p>
        </div>

        <a href="{{ route('collection.export-pdf') }}" 
           class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-450 hover:to-yellow-450 text-slate-950 font-bold px-5 py-2.5 rounded-xl text-xs transition shadow-lg shadow-amber-500/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            PDF Koleksiyon Sertifikası İndir
        </a>
    </div>

    <!-- 1. Portföy Değer Özeti Kartları -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-slate-900 to-slate-850 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Kayıtlı Sikke Sayısı</span>
            <div class="text-3xl font-bold font-mono text-slate-100">{{ $summary['total_coins'] }} <span class="text-sm font-sans font-normal text-slate-400">Adet</span></div>
            <span class="text-[11px] text-slate-500 mt-2 block">Şahsi envanter kaydı</span>
        </div>

        <div class="bg-gradient-to-br from-amber-950/40 via-slate-900 to-slate-850 border border-amber-500/30 rounded-2xl p-6 shadow-xl">
            <span class="text-xs font-semibold text-amber-400 uppercase tracking-wider block mb-1">Tahmini Portföy Değeri (USD)</span>
            <div class="text-2xl sm:text-3xl font-bold font-mono text-amber-400">{{ $summary['price_range_formatted'] }}</div>
            <span class="text-[11px] text-amber-400/70 mt-2 block">Müzayede çekiç fiyatları bazlı referans aralığı</span>
        </div>

        <div class="bg-gradient-to-br from-slate-900 to-slate-850 border border-slate-800 rounded-2xl p-6 shadow-xl">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Maden Dağılımı</span>
            <div class="flex flex-wrap gap-2 mt-2">
                @forelse($summary['metal_breakdown'] as $metalKey => $data)
                    <span class="bg-slate-950 border border-slate-700 text-xs px-2.5 py-1 rounded-lg text-slate-200">
                        {{ ucfirst($metalKey) }}: <strong class="text-amber-400 font-mono">{{ $data['count'] }}</strong>
                    </span>
                @empty
                    <span class="text-xs text-slate-500">Henüz sikke eklenmedi</span>
                @endforelse
            </div>
        </div>
    </div>

    <!-- 2. Sikke Listesi Tablosu -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-950/60 text-slate-400 text-xs uppercase tracking-wider">
                        <th class="py-4 px-4">Görsel</th>
                        <th class="py-4 px-4">Sikke & Katalog No</th>
                        <th class="py-4 px-4">Dönem / Hükümdar</th>
                        <th class="py-4 px-4">Kondisyonum</th>
                        <th class="py-4 px-4 text-right">Tahmini Değer (USD)</th>
                        <th class="py-4 px-4 text-center">İşlem</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    @forelse($items as $item)
                        @php
                            $coin = $item->coin;
                            $val = $item->estimated_valuation;
                        @endphp
                        <tr class="hover:bg-slate-850/50 transition">
                            <!-- Görsel (Özel yüklenen veya katalog ana görseli) -->
                            <td class="py-4 px-4">
                                <div class="w-14 h-14 rounded-xl bg-slate-950 border border-slate-800 p-1 flex items-center justify-center overflow-hidden">
                                    @if($item->display_image_url)
                                        <img src="{{ $item->display_image_url }}" alt="{{ $coin?->title }}" class="max-h-full max-w-full object-contain">
                                    @else
                                        <span class="text-[10px] text-slate-600 font-mono">Yok</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Başlık & Referans -->
                            <td class="py-4 px-4">
                                <span class="font-mono text-amber-400 font-bold text-xs block mb-0.5">{{ $coin?->catalog_reference }}</span>
                                <a href="{{ $coin ? route('coins.show', $coin->slug) : '#' }}" class="font-numis font-bold text-slate-100 hover:text-amber-400 transition text-sm">
                                    {{ $coin?->title }}
                                </a>
                                @if($item->notes)
                                    <p class="text-xs text-sky-400 mt-1 italic">Notum: {{ $item->notes }}</p>
                                @endif
                            </td>

                            <!-- Dönem / Hükümdar -->
                            <td class="py-4 px-4 text-xs text-slate-300">
                                <div>{{ $coin?->period?->name }}</div>
                                <span class="text-slate-500">{{ $coin?->ruler?->name ?? '—' }}</span>
                            </td>

                            <!-- Kullanıcı Kondisyonu -->
                            <td class="py-4 px-4 font-mono font-bold">
                                <span class="bg-amber-500/20 text-amber-300 border border-amber-500/30 px-2.5 py-1 rounded text-xs">
                                    {{ $item->user_condition->value }}
                                </span>
                            </td>

                            <!-- Değerleme -->
                            <td class="py-4 px-4 text-right font-mono font-bold text-slate-100 text-sm">
                                @if($val)
                                    <span class="text-amber-400">${{ number_format($val->min_value_usd, 0) }} - ${{ number_format($val->max_value_usd, 0) }}</span>
                                @else
                                    <span class="text-slate-600 text-xs font-normal">Veri Yok</span>
                                @endif
                            </td>

                            <!-- Kaldır -->
                            <td class="py-4 px-4 text-center">
                                <form action="{{ route('collection.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Sikkeyi envanterinizden kaldırmak istediğinize emin misiniz?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-500 hover:text-red-400 transition p-1" title="Envanterden Kaldır">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-500">
                                Koleksiyon defterinizde henüz kayıtlı sikke bulunmamaktadır.
                                <div class="mt-3">
                                    <a href="{{ route('coins.index') }}" class="text-amber-400 hover:underline text-xs font-semibold">Kataloğu İncele & Ekle &rarr;</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $items->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

