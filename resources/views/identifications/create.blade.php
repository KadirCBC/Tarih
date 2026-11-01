@extends('layouts.app')

@section('title', 'Yeni Sikke Kimlikleme Talebi Aç - Nümizmat Kulübü')
@section('meta_description', 'Elinizdeki antik sikkenin türünü ve nümizmatik referansını öğrenmek için topluluğa ve doğrulanmış nümizmatlara danışın.')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Üst Başlık -->
    <div class="mb-8">
        <a href="{{ route('identifications.index') }}" class="inline-flex items-center gap-2 text-xs text-slate-400 hover:text-amber-400 transition font-mono mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <span>Kulüp Taleplerine Dön</span>
        </a>
        <h1 class="font-numis text-2xl sm:text-3xl font-bold text-slate-100 tracking-wide">
            Yeni Kimlikleme Talebi Oluştur
        </h1>
        <p class="text-xs text-slate-400 mt-1">
            Sikkenin ön ve arka yüz fotoğraflarını yükleyin. Nümizmat editörler ve topluluk üyeleri sikkenizi incelesin.
        </p>
    </div>

    <!-- Hukuki Hatırlatma -->
    <div class="bg-amber-950/40 border border-amber-500/30 rounded-xl p-4 mb-8 text-xs text-amber-200/90 flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-400 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <div class="space-y-1">
            <strong class="text-amber-300 block">2863 Sayılı Kültür ve Tabiat Varlıklarını Koruma Kanunu Bildirimi:</strong>
            <p>
                Bu platform yalnızca nümizmatik eğitim, arşiv ve akademik kimlikleme amaçlıdır. Platform üzerinden hiçbir şekilde kültür varlığı alım-satımı, aracılığı veya definecilik faaliyetine izin verilmez. "Fiyatı ne kadar", "kaça satılır" veya "nereden buldum" gibi ifadeler otomatik filtre tarafından engellenir.
            </p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl">
        <form action="{{ route('identifications.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Başlık -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-2 font-mono">
                    Talep Başlığı: <span class="text-amber-400">*</span>
                </label>
                <input type="text" 
                       name="title" 
                       value="{{ old('title') }}" 
                       required
                       placeholder="Örn: Gümüş Antik Sikke - Ön Yüzde Miğferli Baş, Arka Yüzde At Arabası Var" 
                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500">
                @error('title')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Görsel Yüklemeleri (Ön ve Arka Yüz) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Ön Yüz Görseli -->
                <div class="bg-slate-950 border border-slate-800 rounded-xl p-4">
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-2 font-mono">
                        Ön Yüz (Avers) Fotoğrafı: <span class="text-amber-400">*</span>
                    </label>
                    <input type="file" 
                           name="obverse_image" 
                           accept="image/*"
                           required
                           class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-amber-400 hover:file:bg-slate-700 cursor-pointer">
                    <span class="text-[11px] text-slate-500 mt-2 block">Düz ışık altında, merkezlenmiş net çekim yapınız.</span>
                    @error('obverse_image')
                        <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Arka Yüz Görseli -->
                <div class="bg-slate-950 border border-slate-800 rounded-xl p-4">
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-2 font-mono">
                        Arka Yüz (Revers) Fotoğrafı:
                    </label>
                    <input type="file" 
                           name="reverse_image" 
                           accept="image/*"
                           class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-amber-400 hover:file:bg-slate-700 cursor-pointer">
                    <span class="text-[11px] text-slate-500 mt-2 block">Varsa arka yüzdeki tasvir ve lejant yazısı.</span>
                    @error('reverse_image')
                        <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Fiziksel Ölçümler (Ağırlık ve Çap) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-2 font-mono">
                        Hassas Ağırlık (Gram):
                    </label>
                    <input type="number" 
                           step="0.01" 
                           name="weight_g" 
                           value="{{ old('weight_g') }}" 
                           placeholder="Örn: 3.45" 
                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono">
                    <span class="text-[11px] text-slate-500 mt-1 block">Hassas terazi ölçümü sahtelik analizine yardımcı olur.</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase mb-2 font-mono">
                        Çap (Milimetre):
                    </label>
                    <input type="number" 
                           step="0.1" 
                           name="diameter_mm" 
                           value="{{ old('diameter_mm') }}" 
                           placeholder="Örn: 19.5" 
                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono">
                    <span class="text-[11px] text-slate-500 mt-1 block">Kumpas veya cetvel ile ölçülen en geniş çap.</span>
                </div>
            </div>

            <!-- Detaylı Açıklama -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase mb-2 font-mono">
                    Gözlemler ve İpuçları (Lejant Harfleri, Patina vb.):
                </label>
                <textarea name="description" 
                          rows="4" 
                          placeholder="Örn: Sikke gümüş görünümlü, ön yüzde 'ANTONINVS' gibi harfler seçilebiliyor. Arka yüzde tahtta oturan bir figür var..." 
                          class="w-full bg-slate-950 border border-slate-700 rounded-xl p-4 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- 2863 Kanun Onayı Checkbox -->
            <div class="bg-slate-950 p-4 rounded-xl border border-slate-800">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" 
                           name="legal_consent" 
                           value="1" 
                           required 
                           class="w-4 h-4 mt-0.5 rounded border-slate-700 bg-slate-900 text-amber-500 focus:ring-amber-500">
                    <span class="text-xs text-slate-300 leading-relaxed">
                        Yüklediğim görselin yasal haklarına sahibim. Bu talebin yalnızca nümizmatik eğitim ve akademik kimlikleme amaçlı olduğunu, <strong>2863 sayılı kanun uyarınca hiçbir şekilde alım-satım veya definecilik faaliyeti yürütmeyeceğimi</strong> kabul ve taahhüt ederim. <span class="text-amber-400">*</span>
                    </span>
                </label>
                @error('legal_consent')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <!-- Gönder Butonu -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-slate-800">
                <a href="{{ route('identifications.index') }}" class="text-xs text-slate-400 hover:text-slate-200 transition">
                    İptal
                </a>
                <button type="submit" class="bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 text-slate-950 font-bold px-6 py-3 rounded-xl text-xs shadow-lg shadow-amber-500/20 transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Kimlikleme Talebini Yayınla</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

