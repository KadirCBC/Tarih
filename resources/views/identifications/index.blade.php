@extends('layouts.app')

@section('title', 'Sikke Kimlikleme Kulübü - Uzman ve Topluluk Teşhisi')
@section('meta_description', 'Elinizdeki antik sikkenin türünü, dönemini ve hükümdarını öğrenmek için doğrulanmış nümizmatlara ve topluluğa danışabileceğiniz moderasyonlu kimlikleme platformu.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Üst Başlık & Aksiyon -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="bg-amber-500/10 border border-amber-500/30 text-amber-400 font-mono text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                    🔍 Nümizmat Danışma Kulübü
                </span>
                <span class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-semibold px-2.5 py-0.5 rounded-full">
                    Moderasyonlu
                </span>
            </div>
            <h1 class="font-numis text-3xl font-bold text-slate-100 tracking-wide">
                Sikke Kimlikleme Kulübü
            </h1>
            <p class="text-xs text-slate-400 mt-1 max-w-2xl leading-relaxed">
                Tasnif edemediğiniz antik sikkeleri toplulukla paylaşın, <strong>Doğrulanmış Nümizmatlar</strong> tarafından incelenip katalog kayıtlarıyla resmi olarak eşleştirilmesini sağlayın.
            </p>
        </div>

        <a href="{{ route('identifications.create') }}" 
           class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 text-slate-950 font-bold px-5 py-3 rounded-xl shadow-lg shadow-amber-500/20 transition self-start md:self-auto shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Yeni Kimlikleme Talebi Aç</span>
        </a>
    </div>

    <!-- 2863 Yasal Bildirimi -->
    <div class="bg-amber-950/40 border border-amber-500/30 rounded-xl p-4 mb-8 text-xs text-amber-200/90 flex items-center gap-3">
        <svg class="w-5 h-5 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <span>
            <strong>2863 Sayılı Kanun Hatırlatması:</strong> Bu platform yalnızca nümizmatik araştırma ve eğitim amaçlıdır. Yorum veya taleplerde alım-satım teklifinde bulunmak, definecilik/kaçak kazı bilgisi paylaşmak veya fiyat sormak kesinlikle yasaktır ve filtre tarafından engellenir.
        </span>
    </div>

    <!-- Durum Filtreleme Sekmeleri -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-800 pb-4 mb-8 text-xs font-semibold">
        <a href="{{ route('identifications.index', ['status' => 'all']) }}" 
           class="px-4 py-2 rounded-lg transition {{ $status === 'all' ? 'bg-amber-500 text-slate-950 font-bold' : 'bg-slate-900 text-slate-400 hover:text-slate-200 border border-slate-800' }}">
            Tümü ({{ $counts['all'] }})
        </a>
        <a href="{{ route('identifications.index', ['status' => 'pending']) }}" 
           class="px-4 py-2 rounded-lg transition {{ $status === 'pending' ? 'bg-blue-600 text-white font-bold' : 'bg-slate-900 text-slate-400 hover:text-slate-200 border border-slate-800' }}">
            ⏳ İnceleme Bekleyenler ({{ $counts['pending'] }})
        </a>
        <a href="{{ route('identifications.index', ['status' => 'identified']) }}" 
           class="px-4 py-2 rounded-lg transition {{ $status === 'identified' ? 'bg-emerald-600 text-white font-bold' : 'bg-slate-900 text-slate-400 hover:text-slate-200 border border-slate-800' }}">
            ✓ Uzman Onaylılar ({{ $counts['identified'] }})
        </a>
        <a href="{{ route('identifications.index', ['status' => 'disputed']) }}" 
           class="px-4 py-2 rounded-lg transition {{ $status === 'disputed' ? 'bg-amber-600 text-white font-bold' : 'bg-slate-900 text-slate-400 hover:text-slate-200 border border-slate-800' }}">
            ? Tartışmalı / Şüpheli ({{ $counts['disputed'] }})
        </a>
    </div>

    <!-- Talep Kartları Izgarası -->
    @if($requests->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($requests as $item)
                @php $badge = $item->status_badge; @endphp
                <div class="bg-slate-900 border border-slate-800 hover:border-slate-700 rounded-2xl overflow-hidden shadow-xl flex flex-col justify-between transition group">
                    <div>
                        <!-- Görseller -->
                        <div class="aspect-square bg-slate-950 relative overflow-hidden flex items-center justify-center p-3">
                            <img src="{{ $item->obverse_url }}" 
                                 alt="{{ $item->title }}" 
                                 class="max-h-full max-w-full object-contain rounded-lg group-hover:scale-105 transition duration-300">

                            <!-- Durum Rozeti -->
                            <div class="absolute top-3 left-3">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold font-mono border backdrop-blur-md {{ $badge['color'] }}">
                                    {{ $badge['icon'] }} {{ $badge['label'] }}
                                </span>
                            </div>
                        </div>

                        <!-- Başlık ve Künye -->
                        <div class="p-5">
                            <h3 class="font-bold text-slate-100 text-sm leading-snug line-clamp-2 mb-2 group-hover:text-amber-400 transition">
                                <a href="{{ route('identifications.show', $item) }}">
                                    {{ $item->title }}
                                </a>
                            </h3>

                            <!-- Fiziksel Veriler -->
                            <div class="flex items-center gap-2 text-[11px] font-mono text-slate-400 mb-3">
                                @if($item->weight_g)
                                    <span class="bg-slate-800 px-2 py-0.5 rounded">{{ $item->weight_g }} g</span>
                                @endif
                                @if($item->diameter_mm)
                                    <span class="bg-slate-800 px-2 py-0.5 rounded">{{ $item->diameter_mm }} mm</span>
                                @endif
                            </div>

                            <!-- Onaylanmış Sikke Varsa -->
                            @if($item->identifiedCoin)
                                <div class="bg-emerald-950/40 border border-emerald-500/30 rounded-lg p-2.5 mb-3 text-xs">
                                    <span class="text-[10px] text-emerald-400 font-bold uppercase tracking-wider block">Teşhis Edilen Tip:</span>
                                    <span class="text-slate-200 font-medium line-clamp-1">{{ $item->identifiedCoin->title }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Kart Altı (Kullanıcı & Yorum Sayısı) -->
                    <div class="px-5 py-3 bg-slate-850/60 border-t border-slate-800 text-xs text-slate-400 flex items-center justify-between">
                        <span class="truncate max-w-[140px]">{{ $item->user->name }}</span>
                        <div class="flex items-center gap-1 text-slate-300 font-mono">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            <span>{{ $item->comments_count }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $requests->links() }}
        </div>
    @else
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-12 text-center text-slate-400">
            <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
            <h3 class="font-numis text-lg font-bold text-slate-200 mb-1">Henüz Talep Bulunmuyor</h3>
            <p class="text-xs text-slate-500 mb-4">Bu kategoride incelenmeyi bekleyen bir sikke kaydı yok.</p>
            <a href="{{ route('identifications.create') }}" class="inline-block bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-4 py-2 rounded-xl text-xs transition">
                İlk Talebi Açın
            </a>
        </div>
    @endif

</div>
@endsection

