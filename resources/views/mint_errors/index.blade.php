@extends('layouts.app')

@section('title', 'Nümizmatik Darp Hataları Rehberi (Mint Errors)')
@section('meta_description', 'Antik ve tarihi sikkelerde merkezleme kayması (off-center), çift darp, ters baskı (brockage) ve kalıp çatlaklarının nümizmatik değeri ve prim çarpanları.')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <!-- Başlık ve Giriş -->
    <div class="text-center mb-10">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-500/10 text-red-400 border border-red-500/20 mb-3">
            <span>⚡</span> Kusur Değil, Koleksiyon Değeri
        </span>
        <h1 class="text-3xl font-extrabold text-white tracking-tight sm:text-5xl">
            Antik Sikke Darp Hataları Rehberi
        </h1>
        <p class="mt-4 text-slate-400 text-sm sm:text-base max-w-3xl mx-auto leading-relaxed">
            Türkiye'de sıklıkla <span class="text-red-400 font-semibold">"bozuk, hatalı veya çöp para"</span> sanılarak göz ardı edilen darp hataları (Mint Errors); uluslararası nümizmatik müzayedelerinde sikkenin değerini <span class="text-amber-400 font-bold">1.5x ile 10x katına kadar</span> çıkarır.
        </p>
    </div>

    <!-- Hızlı Özet İstatistik Şeridi -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-10">
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl text-center">
            <span class="text-xs text-slate-400 block mb-1">Off-Center Kayma</span>
            <span class="text-xl font-black text-amber-400 font-mono">1.5x - 4x</span>
            <span class="text-[11px] text-slate-500 block mt-1">%30-%50 kaymalar en değerli</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl text-center">
            <span class="text-xs text-slate-400 block mb-1">Double Die (Çift Darp)</span>
            <span class="text-xl font-black text-amber-400 font-mono">2x - 5x</span>
            <span class="text-[11px] text-slate-500 block mt-1">Belirgin çift kontur / ghosting</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl text-center">
            <span class="text-xs text-slate-400 block mb-1">Brockage (Ters Baskı)</span>
            <span class="text-xl font-black text-emerald-400 font-mono">5x - 10x</span>
            <span class="text-[11px] text-slate-500 block mt-1">Nadir ayna negatif vuruş</span>
        </div>
        <div class="bg-slate-900 border border-slate-800 p-4 rounded-xl text-center">
            <span class="text-xs text-slate-400 block mb-1">Overstrike (Yeniden Basım)</span>
            <span class="text-xl font-black text-amber-400 font-mono">3x - 8x</span>
            <span class="text-[11px] text-slate-500 block mt-1">Eski imparator sikkesi üstü</span>
        </div>
    </div>

    <!-- Hata Türleri Eğitim Kartları Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- 1. Off-Center Strike (Merkezleme Kayması) -->
        <div class="bg-slate-900 border border-slate-800 hover:border-amber-500/50 rounded-2xl p-6 transition duration-300 flex flex-col justify-between group shadow-xl">
            <div>
                <!-- Görsel Animasyonlu Simülasyon Kartı -->
                <div class="h-44 bg-slate-950 rounded-xl border border-slate-800/80 mb-5 relative flex items-center justify-center overflow-hidden">
                    <!-- Boş Pul (Flan) -->
                    <div class="w-28 h-28 rounded-full bg-gradient-to-tr from-amber-700/60 to-amber-900/40 border-2 border-amber-600/40 relative shadow-inner">
                        <!-- Kayık Darp İzi (Off-Center Impression) -->
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 border border-white/60 absolute -right-4 -top-3 shadow-lg flex items-center justify-center transform group-hover:translate-x-1 group-hover:-translate-y-1 transition duration-500">
                            <span class="text-2xl font-serif text-slate-950 font-black opacity-80">AVG</span>
                        </div>
                        <!-- Açıkta Kalan Hilal Pul -->
                        <div class="absolute bottom-2 left-2 text-[10px] text-amber-400/80 font-mono bg-black/60 px-1.5 py-0.5 rounded">
                            %35 Boş Pul
                        </div>
                    </div>
                    <span class="absolute top-2 right-2 text-[10px] bg-amber-500/20 text-amber-300 px-2 py-0.5 rounded border border-amber-500/30 font-mono">
                        1.5x - 4x Çarpan
                    </span>
                </div>

                <div class="flex items-center gap-2 mb-2">
                    <span class="text-base text-amber-400">⚡</span>
                    <h3 class="font-bold text-lg text-white">Off-Center Strike</h3>
                </div>
                <div class="text-xs text-amber-300 font-semibold mb-3">Merkezleme Kayması Hatası</div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Sikke pulunun darp esnasında alt ve üst kalıpların arasına tam ortalanmadan konulması sonucu oluşur. Sikke tasarımının bir kısmı dışarı taşar, boş pul yüzeyi hilal şeklinde açıkta kalır. Tasarımın okunduğu %30-%50 kaymalar koleksiyonerler arasında en yüksek primi yapar.
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-500">
                <span>Yaygınlık: <strong class="text-slate-300">Orta</strong></span>
                <span class="text-emerald-400 font-bold">+%150 - %300 Değer</span>
            </div>
        </div>

        <!-- 2. Double Die / Double Strike (Çift Darp) -->
        <div class="bg-slate-900 border border-slate-800 hover:border-amber-500/50 rounded-2xl p-6 transition duration-300 flex flex-col justify-between group shadow-xl">
            <div>
                <div class="h-44 bg-slate-950 rounded-xl border border-slate-800/80 mb-5 relative flex items-center justify-center overflow-hidden">
                    <div class="w-28 h-28 rounded-full bg-gradient-to-tr from-slate-700 to-slate-800 border-2 border-slate-600 relative shadow-inner flex items-center justify-center">
                        <!-- Çift Darp Gölge Animasyonu -->
                        <div class="absolute inset-0 flex items-center justify-center opacity-40 translate-x-1 -translate-y-1 group-hover:translate-x-2 group-hover:-translate-y-2 transition duration-300">
                            <span class="text-4xl text-amber-400 font-black font-serif">TRAIAN</span>
                        </div>
                        <div class="relative flex items-center justify-center">
                            <span class="text-4xl text-slate-200 font-black font-serif">TRAIAN</span>
                        </div>
                    </div>
                    <span class="absolute top-2 right-2 text-[10px] bg-amber-500/20 text-amber-300 px-2 py-0.5 rounded border border-amber-500/30 font-mono">
                        2x - 5x Çarpan
                    </span>
                </div>

                <div class="flex items-center gap-2 mb-2">
                    <span class="text-base text-amber-400">⚡</span>
                    <h3 class="font-bold text-lg text-white">Double Die</h3>
                </div>
                <div class="text-xs text-amber-300 font-semibold mb-3">Çift Darp / İki Kez Vuruş</div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Sikkenin ilk darbe sonrası kalıptan fırlamaması veya hafif kayarak ikinci kez çekiç darbesi alması sonucu meydana gelir. Harflerde, portre hatlarında veya lejantlarda gölge gibi belirgin çift kontur (ghosting) ve ayrık kabartma çizgileri görülür.
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-500">
                <span>Yaygınlık: <strong class="text-slate-300">Nadir</strong></span>
                <span class="text-emerald-400 font-bold">+%200 - %400 Değer</span>
            </div>
        </div>

        <!-- 3. Brockage (Ters Baskı / Ayna Hatası) -->
        <div class="bg-slate-900 border border-slate-800 hover:border-emerald-500/50 rounded-2xl p-6 transition duration-300 flex flex-col justify-between group shadow-xl">
            <div>
                <div class="h-44 bg-slate-950 rounded-xl border border-slate-800/80 mb-5 relative flex items-center justify-center overflow-hidden">
                    <div class="w-28 h-28 rounded-full bg-gradient-to-tr from-slate-800 to-slate-950 border-2 border-emerald-500/40 relative shadow-inner flex items-center justify-center group-hover:scale-105 transition duration-500">
                        <!-- Çukur Ayna Negatifi (Incuse Mirror) -->
                        <div class="w-20 h-20 rounded-full border border-dashed border-emerald-400/50 flex items-center justify-center bg-black/60 shadow-[inset_0_4px_10px_rgba(0,0,0,0.9)]">
                            <span class="text-3xl text-emerald-400/80 font-black transform scale-x-[-1] font-serif">🏛️</span>
                        </div>
                    </div>
                    <span class="absolute top-2 right-2 text-[10px] bg-emerald-500/20 text-emerald-300 px-2 py-0.5 rounded border border-emerald-500/30 font-mono font-bold">
                        5x - 10x Çarpan (Ultra Nadir)
                    </span>
                </div>

                <div class="flex items-center gap-2 mb-2">
                    <span class="text-base text-emerald-400">💎</span>
                    <h3 class="font-bold text-lg text-white">Brockage</h3>
                </div>
                <div class="text-xs text-emerald-300 font-semibold mb-3">Ters Baskı / Çukur Ayna Hatası</div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Darp edilen önceki sikke üst kalıba yapışık kaldığında, yeni konulan boş pula baskı yapar. Yeni sikkenin bir yüzü normal kabartma iken, diğer yüzü yapışan sikkenin çukur/negatif ayna görüntüsü (incuse) şeklinde basılır. En nadir ve en yüksek değer çarpanına sahip antik darp hatasıdır.
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-500">
                <span>Yaygınlık: <strong class="text-emerald-400 font-bold">Çok Nadir</strong></span>
                <span class="text-emerald-400 font-bold">+%500 - %1000 Değer</span>
            </div>
        </div>

        <!-- 4. Die Crack / Cud (Kalıp Çatlağı) -->
        <div class="bg-slate-900 border border-slate-800 hover:border-amber-500/50 rounded-2xl p-6 transition duration-300 flex flex-col justify-between group shadow-xl">
            <div>
                <div class="h-44 bg-slate-950 rounded-xl border border-slate-800/80 mb-5 relative flex items-center justify-center overflow-hidden">
                    <div class="w-28 h-28 rounded-full bg-gradient-to-tr from-amber-900/40 to-slate-800 border-2 border-slate-600 relative shadow-inner flex items-center justify-center">
                        <span class="text-4xl text-slate-400 opacity-60">👑</span>
                        <!-- Çatlak Çizgisi (SVG Lightning Die Crack) -->
                        <svg class="absolute inset-0 w-full h-full pointer-events-none group-hover:stroke-amber-300 transition duration-300" stroke="#f59e0b" stroke-width="2.5" fill="none">
                            <path d="M 15 35 L 45 60 L 65 52 L 95 85 L 110 95" stroke-dasharray="1 0" />
                        </svg>
                    </div>
                    <span class="absolute top-2 right-2 text-[10px] bg-amber-500/20 text-amber-300 px-2 py-0.5 rounded border border-amber-500/30 font-mono">
                        1.3x - 2.5x Çarpan
                    </span>
                </div>

                <div class="flex items-center gap-2 mb-2">
                    <span class="text-base text-amber-400">⚡</span>
                    <h3 class="font-bold text-lg text-white">Die Crack & Cud</h3>
                </div>
                <div class="text-xs text-amber-300 font-semibold mb-3">Kalıp Çatlağı ve Kenar Kırığı</div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Binlerce sikke darp edildikten sonra demir veya bronz kalıbın aşırı yorulması ve çatlaması sonucu oluşur. Isıtılmış metal çatlağın içine dolarak sikke yüzeyinde belirgin kabartı şerit (die crack) veya kenarda dolgu kütlesi (cud) bırakır.
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-500">
                <span>Yaygınlık: <strong class="text-slate-300">Sık</strong></span>
                <span class="text-emerald-400 font-bold">+%30 - %150 Değer</span>
            </div>
        </div>

        <!-- 5. Overstrike (Üst Üste Darp) -->
        <div class="bg-slate-900 border border-slate-800 hover:border-amber-500/50 rounded-2xl p-6 transition duration-300 flex flex-col justify-between group shadow-xl">
            <div>
                <div class="h-44 bg-slate-950 rounded-xl border border-slate-800/80 mb-5 relative flex items-center justify-center overflow-hidden">
                    <div class="w-28 h-28 rounded-full bg-gradient-to-tr from-slate-800 to-amber-950/60 border-2 border-slate-600 relative shadow-inner flex items-center justify-center">
                        <!-- Alttaki Sikke İzi (Silik Grek Yazısı) -->
                        <span class="absolute text-[11px] font-mono text-amber-500/40 rotate-45 transform">BASILEOS SELEUKOY</span>
                        <!-- Üstteki Yeni Roma Darpı -->
                        <span class="relative text-3xl text-slate-100 font-bold">IMP</span>
                    </div>
                    <span class="absolute top-2 right-2 text-[10px] bg-amber-500/20 text-amber-300 px-2 py-0.5 rounded border border-amber-500/30 font-mono">
                        3x - 8x Çarpan
                    </span>
                </div>

                <div class="flex items-center gap-2 mb-2">
                    <span class="text-base text-amber-400">⚡</span>
                    <h3 class="font-bold text-lg text-white">Overstrike</h3>
                </div>
                <div class="text-xs text-amber-300 font-semibold mb-3">Yeniden Basım / Üst Üste Darp</div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Savaş veya ekonomik krizlerde pul eritmek yerine düşman devletin veya önceki hükümdarın tedavüldeki sikkesi üzerine doğrudan yeni kalıpla baskı yapılmasıdır. Alttaki sikkenin lejantının kısmen okunabilmesi muazzam tarihsel belge değeri taşır.
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-500">
                <span>Yaygınlık: <strong class="text-slate-300">Nadir</strong></span>
                <span class="text-emerald-400 font-bold">+%200 - %700 Değer</span>
            </div>
        </div>

        <!-- 6. Clipped Planchet (Eksik Pul) -->
        <div class="bg-slate-900 border border-slate-800 hover:border-amber-500/50 rounded-2xl p-6 transition duration-300 flex flex-col justify-between group shadow-xl">
            <div>
                <div class="h-44 bg-slate-950 rounded-xl border border-slate-800/80 mb-5 relative flex items-center justify-center overflow-hidden">
                    <div class="w-28 h-28 rounded-full bg-gradient-to-tr from-slate-700 to-slate-900 border-2 border-slate-600 relative shadow-inner flex items-center justify-center">
                        <span class="text-3xl text-slate-300">🪙</span>
                        <!-- Kesik Isırık (Clipped Crescent) -->
                        <div class="absolute -right-3 -top-3 w-12 h-12 rounded-full bg-slate-950 border border-slate-800"></div>
                    </div>
                    <span class="absolute top-2 right-2 text-[10px] bg-amber-500/20 text-amber-300 px-2 py-0.5 rounded border border-amber-500/30 font-mono">
                        1.5x - 3x Çarpan
                    </span>
                </div>

                <div class="flex items-center gap-2 mb-2">
                    <span class="text-base text-amber-400">⚡</span>
                    <h3 class="font-bold text-lg text-white">Clipped Planchet</h3>
                </div>
                <div class="text-xs text-amber-300 font-semibold mb-3">Eksik / Kesik Pul Hatası</div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Döküm metal şeritten yuvarlak pullar kesilirken delgi bıçağının önceki kesimle üst üste binmesi sonucu pulun hilal şeklinde eksik kalmasıdır. Orijinal darp anında eksik olduğunu kanıtlayan Blakesley kenar basıklığı etkisi taşır.
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-500">
                <span>Yaygınlık: <strong class="text-slate-300">Orta</strong></span>
                <span class="text-emerald-400 font-bold">+%50 - %200 Değer</span>
            </div>
        </div>
    </div>

    <!-- Yapay Zeka Hata Tespiti Bilgilendirme Kutusu -->
    <div class="mt-12 bg-gradient-to-r from-amber-950/40 via-slate-900 to-slate-900 border border-amber-500/30 rounded-2xl p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-300 border border-amber-500/20 mb-3">
                <span>🤖</span> Otomatik Yapay Zeka Taraması
            </div>
            <h2 class="text-xl sm:text-2xl font-bold text-white mb-2">
                Sikkenizde Darp Hatası Olup Olmadığını Biliyor musunuz?
            </h2>
            <p class="text-sm text-slate-400 max-w-xl">
                Yapay zeka görsel tarama motorumuz (<span class="font-mono text-amber-400">/api/v1/coins/identify</span>), yüklediğiniz fotoğraflarda merkez kayması veya brockage tespit ettiğinde güven skoruyla birlikte raporlar.
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 shrink-0">
            <a href="{{ route('coins.index') }}" class="px-5 py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl text-sm transition shadow-lg shadow-amber-500/20 text-center">
                Kataloğu İncele
            </a>
            <a href="{{ route('collection.index') }}" class="px-5 py-3 bg-slate-800 hover:bg-slate-700 text-white font-semibold rounded-xl text-sm transition border border-slate-700 text-center">
                Envanterime Sikke Ekle
            </a>
        </div>
    </div>
</div>
@endsection

