@extends('layouts.app')

@section('title', $identification->title . ' - Sikke Kimlikleme Kulübü')
@section('meta_description', Str::limit(strip_tags($identification->description ?? 'Nümizmatik kimlikleme ve uzman inceleme talebi.'), 155))

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Üst Breadcrumb & Geri Dönüş -->
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('identifications.index') }}" class="inline-flex items-center gap-2 text-xs text-slate-400 hover:text-amber-400 transition font-mono">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <span>Kulüp Taleplerine Dön</span>
        </a>
        @php $badge = $identification->status_badge; @endphp
        <span class="px-3 py-1 rounded-lg text-xs font-bold font-mono border backdrop-blur-md {{ $badge['color'] }}">
            {{ $badge['icon'] }} {{ $badge['label'] }}
        </span>
    </div>

    <!-- Başarı / Hata Bildirimleri -->
    @if(session('success'))
        <div class="bg-emerald-950/80 border border-emerald-500/50 rounded-xl p-4 mb-6 text-emerald-200 text-xs flex items-center gap-2.5">
            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-950/80 border border-red-500/50 rounded-xl p-4 mb-6 text-red-200 text-xs flex items-center gap-2.5">
            <svg class="w-4 h-4 text-red-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Ana Talep Kartı -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl mb-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            
            <!-- Görseller (Ön ve Arka Yüz) -->
            <div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Ön Yüz -->
                    <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 flex flex-col items-center">
                        <span class="text-[11px] font-mono text-slate-400 uppercase tracking-wider mb-2">ÖN YÜZ (AVERS)</span>
                        <div class="aspect-square w-full flex items-center justify-center overflow-hidden">
                            <img src="{{ $identification->obverse_url }}" alt="{{ $identification->title }} - Ön Yüz" class="max-h-full max-w-full object-contain rounded hover:scale-105 transition duration-300">
                        </div>
                    </div>

                    <!-- Arka Yüz -->
                    <div class="bg-slate-950 border border-slate-800 rounded-xl p-4 flex flex-col items-center">
                        <span class="text-[11px] font-mono text-slate-400 uppercase tracking-wider mb-2">ARKA YÜZ (REVERS)</span>
                        <div class="aspect-square w-full flex items-center justify-center overflow-hidden">
                            @if($identification->reverse_url)
                                <img src="{{ $identification->reverse_url }}" alt="{{ $identification->title }} - Arka Yüz" class="max-h-full max-w-full object-contain rounded hover:scale-105 transition duration-300">
                            @else
                                <span class="text-slate-600 text-xs font-mono text-center">Arka yüz fotoğrafı eklenmemiş</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Ölçüm Değerleri -->
                <div class="grid grid-cols-2 gap-3 mt-4 text-center">
                    <div class="bg-slate-950 border border-slate-800/80 rounded-lg p-2.5">
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block">Ölçülen Ağırlık:</span>
                        <span class="font-mono text-sm font-bold text-amber-400">{{ $identification->weight_g ? $identification->weight_g . ' g' : 'Belirtilmedi' }}</span>
                    </div>
                    <div class="bg-slate-950 border border-slate-800/80 rounded-lg p-2.5">
                        <span class="text-[10px] text-slate-500 uppercase tracking-wider block">Ölçülen Çap:</span>
                        <span class="font-mono text-sm font-bold text-amber-400">{{ $identification->diameter_mm ? $identification->diameter_mm . ' mm' : 'Belirtilmedi' }}</span>
                    </div>
                </div>
            </div>

            <!-- Künye ve Açıklama -->
            <div class="flex flex-col justify-between h-full">
                <div>
                    <h1 class="font-numis text-2xl font-bold text-slate-100 tracking-wide mb-3">
                        {{ $identification->title }}
                    </h1>

                    <div class="flex items-center gap-3 text-xs text-slate-400 pb-4 border-b border-slate-800 mb-4 font-mono">
                        <span>Gönderen: <strong>{{ $identification->user->name }}</strong></span>
                        <span>•</span>
                        <span>{{ $identification->created_at->format('d.m.Y H:i') }}</span>
                    </div>

                    <!-- Kullanıcı Notu -->
                    <div class="mb-6">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-2 font-mono">Kullanıcı Açıklaması:</span>
                        <p class="text-sm text-slate-300 leading-relaxed bg-slate-950/60 p-4 rounded-xl border border-slate-800/80">
                            {{ $identification->description ?? 'Ek bir açıklama girilmemiş.' }}
                        </p>
                    </div>

                    <!-- Onaylanmış Katalog Sikkesi Paneli (Varsa) -->
                    @if($identification->identifiedCoin)
                        <div class="bg-gradient-to-r from-emerald-950/60 via-slate-900 to-emerald-950/60 border border-emerald-500/40 rounded-xl p-5 mb-6">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider flex items-center gap-1.5 font-mono">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    KESİNLEŞEN KATALOG TİPİ
                                </span>
                                @if($identification->verifiedBy)
                                    <span class="text-[11px] text-emerald-300 font-mono bg-emerald-900/60 px-2 py-0.5 rounded border border-emerald-500/30">
                                        Onaylayan: {{ $identification->verifiedBy->name }} ({{ $identification->verifiedBy->expert_title ?? 'Doğrulanmış Nümizmat' }})
                                    </span>
                                @endif
                            </div>

                            <h3 class="font-bold text-slate-100 text-base mb-1">
                                {{ $identification->identifiedCoin->title }}
                            </h3>
                            <p class="text-xs text-slate-400 font-mono mb-3">
                                Referans: {{ $identification->identifiedCoin->catalog_reference }} | {{ $identification->identifiedCoin->period?->name }}
                            </p>

                            @if($identification->expert_notes)
                                <p class="text-xs text-slate-300 italic mb-4 bg-slate-950/80 p-3 rounded-lg border border-slate-800">
                                    "{{ $identification->expert_notes }}"
                                </p>
                            @endif

                            <a href="{{ route('coins.show', $identification->identifiedCoin->slug) }}" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold px-4 py-2 rounded-lg text-xs transition">
                                <span>Katalog Kaydını ve Değerlemesini İncele</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- 2863 Kanun Hatırlatması -->
                <div class="text-[11px] text-amber-300/80 bg-amber-950/20 border border-amber-500/20 p-3 rounded-lg">
                    ⚠️ <strong>2863 Sayılı Kanun Uyarınca:</strong> Bu platformda sikke alım-satımı, definecilik veya parasal pazarlık yapılamaz. Yalnızca bilimsel nümizmatik teşhis yapılmaktadır.
                </div>
            </div>

        </div>
    </div>

    <!-- DOĞRULANMIŞ NÜMİZMAT / EDİTÖR ONAY KONSOLU (Yalnızca Uzmanlara Açık) -->
    @if(Auth::check() && Auth::user()->isExpert() && $identification->status !== 'identified')
        <div class="bg-gradient-to-r from-purple-950/60 via-slate-900 to-purple-950/60 border border-purple-500/40 rounded-2xl p-6 sm:p-8 mb-10 shadow-xl">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-6 h-6 rounded-full bg-purple-500 text-slate-950 font-bold text-xs flex items-center justify-center">🏛️</span>
                <h3 class="font-numis text-lg font-bold text-slate-100">Doğrulanmış Nümizmat Karar Konsolu</h3>
            </div>
            <p class="text-xs text-slate-400 mb-6">
                Sayın <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->expert_title ?? 'Doğrulanmış Nümizmat' }}), bu sikkenin tipini katalog kaydıyla eşleştirip talebi kesinleştirebilirsiniz.
            </p>

            <form action="{{ route('identifications.verify', $identification) }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase mb-1.5 font-mono">Katalog Sikkesi Eşleştir:</label>
                        <select name="coin_id" required class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">-- Katalogdan Sikke Seçin --</option>
                            @foreach($popularCoins as $c)
                                <option value="{{ $c->id }}">{{ $c->title }} ({{ $c->catalog_reference }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase mb-1.5 font-mono">Uzman Açıklaması / Karar Notu:</label>
                        <input type="text" name="expert_notes" placeholder="Örn: Lejant kırılması ve portre özellikleri Trajan RIC II 142 tipine aittir." class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-slate-200 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold px-5 py-2.5 rounded-xl text-xs shadow-lg transition flex items-center gap-2">
                        <span>✓ Kesin Tip Olarak Onayla & Talebi Kapat</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- TOPLULUK ÖNERİLERİ VE YORUMLAR AKIŞI -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl">
        <h3 class="font-numis text-lg font-bold text-slate-100 mb-6 flex items-center justify-between">
            <span>TOPLULUK VE UZMAN ÖNERİLERİ ({{ $identification->comments->count() }})</span>
            <span class="text-xs text-slate-400 font-mono font-normal">Moderasyonlu Tartışma</span>
        </h3>

        <!-- Yorum Listesi -->
        <div class="space-y-4 mb-8">
            @forelse($identification->comments as $comment)
                <div class="p-4 rounded-xl border {{ $comment->is_expert_verification ? 'bg-emerald-950/20 border-emerald-500/30' : 'bg-slate-950 border-slate-800/80' }}">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-xs text-slate-200">{{ $comment->user->name }}</span>
                            @if($comment->user->isExpert())
                                <span class="bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 font-mono text-[10px] font-bold px-2 py-0.5 rounded">
                                    🏛️ Doğrulanmış Nümizmat
                                </span>
                            @endif
                        </div>
                        <span class="text-[11px] text-slate-500 font-mono">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>

                    <p class="text-xs text-slate-300 leading-relaxed">
                        {{ $comment->comment }}
                    </p>

                    @if($comment->suggestedCoin)
                        <div class="mt-2.5 pt-2 border-t border-slate-800 flex items-center gap-2 text-xs">
                            <span class="text-slate-500 text-[11px]">Önerilen Sikke:</span>
                            <a href="{{ route('coins.show', $comment->suggestedCoin->slug) }}" class="text-amber-400 hover:text-amber-300 font-semibold hover:underline">
                                {{ $comment->suggestedCoin->title }}
                            </a>
                        </div>
                    @endif
                </div>
            @empty
                <div class="bg-slate-950 border border-slate-800 rounded-xl p-6 text-center text-xs text-slate-400 font-mono">
                    Bu sikke için henüz bir teşhis önerisi girilmemiş. İlk tahminde siz bulunun!
                </div>
            @endforelse
        </div>

        <!-- Yeni Teşhis / Yorum Ekleme Formu -->
        <div class="border-t border-slate-800 pt-6">
            <h4 class="text-xs font-bold text-slate-300 uppercase tracking-wider mb-3 font-mono">
                Sikke Hakkında Teşhis veya Önerinizi Paylaşın:
            </h4>

            <form action="{{ route('identifications.comment', $identification) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <textarea name="comment" 
                              rows="3" 
                              required
                              placeholder="Örn: Lejantta okunan harflerden anlaşıldığı kadarıyla M.S. 198-217 arası basılmış bir Caracalla gümüş denarius'u olabilir..." 
                              class="w-full bg-slate-950 border border-slate-700 rounded-xl p-3 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                    @error('comment')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <span class="text-[11px] text-slate-500">
                        * Alım-satım, fiyat sorma veya definecilik terimleri 2863 sayılı kanun gereği filtrelenir.
                    </span>
                    <button type="submit" class="bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold px-4 py-2 rounded-xl text-xs transition self-end sm:self-auto">
                        Öneriyi Gönder
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

