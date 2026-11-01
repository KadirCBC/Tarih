@extends('layouts.app')

@section('title', 'Epigrafik Lejant Tamamlama & Dairesel Yazı Çözücü - Nümizmatik')
@section('meta_description', 'Aşınmış, silinmiş veya kırpılmış antik sikke yazıtlarını (Latince/Grekçe) kısmi harflerden veya fotoğraftan polar transform ile tamamlayan nümizmatik epigrafik motoru.')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Başlık ve Giriş -->
    <div class="text-center max-w-3xl mx-auto mb-10">
        <span class="inline-block bg-amber-500/10 border border-amber-500/30 text-amber-400 font-mono text-xs font-bold px-3 py-1 rounded-full mb-3 uppercase tracking-wider">
            🏛️ Nümizmatik Epigrafi Motoru
        </span>
        <h1 class="font-numis text-3xl sm:text-4xl font-bold text-slate-100 tracking-wide mb-4">
            Aşınmış ve Silik Lejant Tamamlayıcı
        </h1>
        <p class="text-sm text-slate-400 leading-relaxed">
            Sikke üzerindeki dairesel Latince veya Grekçe yazıtın sadece birkaç harfini mi okuyabiliyorsunuz?
            Wildcard (<code class="text-amber-400 font-mono">...</code>) arama veya polar transform görsel analizi ile eksik lejantı ve ait olduğu sikkeyi saniyeler içinde tespit edin.
        </p>
    </div>

    <!-- Giriş Form Paneli -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-2xl mb-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- 1. Form: Kısmi Metin / Harf Girişi -->
            <div class="flex flex-col justify-between border-b md:border-b-0 md:border-r border-slate-800 pb-8 md:pb-0 md:pr-8">
                <div>
                    <h3 class="font-numis text-lg font-bold text-slate-200 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Kısmi Harf Girişi ile Arama
                    </h3>
                    <p class="text-xs text-slate-400 mb-4">
                        Okuyabildiğiniz harfleri yazın, silik kısımlar için <strong class="text-amber-400 font-mono">...</strong> veya <strong class="text-amber-400 font-mono">*</strong> kullanın.
                    </p>

                    <form action="{{ route('epigraphy.index') }}" method="GET">
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-slate-300 uppercase mb-1.5 font-mono">
                                Okunan Parçacıklar:
                            </label>
                            <input type="text" 
                                   name="q" 
                                   value="{{ $query }}"
                                   placeholder="Örn: ...NERV...TR...PARTH..." 
                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-slate-100 placeholder-slate-500 font-mono uppercase tracking-widest focus:ring-2 focus:ring-amber-500 focus:outline-none transition">
                        </div>

                        <!-- Örnek Kısayollar -->
                        <div class="mb-4">
                            <span class="text-[11px] text-slate-500 block mb-1.5">Örnek epigrafik aramalar:</span>
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('epigraphy.index', ['q' => '...NERV...TR...PARTH...']) }}" class="text-[11px] bg-slate-800 hover:bg-slate-700 text-amber-300 font-mono px-2.5 py-1 rounded-md transition">
                                    ...NERV...TR...PARTH...
                                </a>
                                <a href="{{ route('epigraphy.index', ['q' => 'BASIL...ALE...']) }}" class="text-[11px] bg-slate-800 hover:bg-slate-700 text-amber-300 font-mono px-2.5 py-1 rounded-md transition">
                                    BASIL...ALE...
                                </a>
                                <a href="{{ route('epigraphy.index', ['q' => 'DÂRİB...NASR...']) }}" class="text-[11px] bg-slate-800 hover:bg-slate-700 text-amber-300 font-mono px-2.5 py-1 rounded-md transition">
                                    DÂRİB...NASR...
                                </a>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-amber-600 to-amber-500 hover:from-amber-500 hover:to-amber-400 text-slate-950 font-bold py-2.5 px-4 rounded-xl shadow-lg shadow-amber-500/20 transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <span>Lejantı Tamamla</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- 2. Form: Fotoğraftan Polar Transform Analizi -->
            <div class="flex flex-col justify-between">
                <div>
                    <h3 class="font-numis text-lg font-bold text-slate-200 mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                        Fotoğraftan Polar Düzleştirme & OCR
                    </h3>
                    <p class="text-xs text-slate-400 mb-4">
                        Sikke fotoğrafındaki dairesel kenar yazısını yatay bir şeride açar ve epigrafik yapay zeka ile okur.
                    </p>

                    <form action="{{ route('epigraphy.reconstruct-image') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-slate-300 uppercase mb-1.5 font-mono">
                                Sikke Fotoğrafı Yükle:
                            </label>
                            <input type="file" 
                                   name="image" 
                                   accept="image/*"
                                   required
                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-amber-400 hover:file:bg-slate-700 cursor-pointer">
                        </div>

                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-slate-300 uppercase mb-1.5 font-mono">
                                İncelenecek Yüz:
                            </label>
                            <select name="face" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2.5 text-xs text-slate-300 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="obverse">Ön Yüz (Avers)</option>
                                <option value="reverse">Arka Yüz (Revers)</option>
                                <option value="both">Her İki Yüzü Tara</option>
                            </select>
                        </div>

                        <button type="submit" class="w-full bg-slate-800 hover:bg-slate-700 text-emerald-400 border border-emerald-500/30 font-bold py-2.5 px-4 rounded-xl transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <span>Dairesel Polar Transform Uygula</span>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Düzleştirilmiş Şerit Gösterimi (Görsel Analizi Varsa) -->
    @if(isset($imageResult) && !empty($imageResult['unwrapped_strip_url']))
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 mb-10 shadow-xl">
            <h3 class="font-numis text-sm font-bold text-slate-200 mb-2 uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Düzleştirilmiş Dairesel Kenar Yazıtı (Polar Unwrapped Strip)
            </h3>
            <p class="text-xs text-slate-400 mb-4">
                Sikke çevresindeki dairesel eğrisel metin 360° radyal açıyla taranıp OCR için yatay şerit haline getirilmiştir:
            </p>
            <div class="bg-slate-950 p-2 rounded-xl border border-slate-800 overflow-x-auto">
                <img src="{{ $imageResult['unwrapped_strip_url'] }}" alt="Düzleştirilmiş Lejant Şeridi" class="h-16 max-w-none rounded">
            </div>
        </div>
    @endif

    <!-- SONUÇLAR BÖLÜMÜ -->
    @if(!empty($results))
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="font-numis text-xl font-bold text-slate-100 tracking-wider">
                    REKONSTRÜKSİYON SONUÇLARI ({{ count($results) }})
                </h2>
                <span class="text-xs text-slate-400 font-mono">Levenshtein & Regex Skoru</span>
            </div>

            @foreach($results as $index => $match)
                <div class="bg-slate-900 border {{ $index === 0 ? 'border-amber-500/50 ring-1 ring-amber-500/20' : 'border-slate-800' }} rounded-2xl p-6 shadow-xl transition">
                    
                    <!-- Üst Bilgi Satırı -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-800 mb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-full {{ $index === 0 ? 'bg-amber-500 text-slate-950' : 'bg-slate-800 text-slate-300' }} font-bold text-xs flex items-center justify-center font-mono">
                                #{{ $index + 1 }}
                            </span>
                            <div>
                                <a href="{{ route('coins.show', $match['coin_slug']) }}" class="font-bold text-slate-100 hover:text-amber-400 text-base transition">
                                    {{ $match['coin_title'] }}
                                </a>
                                <div class="flex items-center gap-2 text-xs text-slate-400 font-mono mt-0.5">
                                    <span>{{ $match['period'] }}</span>
                                    <span>•</span>
                                    <span>{{ $match['ruler'] }}</span>
                                    <span>•</span>
                                    <span class="text-amber-400/90">{{ $match['catalog_reference'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Güven Skoru Rozeti -->
                        <div class="flex items-center gap-2 self-start sm:self-auto">
                            <span class="text-xs text-slate-400">Eşleşme Oranı:</span>
                            <span class="px-3 py-1 rounded-lg text-xs font-bold font-mono {{ $match['confidence_score'] >= 85 ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' }}">
                                %{{ $match['confidence_score'] }}
                            </span>
                        </div>
                    </div>

                    <!-- Tamamlanan Görsel Geri Bildirim Formatı (Köşeli Parantezli Segmentasyon) -->
                    <div class="mb-4">
                        <span class="text-[11px] font-semibold uppercase tracking-wider text-amber-400 block mb-1">
                            Epigrafik Rekonstrüksiyon:
                        </span>
                        <div class="bg-slate-950 border border-slate-800 rounded-xl p-3.5 text-slate-100 font-mono text-sm leading-relaxed tracking-wider break-words selection:bg-amber-500/30">
                            {{ $match['formatted_feedback'] }}
                        </div>
                    </div>

                    <!-- Standart Orijinal Metin ve Türkçe Çözümlemesi -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div class="bg-slate-950/60 p-3 rounded-lg border border-slate-800/80">
                            <span class="text-slate-400 font-medium block mb-1">Tam Orijinal Lejant:</span>
                            <p class="font-legend text-slate-200 text-sm tracking-widest">{{ $match['original_legend'] }}</p>
                        </div>
                        <div class="bg-slate-950/60 p-3 rounded-lg border border-slate-800/80">
                            <span class="text-emerald-400 font-medium block mb-1">Türkçe Çeviri & Anlamı:</span>
                            <p class="text-slate-300 italic leading-relaxed">{{ $match['legend_translation'] ?? 'Çeviri bulunamadı.' }}</p>
                        </div>
                    </div>

                    <!-- Sikke Detay Bağlantısı -->
                    <div class="mt-4 pt-4 border-t border-slate-800/80 flex justify-end">
                        <a href="{{ route('coins.show', $match['coin_slug']) }}" class="text-xs text-amber-400 hover:text-amber-300 font-semibold flex items-center gap-1.5 transition">
                            <span>Sikke Kataloğunda İncele</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @elseif(!empty($query))
        <div class="bg-slate-900 border border-slate-800 rounded-2xl p-10 text-center text-slate-400">
            <svg class="w-10 h-10 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <h4 class="font-numis text-base font-bold text-slate-300 mb-1">Eşleşen Lejant Bulunamadı</h4>
            <p class="text-xs text-slate-500">
                "<span class="font-mono text-slate-300">{{ $query }}</span>" harf dizilimiyle eşleşen standart bir epigrafik kayıt tespit edilemedi. Daha az harfle veya farklı parçacıklarla aramayı deneyebilirsiniz.
            </p>
        </div>
    @endif

</div>
@endsection

