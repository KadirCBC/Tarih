<?php $__env->startSection('title', $coin->title . ' - Nümizmatik Katalog Referansı'); ?>
<?php $__env->startSection('meta_description', Str::limit(strip_tags($coin->historical_notes ?? ($coin->denomination . ' - ' . $coin->period?->name . ' dönemi sikkesi. Ön lejant: ' . $coin->obverse_legend)), 155)); ?>

<?php $__env->startSection('og_image'); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($coin->primaryImage): ?>
        <meta property="og:image" content="<?php echo e($coin->primaryImage->url); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('schema_org'); ?>
    <!-- JSON-LD Dinamik Schema Markup (VisualArtifact & BreadcrumbList) -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@graph": [
            {
                "@type": "VisualArtifact",
                "@id": "<?php echo e(url()->current()); ?>#artifact",
                "name": "<?php echo e($coin->title); ?>",
                "headline": "<?php echo e($coin->title); ?>",
                "description": "<?php echo e(addslashes(Str::limit(strip_tags($coin->historical_notes ?? $coin->obverse_desc), 250))); ?>",
                "artform": "Coin",
                "material": "<?php echo e($coin->metal->label()); ?>",
                "depth": "<?php echo e($coin->diameter_mm); ?> mm",
                "weight": "<?php echo e($coin->weight_g); ?> g",
                "accessMode": "visual",
                <?php if($coin->primaryImage): ?>
                "image": "<?php echo e($coin->primaryImage->url); ?>",
                <?php endif; ?>
                "temporalCoverage": "<?php echo e($coin->period?->formatted_date_range); ?>",
                "creator": {
                    "@type": "Person",
                    "name": "<?php echo e($coin->ruler?->name ?? 'Bilinmeyen Darbedici'); ?>"
                },
                "locationCreated": {
                    "@type": "Place",
                    "name": "<?php echo e($coin->mint?->name ?? 'Antik Darphane'); ?>"
                },
                "identifier": "<?php echo e($coin->catalog_reference); ?>",
                "inLanguage": "tr"
            },
            {
                "@type": "BreadcrumbList",
                "@id": "<?php echo e(url()->current()); ?>#breadcrumb",
                "itemListElement": [
                    {
                        "@type": "ListItem",
                        "position": 1,
                        "name": "Katalog",
                        "item": "<?php echo e(route('coins.index')); ?>"
                    },
                    {
                        "@type": "ListItem",
                        "position": 2,
                        "name": "<?php echo e($coin->period?->name ?? 'Dönem'); ?>",
                        "item": "<?php echo e($coin->period ? route('periods.show', $coin->period->slug) : '#'); ?>"
                    },
                    {
                        "@type": "ListItem",
                        "position": 3,
                        "name": "<?php echo e($coin->title); ?>"
                    }
                ]
            }
        ]
    }
    </script>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($nomismaJsonLd)): ?>
    <!-- Nomisma.org Linked Open Data RDF/JSON-LD Standardı (American Numismatic Society & British Museum) -->
    <script type="application/ld+json">
    <?php echo json_encode($nomismaJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>

    </script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $obverseImg = $coin->images->firstWhere('image_type.value', 'obverse') ?? $coin->primaryImage;
    $reverseImg = $coin->images->firstWhere('image_type.value', 'reverse') ?? $coin->images->firstWhere('id', '!=', $obverseImg?->id) ?? $coin->primaryImage;
    $obverseUrl = $obverseImg?->url ?? asset('images/coin-placeholder.webp');
    $reverseUrl = $reverseImg?->url ?? asset('images/coin-placeholder.webp');
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Ekmek Kırıntısı (Breadcrumbs) -->
    <nav class="flex items-center text-xs text-slate-400 mb-6 space-x-2">
        <a href="<?php echo e(route('home')); ?>" class="hover:text-gold-antique transition">Ana Sayfa</a>
        <span>/</span>
        <a href="<?php echo e(route('coins.index')); ?>" class="hover:text-gold-antique transition">Katalog</a>
        <span>/</span>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($coin->period): ?>
            <a href="<?php echo e(route('periods.show', $coin->period->slug)); ?>" class="hover:text-gold-antique transition"><?php echo e($coin->period->name); ?></a>
            <span>/</span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <span class="text-slate-200 truncate max-w-xs"><?php echo e($coin->title); ?></span>
    </nav>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('concordance_info')): ?>
        <div class="bg-emerald-950/80 border border-emerald-500/50 rounded-xl p-4 mb-6 shadow-lg flex items-center gap-3 text-emerald-200 text-sm">
            <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span><strong>Akıllı Katalog Çözümleyici:</strong> <?php echo e(session('concordance_info')); ?></span>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- 1. YASAL UYARI ROZETİ (2863 Sayılı Kanun Uyarısı) -->
    <div class="bg-gradient-to-r from-stone-900 via-stone-950 to-stone-900 border border-amber-500/30 rounded-xl p-4 mb-8 shadow-lg flex items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-amber-500/10 border border-amber-500/30 flex items-center justify-center shrink-0 text-gold-antique">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd" />
                </svg>
            </div>
            <div>
                <h4 class="text-xs font-bold text-gold-antique uppercase tracking-wider">Kültürel Miras & Akademik Referans Bildirimi</h4>
                <p class="text-xs text-slate-300">
                    Bu sayfa <strong>2863 sayılı Kültür ve Tabiat Varlıklarını Koruma Kanunu</strong> kapsamında akademik ve eğitim amaçlı nümizmatik arşiv kaydıdır. <strong>Alım-satımı kesinlikle yapılamaz.</strong>
                </p>
            </div>
        </div>
        <span class="hidden sm:inline-block bg-amber-500/10 text-gold-antique border border-amber-500/30 text-[11px] font-mono font-semibold px-3 py-1 rounded-md shrink-0">
            Arşiv Kaydı #<?php echo e(str_pad($coin->id, 6, '0', STR_PAD_LEFT)); ?>

        </span>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. GÖRSEL & İNTERAKTİF SİKKE ÇEVİRİCİ + KOMPAKT ÖZELLİKLER (ALPINE.JS)     -->
    <!-- ========================================================================= -->
    <div 
        x-data="{
            activeFace: 'obverse',
            isFlipping: false,
            zoomActive: false,
            zoomX: 50,
            zoomY: 50,
            flipCoin() {
                this.isFlipping = true;
                setTimeout(() => {
                    this.activeFace = this.activeFace === 'obverse' ? 'reverse' : 'obverse';
                    this.isFlipping = false;
                }, 180);
            },
            handleMouseMove(e) {
                const rect = e.currentTarget.getBoundingClientRect();
                this.zoomX = Math.max(0, Math.min(100, ((e.clientX - rect.left) / rect.width) * 100));
                this.zoomY = Math.max(0, Math.min(100, ((e.clientY - rect.top) / rect.height) * 100));
            }
        }"
        class="bg-stone-900 border border-slate-800 rounded-3xl p-6 sm:p-8 mb-12 shadow-2xl relative overflow-hidden"
    >
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <!-- SOL KOLON: İNTERAKTİF SİKKE ÇEVİRİCİ & BÜYÜTEÇLİ SAHNE (7 Kolon) -->
            <div class="lg:col-span-7 flex flex-col items-center">
                
                <!-- Üst Kontrol Sekmeleri & Çevirme Butonu -->
                <div class="w-full flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
                    <div class="flex items-center gap-2">
                        <button 
                            type="button"
                            @click="activeFace = 'obverse'"
                            :class="activeFace === 'obverse' ? 'bg-amber-500/20 text-gold-antique border-amber-500/50 shadow-sm' : 'bg-stone-950 text-slate-400 border-slate-800 hover:text-slate-200'"
                            class="px-3.5 py-1.5 rounded-xl border text-xs font-semibold font-numis tracking-wider transition duration-150"
                        >
                            Ön Yüz (Avers)
                        </button>
                        <button 
                            type="button"
                            @click="activeFace = 'reverse'"
                            :class="activeFace === 'reverse' ? 'bg-amber-500/20 text-gold-antique border-amber-500/50 shadow-sm' : 'bg-stone-950 text-slate-400 border-slate-800 hover:text-slate-200'"
                            class="px-3.5 py-1.5 rounded-xl border text-xs font-semibold font-numis tracking-wider transition duration-150"
                        >
                            Arka Yüz (Revers)
                        </button>
                    </div>

                    <!-- 3D Çevirme Butonu -->
                    <button 
                        type="button" 
                        @click="flipCoin()"
                        class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-amber-600/30 to-amber-500/20 hover:from-amber-600/50 hover:to-amber-500/40 border border-amber-500/40 text-gold-antique text-xs font-bold flex items-center gap-1.5 transition shadow-lg"
                        title="Sikkenin diğer yüzünü görmek için çevirin"
                    >
                        <span class="inline-block transition-transform duration-300" :class="{'rotate-180': isFlipping}">🔄</span>
                        <span>Sikkeyi Çevir</span>
                    </button>
                </div>

                <!-- İnteraktif Büyüteçli Darp Alanı (Coin Canvas) -->
                <div 
                    class="relative w-full max-w-md aspect-square bg-stone-950/90 rounded-3xl border border-slate-800/90 p-8 flex items-center justify-center shadow-inner overflow-hidden cursor-crosshair group select-none"
                    @mousemove="handleMouseMove($event)"
                    @mouseenter="zoomActive = true"
                    @mouseleave="zoomActive = false"
                >
                    <!-- Arka Işık Efekti -->
                    <div class="absolute inset-0 bg-radial-gradient from-amber-500/10 via-transparent to-transparent pointer-events-none"></div>

                    <!-- Sikke Görseli (Flip & Magnifier Zoom Efekti) -->
                    <div 
                        class="relative w-full h-full flex items-center justify-center transition-transform duration-200 ease-out"
                        :class="{'scale-95 opacity-50': isFlipping}"
                    >
                        <!-- Ön Yüz -->
                        <img 
                            x-show="activeFace === 'obverse'"
                            src="<?php echo e($obverseUrl); ?>" 
                            alt="<?php echo e($coin->title); ?> - Ön Yüz" 
                            class="max-h-full max-w-full object-contain rounded-full shadow-2xl transition-transform duration-75 ease-out filter drop-shadow-[0_15px_25px_rgba(0,0,0,0.8)]"
                            :style="zoomActive ? `transform: scale(2.4); transform-origin: ${zoomX}% ${zoomY}%;` : 'transform: scale(1);'"
                        >

                        <!-- Arka Yüz -->
                        <img 
                            x-show="activeFace === 'reverse'"
                            x-cloak
                            src="<?php echo e($reverseUrl); ?>" 
                            alt="<?php echo e($coin->title); ?> - Arka Yüz" 
                            class="max-h-full max-w-full object-contain rounded-full shadow-2xl transition-transform duration-75 ease-out filter drop-shadow-[0_15px_25px_rgba(0,0,0,0.8)]"
                            :style="zoomActive ? `transform: scale(2.4); transform-origin: ${zoomX}% ${zoomY}%;` : 'transform: scale(1);'"
                        >
                    </div>

                    <!-- Büyüteç İpucu Rozeti -->
                    <div 
                        x-show="!zoomActive" 
                        class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-stone-900/90 border border-slate-700/80 px-3 py-1 rounded-full text-[11px] text-slate-300 font-medium flex items-center gap-1.5 shadow-xl pointer-events-none transition duration-200"
                    >
                        <span>🔍</span>
                        <span>Detaylı patina ve kılcal çatlaklar için fareyle üzerine gelin</span>
                    </div>

                    <!-- Aktif Yüz Bilgisi -->
                    <div class="absolute top-4 left-4 bg-stone-900/80 border border-slate-800 text-[10px] font-mono text-gold-antique uppercase px-2 py-0.5 rounded-md">
                        <span x-text="activeFace === 'obverse' ? 'Avers (Ön Yüz)' : 'Revers (Arka Yüz)'"></span>
                    </div>

                    <!-- Deep Zoom Modal Butonu -->
                    <button 
                        type="button" 
                        @click="openDeepZoom(activeFace === 'obverse' ? '<?php echo e($obverseUrl); ?>' : '<?php echo e($reverseUrl); ?>', activeFace === 'obverse' ? '<?php echo e($obverseImg?->id ?? 1); ?>' : '<?php echo e($reverseImg?->id ?? 2); ?>', activeFace === 'obverse' ? 'Ön Yüz (Avers)' : 'Arka Yüz (Revers)')"
                        class="absolute top-4 right-4 bg-stone-900/80 hover:bg-gold-antique hover:text-stone-950 text-slate-300 border border-slate-700 text-[11px] font-semibold px-2.5 py-1 rounded-lg transition shadow-lg flex items-center gap-1"
                        title="IIIF OpenSeadragon ile mikroskobik inceleme"
                    >
                        <span>🔬</span>
                        <span class="hidden sm:inline">IIIF Deep Zoom</span>
                    </button>
                </div>
            </div>

            <!-- SAĞ KOLON: BAŞLIK, NADİRLİK & KOMPAKT ÖZELLİKLER TABLOSU (5 Kolon) -->
            <div class="lg:col-span-5 flex flex-col justify-between h-full">
                <div>
                    <!-- Metal, Nominal ve Nadirlik Rozetleri -->
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="bg-amber-500/15 text-gold-antique border border-amber-500/30 text-xs font-bold px-2.5 py-1 rounded-lg">
                            <?php echo e($coin->metal->label()); ?>

                        </span>
                        <span class="bg-stone-950 text-slate-200 border border-slate-800 text-xs font-mono px-2.5 py-1 rounded-lg">
                            <?php echo e($coin->denomination); ?>

                        </span>
                        <span class="bg-purple-950/80 border border-purple-500/40 text-purple-300 font-mono text-xs font-bold px-2.5 py-1 rounded-lg">
                            Nadirlik: <?php echo e($coin->rarity_grade->value); ?> (<?php echo e($coin->rarity_grade->label()); ?>)
                        </span>
                    </div>

                    <!-- Başlık -->
                    <h1 class="font-numis text-2xl sm:text-3xl font-extrabold text-slate-100 tracking-wide mb-2 leading-tight">
                        <?php echo e($coin->title); ?>

                    </h1>

                    <!-- Katalog Referansı -->
                    <p class="text-xs text-slate-400 font-mono mb-6">
                        Standart Katalog No: <span class="text-gold-antique font-bold text-sm"><?php echo e($coin->catalog_reference); ?></span>
                    </p>

                    <!-- KOMPAKT ÖZELLİKLER TABLOSU (Antik Müze Envanteri) -->
                    <div class="bg-stone-950/80 border border-slate-800 rounded-2xl overflow-hidden divide-y divide-slate-800/80 text-xs mb-6">
                        <div class="flex items-center justify-between p-3">
                            <span class="text-slate-400 font-medium">🏛️ Dönem / Uygarlık:</span>
                            <span class="font-semibold text-slate-100 text-right">
                                <?php echo e($coin->period?->name ?? '—'); ?>

                                <span class="text-[10px] text-slate-500 block font-mono"><?php echo e($coin->period?->formatted_date_range); ?></span>
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3">
                            <span class="text-slate-400 font-medium">👑 Hükümdar / Otorite:</span>
                            <span class="font-bold text-gold-antique text-right">
                                <?php echo e($coin->ruler?->name ?? 'Senato / Şehir Devleti'); ?>

                                <span class="text-[10px] text-slate-400 block font-sans"><?php echo e($coin->ruler?->title); ?></span>
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3">
                            <span class="text-slate-400 font-medium">📍 Antik Darphane:</span>
                            <span class="font-semibold text-slate-200 text-right flex items-center gap-1.5">
                                <?php echo e($coin->mint?->name ?? 'Bilinmiyor'); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($coin->mint): ?>
                                    <a href="<?php echo e(route('map.index', ['mint' => $coin->mint->slug])); ?>" class="text-[11px] text-gold-antique hover:underline" title="Haritada Konumu İncele">
                                        (Harita ↗)
                                    </a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3">
                            <span class="text-slate-400 font-medium">⚖️ Ağırlık & Çap:</span>
                            <span class="font-bold text-slate-100 font-mono text-right">
                                <?php echo e($coin->weight_g ? $coin->weight_g . ' g' : '—'); ?> / <?php echo e($coin->diameter_mm ? $coin->diameter_mm . ' mm' : '—'); ?>

                            </span>
                        </div>

                        <div class="flex items-center justify-between p-3">
                            <span class="text-slate-400 font-medium">🔄 Kalıp Ekseni (Die Axis):</span>
                            <span class="font-mono text-slate-200 text-right">
                                <?php echo e($coin->die_axis ?? "12:00 (↑↑ Standart Darp)"); ?>

                            </span>
                        </div>
                    </div>
                </div>

                <!-- Şahsi Koleksiyon ve Eylem Butonları -->
                <div class="flex flex-col sm:flex-row items-center gap-3 pt-2">
                    <a 
                        href="#collection-section" 
                        class="w-full sm:flex-1 py-3 px-4 bg-gold-antique hover:bg-gold-hover text-stone-950 font-bold text-xs rounded-xl transition duration-150 shadow-lg shadow-amber-500/20 text-center flex items-center justify-center gap-1.5"
                    >
                        <span>📖</span>
                        <span>Sanal Koleksiyonuma Ekle</span>
                    </a>

                    <a 
                        href="#deviationCalculator" 
                        class="w-full sm:w-auto py-3 px-4 bg-stone-950 hover:bg-stone-800 text-slate-300 hover:text-white border border-slate-700 font-semibold text-xs rounded-xl transition duration-150 text-center flex items-center justify-center gap-1.5"
                    >
                        <span>⚖️</span>
                        <span>Sahte & Sapma Analizi</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. LEJANT VE EPİGRAFİ KARTI (KELİME KELİME AÇILAN MORFOLOJİK ÇÖZÜMLEME)    -->
    <!-- ========================================================================= -->
    <?php
        // Nümizmatik Morfolojik Terim Sözlüğü
        $numismaticGlossary = [
            'IMP' => ['title' => 'Imperator', 'meaning' => 'Başkomutan, muzaffer Roma generali ve imparatorluk yüce unvanı.'],
            'CAES' => ['title' => 'Caesar', 'meaning' => 'Julius Caesar\'a atıfla veliaht veya hüküm süren imparator hanedan unvanı.'],
            'NERVA' => ['title' => 'Nerva', 'meaning' => 'İmparator Nerva (Trajan\'ın manevi babası ve selefi).'],
            'TRAIAN' => ['title' => 'Traianus', 'meaning' => 'İmparator Marcus Ulpius Traianus (Optimus Princeps).'],
            'TRAIANVS' => ['title' => 'Traianus', 'meaning' => 'İmparator Trajan\'ın yalın Latince adı.'],
            'TRAIANO' => ['title' => 'Traiano', 'meaning' => 'Trajan\'a ithafen / Trajan için (Datif form).'],
            'AVG' => ['title' => 'Augustus', 'meaning' => 'Kutsal, ulu ve saygıdeğer; Roma hükümdarının mutlak egemenlik unvanı.'],
            'GERM' => ['title' => 'Germanicus', 'meaning' => 'Germania kavimlerine karşı kazanılan zaferin şeref unvanı.'],
            'GER' => ['title' => 'Germanicus', 'meaning' => 'Germanya fatihi şeref sıfatı.'],
            'DAC' => ['title' => 'Dacicus', 'meaning' => 'Daçya (Dacia) savaşları fatihi şerefine verilen zafer unvanı.'],
            'PARTH' => ['title' => 'Parthicus', 'meaning' => 'Part İmparatorluğu seferi zafer unvanı.'],
            'P M' => ['title' => 'Pontifex Maximus', 'meaning' => 'Roma\'nın en yüksek dini makamı (Başrahip / Kolej Başkanı).'],
            'TR P' => ['title' => 'Tribunicia Potestate', 'meaning' => 'Roma halkını koruma tribünlük yetkisi (Her yıl 10 Aralık\'ta yenilenir).'],
            'COS' => ['title' => 'Consul', 'meaning' => 'Roma Cumhuriyeti\'nin en üst yürütme makamı olan Konsüllük sayısı.'],
            'P P' => ['title' => 'Pater Patriae', 'meaning' => 'Vatanın Babası; Roma Senatosu tarafından bahşedilen en yüksek onur unvanı.'],
            'S C' => ['title' => 'Senatus Consulto', 'meaning' => 'Senato Kararı ile; bronz ve orikalkum sikkelerde basım izni damgası.'],
            'OPTIMO' => ['title' => 'Optimo', 'meaning' => 'En iyiye, en kusursuza ithafen (Optimus Princeps).'],
            'PRINCIPI' => ['title' => 'Principi', 'meaning' => 'Roma yurttaşlarının ilkine / liderine (Princeps).'],
            'FEL' => ['title' => 'Felix', 'meaning' => 'Kutlu, talihli, bahtiyar.'],
            'INV' => ['title' => 'Invictus', 'meaning' => 'Yenilmez, mağlup edilemez.'],
            'DIVVS' => ['title' => 'Divus', 'meaning' => 'Ölümünden sonra Senato kararıyla tanrılaştırılmış imparator.'],
            'PROVIDENTIA' => ['title' => 'Providentia', 'meaning' => 'Tanrısal öngörü ve devlet basireti kişileştirmesi.'],
            'PAX' => ['title' => 'Pax', 'meaning' => 'Roma Barışı tanrıçası; zeytin dalı ve bereket boynuzu taşır.'],
            'VICTORIA' => ['title' => 'Victoria', 'meaning' => 'Roma Zafer Tanrıçası (Grek dengi: Nike); çelenk ve palmiye dalı tutar.'],
            'VIRTVS' => ['title' => 'Virtus', 'meaning' => 'Askeri erdem, cesaret ve kahramanlık simgesi.'],
            'SALVS' => ['title' => 'Salus', 'meaning' => 'Halkın ve imparatorun sağlık, esenlik tanrıçası.'],
            'CONCORDIA' => ['title' => 'Concordia', 'meaning' => 'İmparatorluk içinde uyum, düzen ve evlilik birliği.'],
            'BASILEOS' => ['title' => 'ΒΑΣΙΛΕΩΣ', 'meaning' => 'Grekçe: Kralın / Bağımsız hükümdarın sikkesi.'],
            'ALEXANDROY' => ['title' => 'ΑΛΕΞΑΝΔΡΟΥ', 'meaning' => 'Grekçe: Büyük İskender\'in adına darp edilmiştir.'],
        ];

        // Ön Yüz Lejantını kelimelere böl
        $rawObverseWords = preg_split('/\s+/', trim($coin->obverse_legend ?? ''));
        $obverseTokens = [];
        foreach ($rawObverseWords as $word) {
            $cleanWord = strtoupper(trim(preg_replace('/[^\p{L}\p{N}]+/u', '', $word)));
            if (!empty($cleanWord)) {
                $info = $numismaticGlossary[$cleanWord] ?? [
                    'title' => $cleanWord,
                    'meaning' => 'Antik lejant unvanı veya imparatorluk tanımlama kelimesi.'
                ];
                $obverseTokens[] = [
                    'token' => $cleanWord,
                    'title' => $info['title'],
                    'meaning' => $info['meaning']
                ];
            }
        }
    ?>

    <div 
        x-data="{
            selectedToken: null,
            selectToken(t) {
                this.selectedToken = (this.selectedToken === t) ? null : t;
            }
        }"
        class="bg-stone-900 border border-slate-800 rounded-3xl p-6 sm:p-8 mb-12 shadow-2xl"
    >
        <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-6">
            <h2 class="font-numis text-xl font-bold text-slate-100 tracking-wider flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-gold-antique"></span>
                EPİGRAFİK LEJANT & İKONOGRAFİ ANALİZİ
            </h2>
            <span class="text-xs text-slate-400 font-mono hidden sm:inline">Latin & Greek Epigraphy Engine</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- ÖN YÜZ LEJANTI & KELİME KELİME ETKİLEŞİMLİ MORFOLOJİ -->
            <div class="bg-stone-950/80 border border-slate-800 rounded-2xl p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-gold-antique uppercase font-numis tracking-wider">
                            Ön Yüz Lejantı (Avers Yazıtı)
                        </span>
                        <span class="text-[11px] text-slate-500 font-mono">Kelimeye tıkla & anlamını gör</span>
                    </div>

                    <!-- Kelime Kelime Tıklanabilir Piller -->
                    <div class="bg-stone-900 border border-slate-800 rounded-2xl p-4 mb-4">
                        <div class="flex flex-wrap gap-2 items-center">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $obverseTokens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <button 
                                    type="button"
                                    @click="selectToken('<?php echo e($item['token']); ?>')"
                                    :class="selectedToken === '<?php echo e($item['token']); ?>' ? 'bg-amber-500 text-stone-950 font-extrabold border-amber-400 shadow-md scale-105' : 'bg-stone-950 hover:bg-stone-800 text-slate-200 border-slate-700'"
                                    class="px-3 py-1.5 rounded-xl border text-sm font-legend font-semibold transition duration-150 transform"
                                >
                                    <?php echo e($item['token']); ?>

                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <span class="text-slate-500 italic text-xs font-mono">YAZISIZ (ANEPIGRAPHIC)</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <!-- Açılır Morfolojik Çözümleme Kutucuğu -->
                    <div class="space-y-2 mb-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $obverseTokens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div 
                                x-show="selectedToken === '<?php echo e($item['token']); ?>'"
                                x-cloak
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="p-3.5 bg-amber-500/10 border border-amber-500/30 rounded-xl"
                            >
                                <div class="flex items-center justify-between mb-1">
                                    <span class="font-numis font-bold text-gold-antique text-sm">
                                        [<?php echo e($item['token']); ?>] &rarr; <?php echo e($item['title']); ?>

                                    </span>
                                    <span class="text-[10px] uppercase font-mono text-slate-400">Morfolojik Analiz</span>
                                </div>
                                <p class="text-xs text-slate-200 leading-relaxed">
                                    <?php echo e($item['meaning']); ?>

                                </p>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- Tam Türkçe Çeviri -->
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($coin->obverse_legend_trans): ?>
                        <div class="p-3.5 bg-emerald-950/20 border border-emerald-500/20 rounded-xl mb-4">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400 block mb-1">
                                Tam Akademik Türkçe Tercüme:
                            </span>
                            <p class="text-xs text-slate-200 italic leading-relaxed">
                                "<?php echo e($coin->obverse_legend_trans); ?>"
                            </p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <!-- Ön Yüz Tasvir İkonografisi -->
                <div class="pt-3 border-t border-slate-800/80">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-1">
                        Ön Yüz Tasvir İkonografisi:
                    </span>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        <?php echo e($coin->obverse_desc ?? 'Hükümdarın defne taçlı sağa bakan büstü.'); ?>

                    </p>
                </div>
            </div>

            <!-- ARKA YÜZ LEJANTI & İKONOGRAFİK FİGÜR AÇIKLAMASI -->
            <div class="bg-stone-950/80 border border-slate-800 rounded-2xl p-6 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-gold-antique uppercase font-numis tracking-wider">
                            Arka Yüz Lejantı (Revers Yazıtı)
                        </span>
                        <span class="text-[11px] text-slate-500 font-mono">Reverse Legend</span>
                    </div>

                    <!-- Orijinal Revers Metni -->
                    <div class="bg-stone-900 border border-slate-800 rounded-2xl p-4 mb-4 font-legend text-base text-slate-100 tracking-widest break-words">
                        <?php echo e($coin->reverse_legend ?? 'YAZISIZ (ANEPIGRAPHIC)'); ?>

                    </div>

                    <!-- Revers Türkçe Çeviri -->
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($coin->reverse_legend_trans): ?>
                        <div class="p-3.5 bg-emerald-950/20 border border-emerald-500/20 rounded-xl mb-4">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400 block mb-1">
                                Revers Lejant Tercümesi:
                            </span>
                            <p class="text-xs text-slate-200 italic leading-relaxed">
                                "<?php echo e($coin->reverse_legend_trans); ?>"
                            </p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <!-- Tasvir Edilen Mitolojik / İmp. Figür -->
                    <div class="p-4 bg-stone-900/90 border border-slate-800 rounded-xl mb-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-amber-400 text-sm">🦅</span>
                            <h4 class="text-xs font-bold text-slate-100 uppercase tracking-wider font-numis">
                                Tasvir Edilen Figür ve Sembolizm
                            </h4>
                        </div>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            <?php echo e($coin->reverse_desc ?? 'Arka yüzde zafer tanrıçası Victoria (Nike) ayakta, elinde zafer çelengi ve palmiye dalı tutmaktadır. İmparatorluğun askeri zaferlerini ve Roma barışının kalıcılığını simgeler.'); ?>

                        </p>
                    </div>
                </div>

                <!-- Darphane ve Kontrol İşaretleri -->
                <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400 font-mono">
                    <span>Eksürj Damgası: <strong><?php echo e($coin->mint?->code ?? 'CONOB / SMN'); ?></strong></span>
                    <span>Darphane: <strong><?php echo e($coin->mint?->name ?? 'Roma'); ?></strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. İNTERAKTİF KONDİSYON & MÜZAYEDE FİYAT KAYDIRICISI (ALPINE.JS SLIDER)   -->
    <!-- ========================================================================= -->
    <div 
        x-data="{
            selectedGrade: 'VF',
            grades: {
                'Poor': {
                    key: 'Poor',
                    title: 'Kötü / Yıpranmış (Poor - P/G)',
                    badgeClass: 'bg-stone-800 text-stone-300 border-stone-700',
                    wearPercent: 85,
                    wearDesc: 'Yüzeyde %85 aşınma mevcut. Tipolojik ana hatlar ve metal cinsi seçilebilir ancak portre kabartmaları ve lejant harfleri büyük oranda silinmiştir.',
                    minUsd: <?php echo e($valuationMap['Poor']->min_value_usd ?? ($coin->metal->value === 'gold' ? 350 : 25)); ?>,
                    maxUsd: <?php echo e($valuationMap['Poor']->max_value_usd ?? ($coin->metal->value === 'gold' ? 500 : 50)); ?>

                },
                'Fine': {
                    key: 'Fine',
                    title: 'İyi / Dolaşmış (Fine - F)',
                    badgeClass: 'bg-slate-800 text-slate-200 border-slate-700',
                    wearPercent: 55,
                    wearDesc: 'Orta düzeyde dolaşım aşınması. Lejantın temel kelimeleri ve hükümdar profili net; ancak saç bukleleri, defne yaprakları ve kumaş kıvrımları düzleşmiştir.',
                    minUsd: <?php echo e($valuationMap['Fine']->min_value_usd ?? ($coin->metal->value === 'gold' ? 650 : 60)); ?>,
                    maxUsd: <?php echo e($valuationMap['Fine']->max_value_usd ?? ($coin->metal->value === 'gold' ? 950 : 110)); ?>

                },
                'VF': {
                    key: 'VF',
                    title: 'Çok İyi (Very Fine - VF)',
                    badgeClass: 'bg-blue-500/20 text-blue-300 border-blue-500/40',
                    wearPercent: 25,
                    wearDesc: 'Koleksiyoner standardı. Yüksek kabartmalarda hafif aşınma haricinde defne tacı, sakal detayları ve yazıtlar tamamen okunaklı ve keskindir.',
                    minUsd: <?php echo e($valuationMap['VF']->min_value_usd ?? ($coin->metal->value === 'gold' ? 1200 : 150)); ?>,
                    maxUsd: <?php echo e($valuationMap['VF']->max_value_usd ?? ($coin->metal->value === 'gold' ? 1800 : 250)); ?>

                },
                'XF': {
                    key: 'XF',
                    title: 'Son Derece İyi (Extremely Fine - XF/EF)',
                    badgeClass: 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40',
                    wearPercent: 10,
                    wearDesc: 'Minimum dolaşım izi. Yalnızca en uç rölyef noktalarında mikroskobik aşınma; flan parlaklığı (mint luster) yer yer korunmuştur.',
                    minUsd: <?php echo e($valuationMap['XF']->min_value_usd ?? ($coin->metal->value === 'gold' ? 2400 : 350)); ?>,
                    maxUsd: <?php echo e($valuationMap['XF']->max_value_usd ?? ($coin->metal->value === 'gold' ? 3500 : 550)); ?>

                },
                'UNC': {
                    key: 'UNC',
                    title: 'Çil / Darphaneden Çıktığı Gibi (Uncirculated - UNC)',
                    badgeClass: 'bg-amber-500/20 text-gold-antique border-amber-500/40',
                    wearPercent: 0,
                    wearDesc: 'Dolaşıma girmemiş, darp kalıbından çıktığı günkü gibi kusursuz yüzey bütünlüğü, tam flan parlaklığı ve sıfır dolaşım kaybı.',
                    minUsd: <?php echo e($valuationMap['UNC']->min_value_usd ?? ($coin->metal->value === 'gold' ? 4500 : 800)); ?>,
                    maxUsd: <?php echo e($valuationMap['UNC']->max_value_usd ?? ($coin->metal->value === 'gold' ? 7000 : 1400)); ?>

                }
            }
        }"
        class="bg-stone-900 border border-slate-800 rounded-3xl p-6 sm:p-8 mb-12 shadow-2xl"
    >
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-4 border-b border-slate-800">
            <div>
                <h3 class="font-numis text-xl font-bold text-slate-100 tracking-wider flex items-center gap-2">
                    <svg class="w-5 h-5 text-gold-antique" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    İNTERAKTİF KONDİSYON VE MÜZAYEDE FİYAT BANTLARI
                </h3>
                <p class="text-xs text-slate-400 mt-1">
                    Kondisyon butonlarını değiştirerek sikkenin yıpranma oranını ve gerçekçi müzayede aralığını anında görüntüleyin.
                </p>
            </div>
            <span class="text-xs bg-stone-950 border border-slate-800 text-gold-antique px-3 py-1.5 rounded-xl font-mono self-start sm:self-auto">
                Para Birimi: USD ($)
            </span>
        </div>

        <!-- 5 Butonlu Kondisyon Seçici (Poor -> Fine -> VF -> XF -> UNC) -->
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5 mb-8">
            <template x-for="(g, key) in grades" :key="key">
                <button 
                    type="button" 
                    @click="selectedGrade = key"
                    :class="selectedGrade === key ? 'border-gold-antique bg-amber-500/10 shadow-lg shadow-amber-500/10 scale-102 ring-1 ring-gold-antique' : 'border-slate-800 bg-stone-950 hover:bg-stone-850 opacity-80'"
                    class="p-3.5 rounded-2xl border text-center transition duration-150 flex flex-col items-center justify-center gap-1"
                >
                    <span class="font-numis font-extrabold text-base" :class="selectedGrade === key ? 'text-gold-antique' : 'text-slate-200'" x-text="key"></span>
                    <span class="text-[10px] text-slate-400" x-text="key === 'UNC' ? 'Çil / Kusursuz' : (key === 'VF' ? 'Koleksiyon Std.' : (key === 'XF' ? 'Çok Temiz' : 'Dolaşmış'))"></span>
                </button>
            </template>
        </div>

        <!-- Canlı Dinamik Değer & Aşınma Kartı -->
        <div class="bg-gradient-to-r from-stone-950 via-stone-900 to-stone-950 border border-slate-800 rounded-2xl p-6 sm:p-8 mb-8 grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
            
            <!-- Tahmini Referans Müzayede Fiyatı (5 Kolon) -->
            <div class="md:col-span-5 border-b md:border-b-0 md:border-r border-slate-800 pb-6 md:pb-0 md:pr-6">
                <span class="text-[11px] uppercase font-bold tracking-wider text-slate-400 block mb-1">
                    Tahmini Müzayede Değer Bandı (<span x-text="selectedGrade"></span>):
                </span>
                <div class="flex items-baseline gap-2 mb-2">
                    <span class="font-numis text-3xl sm:text-4xl font-black text-gold-antique">
                        $<span x-text="grades[selectedGrade].minUsd.toLocaleString()"></span> - $<span x-text="grades[selectedGrade].maxUsd.toLocaleString()"></span>
                    </span>
                    <span class="text-xs font-mono text-slate-400">USD</span>
                </div>
                <p class="text-[11px] text-slate-500 leading-relaxed">
                    Heritage, CNG, Roma Numismatics ve Gorny & Mosch gerçekleşen çekiç fiyatları konsolidasyonudur.
                </p>
            </div>

            <!-- Aşınma Seviyesi Barı ve Açıklama (7 Kolon) -->
            <div class="md:col-span-7 flex flex-col justify-center">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-200" x-text="grades[selectedGrade].title"></span>
                    <span class="text-xs font-mono font-bold text-gold-antique" x-text="`%${grades[selectedGrade].wearPercent} Aşınma Oranı`"></span>
                </div>

                <!-- İlerleme Çubuğu (Wear Bar) -->
                <div class="w-full h-3 bg-stone-950 rounded-full border border-slate-800 overflow-hidden mb-3">
                    <div 
                        class="h-full bg-gradient-to-r from-emerald-500 via-amber-500 to-red-500 transition-all duration-300 rounded-full"
                        :style="`width: ${100 - grades[selectedGrade].wearPercent}%;`"
                    ></div>
                </div>

                <!-- Detaylı Kondisyon Kriteri -->
                <p class="text-xs text-slate-300 leading-relaxed italic" x-text="grades[selectedGrade].wearDesc"></p>
            </div>
        </div>

        <!-- Gerçekleşen Açık Artırma Satışları Tablosu (Son 24 Ay) -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-xs font-bold text-slate-200 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    KAMUYA AÇIK GERÇEKLEŞEN SATIŞ ARŞİVİ (SON 24 AY)
                </h4>
                <span class="text-xs text-slate-400 font-mono"><?php echo e($coin->auctionRecords->count()); ?> Satış Kaydı</span>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($coin->auctionRecords->isNotEmpty()): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider">
                                <th class="py-2.5 px-3">Müzayede Evi & Satış</th>
                                <th class="py-2.5 px-3">Lot No</th>
                                <th class="py-2.5 px-3">Kondisyon</th>
                                <th class="py-2.5 px-3">Tarih</th>
                                <th class="py-2.5 px-3 text-right">Çekiç Fiyatı</th>
                                <th class="py-2.5 px-3 text-right">Net Gerçekleşen (USD)</th>
                                <th class="py-2.5 px-3 text-center">Kaynak</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 font-mono">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $coin->auctionRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $auction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-stone-850/40 transition">
                                    <td class="py-2.5 px-3 font-sans font-medium text-slate-200">
                                        <?php echo e($auction->auction_house); ?>

                                        <span class="block text-[11px] text-slate-400"><?php echo e($auction->sale_name_or_number); ?></span>
                                    </td>
                                    <td class="py-2.5 px-3 text-slate-300"><?php echo e($auction->lot_number); ?></td>
                                    <td class="py-2.5 px-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-stone-950 text-gold-antique border border-slate-800">
                                            <?php echo e($auction->condition_grade?->value ?? $auction->condition_grade); ?>

                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3 text-slate-400"><?php echo e($auction->sale_date?->format('d.m.Y')); ?></td>
                                    <td class="py-2.5 px-3 text-right text-slate-400">$<?php echo e(number_format($auction->hammer_price_usd, 0)); ?></td>
                                    <td class="py-2.5 px-3 text-right text-emerald-400 font-bold">$<?php echo e(number_format($auction->realized_price_usd, 0)); ?></td>
                                    <td class="py-2.5 px-3 text-center font-sans">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($auction->source_url): ?>
                                            <a href="<?php echo e($auction->source_url); ?>" target="_blank" rel="noopener noreferrer" class="text-gold-antique hover:underline text-[11px]">
                                                Lot Gör ↗
                                            </a>
                                        <?php else: ?>
                                            <span class="text-slate-600">—</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="bg-stone-950 border border-slate-800 rounded-xl p-4 text-center text-xs text-slate-400 font-mono">
                    Bu sikke için son 24 aya ait açık artırma satış kaydı henüz arşivlenmemiş.
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 5. ŞAHSİ KOLEKSİYON DEFTERİNE EKLEME FORMU                                -->
    <!-- ========================================================================= -->
    <div id="collection-section" class="bg-stone-900 border border-slate-800 rounded-3xl p-6 sm:p-8 mb-12 shadow-2xl">
        <h4 class="text-sm font-bold text-gold-antique uppercase font-numis tracking-wider mb-4 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Bu Sikkeyi Şahsi Koleksiyon Defterime Ekle
        </h4>
        <form action="<?php echo e(route('collection.store', $coin->id)); ?>" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-[11px] text-slate-400 mb-1">Elinizdeki Kondisyon *</label>
                <select name="user_condition" required class="w-full bg-stone-950 border border-slate-700 rounded-xl px-3 py-2.5 text-xs text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $conditionGrades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $grade): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($grade->value); ?>" <?php echo e($grade->value === 'VF' ? 'selected' : ''); ?>>
                            <?php echo e($grade->value); ?> - <?php echo e($grade->label()); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </select>
            </div>
            <div>
                <label class="block text-[11px] text-slate-400 mb-1">Edinme Bedeli (USD, Opsiyonel)</label>
                <input type="number" step="0.01" name="purchase_price_usd" placeholder="Örn: 350.00" class="w-full bg-stone-950 border border-slate-700 rounded-xl px-3 py-2.5 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-[11px] text-slate-400 mb-1">Sikkenizin Fotoğrafı (Opsiyonel)</label>
                <input type="file" name="custom_image" accept="image/*" class="w-full bg-stone-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-slate-400 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[10px] file:bg-stone-800 file:text-slate-300">
            </div>
            <div>
                <label class="block text-[11px] text-slate-400 mb-1">Kişisel Notunuz</label>
                <input type="text" name="notes" placeholder="Örn: 2024 İstanbul sergisinden" class="w-full bg-stone-950 border border-slate-700 rounded-xl px-3 py-2.5 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gold-antique hover:bg-gold-hover text-stone-950 font-bold py-2.5 px-4 rounded-xl text-xs transition duration-150 shadow-md">
                    Envanterime Kaydet
                </button>
            </div>
            <div class="sm:col-span-2 lg:col-span-5 pt-2 border-t border-slate-800/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <label class="flex items-center gap-2 cursor-pointer text-xs text-gold-antique font-semibold select-none">
                    <input type="checkbox" id="has_mint_error_checkbox" name="has_mint_error" value="1" onchange="document.getElementById('mint_error_options').classList.toggle('hidden', !this.checked)" class="rounded border-slate-700 bg-stone-950 text-amber-500 focus:ring-amber-500">
                    <span>⚡ Bu Sikkede Darp Hatası Var mı? (Off-Center, Brockage, Çift Darp vb.)</span>
                </label>

                <div id="mint_error_options" class="hidden flex-1 flex items-center gap-2 w-full sm:w-auto">
                    <select name="mint_error_type_id" class="bg-stone-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 focus:outline-none focus:ring-1 focus:ring-amber-500">
                        <option value="">-- Hata Türü Seçin --</option>
                        <option value="1">Off-Center Strike (Merkezleme Kayması - 1.5x-4x)</option>
                        <option value="2">Double Die (Çift Darp - 2x-5x)</option>
                        <option value="3">Brockage (Ters / Ayna Baskı - 5x-10x)</option>
                        <option value="4">Die Crack (Kalıp Çatlağı - 1.3x-2.5x)</option>
                        <option value="5">Overstrike (Yeniden Basım - 3x-8x)</option>
                        <option value="6">Clipped Planchet (Kesik Pul - 1.5x-3x)</option>
                    </select>
                    <input type="text" name="mint_error_notes" placeholder="Örn: %35 sağa kayık" class="flex-1 bg-stone-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-amber-500">
                    <a href="<?php echo e(route('mint-errors.index')); ?>" target="_blank" class="text-[11px] text-gold-antique hover:underline shrink-0 font-medium">Rehber &rarr;</a>
                </div>
            </div>
            <div class="sm:col-span-2 lg:col-span-5 pt-1">
                <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-400 select-none">
                    <input type="checkbox" name="legal_declaration" value="1" class="rounded border-slate-700 bg-stone-950 text-amber-500 focus:ring-amber-500">
                    <span>Bu görselin yasal haklarına sahibim / kişisel koleksiyon veya açık kaynak amaçlı ekliyorum (2863 sayılı kanun hükümleri saklıdır).</span>
                </label>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- 6. TARİHSEL BAĞLAM VE FİZİKSEL SAPMA / SAHTELİK DOĞRULAMA ARACI           -->
    <!-- ========================================================================= -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($coin->historical_notes): ?>
        <div class="bg-stone-900 border border-slate-800 rounded-3xl p-6 sm:p-8 mb-12 shadow-2xl">
            <h3 class="font-numis text-xl font-bold text-slate-100 tracking-wider mb-4 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-gold-antique"></span>
                TARİHSEL BAĞLAM VE NÜMİZMATİK NOTLAR
            </h3>
            <div class="prose prose-invert max-w-none text-slate-300 text-sm leading-relaxed whitespace-pre-line">
                <?php echo e($coin->historical_notes); ?>

            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!-- Fiziksel Sapma ve Sahtelik Doğrulama Aracı -->
    <div class="bg-stone-900 border border-slate-800 rounded-3xl p-6 sm:p-8 mb-12 shadow-2xl" id="deviationCalculator">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-6 border-b border-slate-800">
            <div>
                <h3 class="font-numis text-xl font-bold text-slate-100 tracking-wider flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    FİZİKSEL SAPMA VE SAHTELİK DOĞRULAMA ARACI
                </h3>
                <p class="text-xs text-slate-400 mt-1">
                    Elinizdeki sikkenin hassas terazi ağırlığı ve kumpas çapını girerek orijinal darp standartlarıyla anlık karşılaştırın.
                </p>
            </div>
            <div class="flex items-center gap-2 font-mono text-xs">
                <span class="bg-stone-950 border border-slate-800 px-3 py-1.5 rounded-xl text-gold-antique">
                    Ref. Ağırlık: <strong><?php echo e($coin->weight_g ? $coin->weight_g . 'g' : '—'); ?></strong>
                </span>
                <span class="bg-stone-950 border border-slate-800 px-3 py-1.5 rounded-xl text-slate-300">
                    Ref. Çap: <strong><?php echo e($coin->diameter_mm ? $coin->diameter_mm . 'mm' : '—'); ?></strong>
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                    Hassas Terazi ile Ölçülen Ağırlık (Gram) *
                </label>
                <div class="relative">
                    <input type="number" id="inputWeight" step="0.01" placeholder="Örn: <?php echo e($coin->weight_g ?? '3.40'); ?>" class="w-full bg-stone-950 border border-slate-700 rounded-xl px-4 py-3 text-slate-100 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono text-sm">
                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 font-mono text-xs">g</span>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                    Kumpas ile Ölçülen Çap (Milimetre, Opsiyonel)
                </label>
                <div class="relative">
                    <input type="number" id="inputDiameter" step="0.1" placeholder="Örn: <?php echo e($coin->diameter_mm ?? '19.0'); ?>" class="w-full bg-stone-950 border border-slate-700 rounded-xl px-4 py-3 text-slate-100 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-amber-500 font-mono text-sm">
                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400 font-mono text-xs">mm</span>
                </div>
            </div>
        </div>

        <div id="diagnosticAlertBox" class="hidden rounded-2xl p-5 border text-sm transition-all duration-300 mb-6">
            <div class="flex items-start gap-3">
                <div id="diagnosticIcon" class="shrink-0 mt-0.5"></div>
                <div>
                    <h4 id="diagnosticTitle" class="font-bold text-sm mb-1"></h4>
                    <p id="diagnosticMessage" class="text-xs leading-relaxed"></p>
                    <div id="diagnosticStats" class="mt-3 flex flex-wrap gap-2 text-[11px] font-mono"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sahte Nasıl Anlaşılır Rehberi -->
    <div class="mb-12">
        <?php echo $__env->make('components.fake-coin-guide', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <!-- ========================================================================= -->
    <!-- 7. IIIF OPEN SEADRAGON MİKROSKOBİK DERİN ZOOM MODALI                      -->
    <!-- ========================================================================= -->
    <div id="deepZoomModal" class="fixed inset-0 z-50 bg-stone-950/95 backdrop-blur-md hidden flex-col">
        <div class="flex items-center justify-between px-6 py-4 bg-stone-900 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <span class="text-gold-antique text-lg">🔬</span>
                <h3 id="deepZoomTitle" class="font-numis font-bold text-sm text-slate-100 tracking-wider">
                    Mikroskobik Yüzey Analizi (IIIF Image API)
                </h3>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" id="osd-zoom-in" class="px-3 py-1.5 bg-stone-800 hover:bg-stone-700 text-white rounded-lg text-xs font-bold" title="Yakınlaştır">+</button>
                <button type="button" id="osd-zoom-out" class="px-3 py-1.5 bg-stone-800 hover:bg-stone-700 text-white rounded-lg text-xs font-bold" title="Uzaklaştır">-</button>
                <button type="button" id="osd-home" class="px-3 py-1.5 bg-stone-800 hover:bg-stone-700 text-white rounded-lg text-xs font-bold" title="Sıfırla">⌂</button>
                <button type="button" id="osd-rotate-left" class="px-3 py-1.5 bg-stone-800 hover:bg-stone-700 text-white rounded-lg text-xs font-bold" title="Sola Döndür">↺</button>
                <button type="button" id="osd-rotate-right" class="px-3 py-1.5 bg-stone-800 hover:bg-stone-700 text-white rounded-lg text-xs font-bold" title="Sağa Döndür">↻</button>
                <button type="button" onclick="closeDeepZoom()" class="px-3 py-1.5 bg-red-900/60 hover:bg-red-800 text-white rounded-lg text-xs font-bold ml-2" title="Kapat">✕ Kapat</button>
            </div>
        </div>
        <div class="flex-1 relative bg-stone-950 overflow-hidden">
            <div id="openseadragon-canvas" class="w-full h-full"></div>
            <div class="absolute bottom-5 left-5 bg-stone-900/90 border border-slate-700 p-3.5 rounded-xl max-w-sm text-xs text-slate-300 pointer-events-none shadow-xl">
                <div class="font-bold text-gold-antique mb-1 flex items-center gap-1">
                    <span>🔍</span> Mikroskobik Yüzey Analizi
                </div>
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Fare tekerleğiyle mikroskobik derinlikte kılcal darp akış çizgilerini (flow lines), patina kristalleşmesini ve kalıp kırıklarını (die breaks) inceleyebilirsiniz.
                </p>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 8. BENZER / İLGİLİ SİKKELER                                               -->
    <!-- ========================================================================= -->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($similarCoins->count() > 0): ?>
        <div>
            <h3 class="font-numis text-xl font-bold text-slate-100 tracking-wider mb-6 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-gold-antique"></span>
                DÖNEMİN DİĞER TİPOLOJİK SİKKELERİ
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $similarCoins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $similar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('coins.show', $similar->slug)); ?>" class="bg-stone-900 border border-slate-800 hover:border-gold-antique/50 rounded-2xl overflow-hidden p-4 group transition flex flex-col justify-between shadow-xl">
                        <div class="aspect-square bg-stone-950 rounded-xl p-3 flex items-center justify-center mb-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($similar->primaryImage): ?>
                                <img src="<?php echo e($similar->primaryImage->url); ?>" alt="<?php echo e($similar->title); ?>" class="max-h-full max-w-full object-contain group-hover:scale-105 transition duration-200">
                            <?php else: ?>
                                <span class="text-xs text-slate-600 font-mono">Görsel Yok</span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <div>
                            <span class="text-[11px] font-mono text-gold-antique block mb-1"><?php echo e($similar->catalog_reference); ?></span>
                            <h4 class="font-numis text-xs font-bold text-slate-200 group-hover:text-gold-antique transition line-clamp-2">
                                <?php echo e($similar->title); ?>

                            </h4>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

</div>

<!-- OpenSeadragon CDN (Deep Zoom & IIIF) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/openseadragon/4.1.1/openseadragon.min.js"></script>

<script>
let osdViewer = null;

function openDeepZoom(imageUrl, imageId, title) {
    document.getElementById('deepZoomTitle').textContent = `${title} - Mikroskobik Yüzey Analizi`;
    document.getElementById('deepZoomModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    if (osdViewer) {
        osdViewer.destroy();
    }

    osdViewer = OpenSeadragon({
        id: "openseadragon-canvas",
        prefixUrl: "https://cdnjs.cloudflare.com/ajax/libs/openseadragon/4.1.1/images/",
        tileSources: {
            type: 'image',
            url: imageUrl,
            buildPyramid: true
        },
        showNavigator: true,
        navigatorPosition: "BOTTOM_RIGHT",
        showRotationControl: true,
        animationTime: 0.5,
        blendTime: 0.1,
        constrainDuringPan: true,
        maxZoomPixelRatio: 6,
        minZoomImageRatio: 0.8,
        visibilityRatio: 1.0,
        zoomInButton: "osd-zoom-in",
        zoomOutButton: "osd-zoom-out",
        homeButton: "osd-home",
        rotateLeftButton: "osd-rotate-left",
        rotateRightButton: "osd-rotate-right"
    });
}

function closeDeepZoom() {
    document.getElementById('deepZoomModal').classList.add('hidden');
    document.body.style.overflow = '';
    if (osdViewer) {
        osdViewer.destroy();
        osdViewer = null;
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeepZoom();
    }
});

// Fiziksel Sapma Hesaplama Mantığı
(function() {
    const refWeight = <?php echo e((float) ($coin->weight_g ?? 0)); ?>;
    const refDiameter = <?php echo e((float) ($coin->diameter_mm ?? 0)); ?>;

    const inputWeight = document.getElementById('inputWeight');
    const inputDiameter = document.getElementById('inputDiameter');
    const alertBox = document.getElementById('diagnosticAlertBox');
    const titleEl = document.getElementById('diagnosticTitle');
    const msgEl = document.getElementById('diagnosticMessage');
    const statsEl = document.getElementById('diagnosticStats');
    const iconEl = document.getElementById('diagnosticIcon');

    if (!inputWeight || !alertBox) return;

    function calculateDeviation() {
        const weightVal = parseFloat(inputWeight.value);
        const diameterVal = parseFloat(inputDiameter.value);

        if (!weightVal || isNaN(weightVal) || weightVal <= 0 || refWeight <= 0) {
            alertBox.classList.add('hidden');
            return;
        }

        alertBox.classList.remove('hidden');

        const weightDev = (((weightVal - refWeight) / refWeight) * 100);
        const absWeightDev = Math.abs(weightDev);

        let diameterDev = 0;
        let hasDiameter = false;
        if (diameterVal && !isNaN(diameterVal) && diameterVal > 0 && refDiameter > 0) {
            diameterDev = (((diameterVal - refDiameter) / refDiameter) * 100);
            hasDiameter = true;
        }
        const absDiameterDev = Math.abs(diameterDev);

        alertBox.className = 'rounded-2xl p-5 border text-sm transition-all duration-300 mb-6';

        if (absWeightDev <= 8.0 && (!hasDiameter || absDiameterDev <= 6.0)) {
            alertBox.classList.add('bg-emerald-950/40', 'border-emerald-500/50', 'text-emerald-200');
            iconEl.innerHTML = '<span class="text-2xl">✅</span>';
            titleEl.textContent = 'Normal Darp Tolerans Aralığında';
            msgEl.textContent = `Girdiğiniz ağırlık (${weightVal.toFixed(2)}g), orijinal referans standartlarına (${refWeight.toFixed(2)}g) çok yakındır (%${weightDev > 0 ? '+' : ''}${weightDev.toFixed(1)} sapma). Antik darp flan toleransı dahilindedir.`;
        } else if (absWeightDev <= 15.0) {
            alertBox.classList.add('bg-amber-950/40', 'border-amber-500/50', 'text-amber-200');
            iconEl.innerHTML = '<span class="text-2xl">⚠️</span>';
            titleEl.textContent = 'Hafif Tolerans / Dolaşım Aşınması';
            msgEl.textContent = `Girdiğiniz ağırlıkta %${weightDev > 0 ? '+' : ''}${weightDev.toFixed(1)} oranında sapma var. Antik dönemde dolaşımda flan aşınması bu farka yol açmış olabilir.`;
        } else {
            alertBox.classList.add('bg-red-950/50', 'border-red-500/60', 'text-red-200');
            iconEl.innerHTML = '<span class="text-2xl">🚨</span>';
            titleEl.textContent = 'Kritik Sapma / Muhtemel Döküm Sahtesi!';
            if (weightDev < -15.0) {
                msgEl.textContent = `Ağırlık referansın çok altındadır (%${weightDev.toFixed(1)} sapma). Sikke kalay/kurşun dolgulu taklit veya aşırı traşlanmış olabilir.`;
            } else {
                msgEl.textContent = `Ağırlık referansın çok üzerindedir (+%${weightDev.toFixed(1)} sapma). Modern döküm kopyalar orijinalden daha kalın ve ağır çıkmaktadır.`;
            }
        }

        statsEl.innerHTML = `
            <span class="bg-stone-900 px-2 py-0.5 rounded border border-slate-700">Ref: ${refWeight}g</span>
            <span class="bg-stone-900 px-2 py-0.5 rounded border border-slate-700">Ölçülen: ${weightVal.toFixed(2)}g</span>
            <span class="bg-stone-900 px-2 py-0.5 rounded border border-slate-700">Ağırlık Farkı: ${weightDev > 0 ? '+' : ''}${weightDev.toFixed(1)}%</span>
            ${hasDiameter ? `<span class="bg-stone-900 px-2 py-0.5 rounded border border-slate-700">Çap Farkı: ${diameterDev > 0 ? '+' : ''}${diameterDev.toFixed(1)}%</span>` : ''}
        `;
    }

    inputWeight.addEventListener('input', calculateDeviation);
    if (inputDiameter) inputDiameter.addEventListener('input', calculateDeviation);
})();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\taner\Documents\GitHub\Tarih\resources\views/coins/show.blade.php ENDPATH**/ ?>