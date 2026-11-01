@extends('layouts.app')

@section('title', 'İnteraktif Antik Darphaneler ve Ticaret Rotaları Haritası')
@section('meta_description', 'Roma, Bizans ve Grek sikkelerinin basıldığı antik darphaneler, Trajan dönemi imparatorluk sınırları ve Akdeniz ticaret rotaları interaktif haritası.')

@section('content')
<div class="h-[calc(100vh-80px)] min-h-[640px] flex flex-col md:flex-row bg-slate-950 overflow-hidden relative">

    <!-- Sol Panel: Darphane Listesi ve Katman Kontrolleri -->
    <aside class="w-full md:w-96 bg-slate-900 border-r border-slate-800 flex flex-col z-20 shrink-0 h-64 md:h-full">
        <!-- Başlık ve Filtre -->
        <div class="p-4 border-b border-slate-800 bg-slate-900/90 backdrop-blur">
            <div class="flex items-center justify-between mb-2">
                <h1 class="text-lg font-bold text-amber-400 flex items-center gap-2">
                    <span>🏛️</span> Antik Darphane Atlası
                </h1>
                <span class="text-xs bg-amber-500/10 text-amber-300 border border-amber-500/20 px-2 py-0.5 rounded-full font-mono">
                    {{ $mints->count() }} Darphane
                </span>
            </div>
            <p class="text-xs text-slate-400 mb-3">
                Akdeniz havzası darphanelerini ve imparatorluk rotalarını inceleyin.
            </p>

            <!-- Arama Kutusu -->
            <div class="relative">
                <input type="text" id="mint-search-input" 
                       placeholder="Darphane veya modern şehir ara..." 
                       class="w-full bg-slate-950 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-amber-400 transition">
                <span class="absolute right-2.5 top-1.5 text-xs text-slate-500">🔍</span>
            </div>

            <!-- Katman Seçenekleri -->
            <div class="mt-3 pt-3 border-t border-slate-800/80 flex flex-col gap-1.5 text-xs">
                <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white transition">
                    <input type="checkbox" id="layer-trajan" checked class="rounded border-slate-700 text-amber-500 focus:ring-0 bg-slate-800">
                    <span class="inline-block w-2.5 h-2.5 rounded-sm bg-red-600/80 border border-red-400"></span>
                    <span>M.S. 117 Trajan Roma Sınırları</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer text-slate-300 hover:text-white transition">
                    <input type="checkbox" id="layer-routes" checked class="rounded border-slate-700 text-amber-500 focus:ring-0 bg-slate-800">
                    <span class="inline-block w-2.5 h-1 rounded-sm bg-amber-500 border border-amber-400"></span>
                    <span>Antik Ticaret Yolları (Via Egnatia & Deniz)</span>
                </label>
            </div>
        </div>

        <!-- Darphaneler Listesi -->
        <div id="mint-list" class="flex-1 overflow-y-auto divide-y divide-slate-800/60 p-2 space-y-1">
            @foreach($mints as $mint)
                <button type="button" 
                        data-mint-id="{{ $mint->id }}"
                        data-mint-slug="{{ $mint->slug }}"
                        data-mint-name="{{ $mint->name }}"
                        data-ancient-name="{{ $mint->ancient_name ?: $mint->name }}"
                        data-lat="{{ $mint->latitude }}"
                        data-lng="{{ $mint->longitude }}"
                        class="mint-item w-full text-left p-2.5 rounded-lg hover:bg-slate-800/80 transition flex items-start justify-between group border border-transparent hover:border-slate-700">
                    <div>
                        <div class="font-semibold text-sm text-slate-200 group-hover:text-amber-400 transition flex items-center gap-1.5">
                            <span class="text-amber-400/80 text-xs">🪙</span>
                            <span>{{ $mint->name }}</span>
                        </div>
                        <div class="text-xs text-slate-400">
                            {{ $mint->ancient_name ?: $mint->name }} &bull; {{ $mint->location }}
                        </div>
                        <div class="text-[11px] text-slate-500 mt-0.5">
                            {{ $mint->active_period ?: 'Antik Dönem' }}
                        </div>
                    </div>
                    <span class="text-xs bg-slate-800 group-hover:bg-amber-400/10 text-slate-400 group-hover:text-amber-300 px-2 py-0.5 rounded border border-slate-700 shrink-0 font-mono">
                        {{ $mint->coins_count }} sikke
                    </span>
                </button>
            @endforeach
        </div>
    </aside>

    <!-- Harita Alanı -->
    <main class="flex-1 relative h-full">
        <div id="ancient-map" class="w-full h-full bg-slate-950 z-0"></div>

        <!-- Bilgi Kutucuğu / Sağ Alt Gösterge -->
        <div class="absolute bottom-5 left-5 z-10 bg-slate-900/90 backdrop-blur border border-slate-700 p-2.5 rounded-lg text-xs shadow-xl hidden md:block max-w-xs text-slate-300 pointer-events-none">
            <div class="font-bold text-amber-400 mb-1 flex items-center gap-1">
                <span>🧭</span> Nümizmatik Coğrafi Bilgi Sistemi
            </div>
            <p class="text-[11px] text-slate-400 leading-relaxed">
                Haritadaki sikke pini ikonlarına tıklayarak darphane detaylarını, aktif dönemini ve bu darphanede basılmış en popüler sikkeleri inceleyin.
            </p>
        </div>
    </main>

    <!-- Sağ Çekmece Panel: Seçili Darphane Detayları (Drawer) -->
    <div id="mint-drawer" class="fixed inset-y-0 right-0 w-full sm:w-[440px] bg-slate-900/95 backdrop-blur-md border-l border-slate-700 shadow-2xl z-30 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
        <!-- Çekmece Başlığı -->
        <div class="p-4 border-b border-slate-800 flex items-start justify-between bg-slate-900">
            <div>
                <span class="text-[11px] font-mono tracking-wider uppercase text-amber-400 bg-amber-500/10 border border-amber-500/20 px-2 py-0.5 rounded">
                    Antik Darphane
                </span>
                <h2 id="drawer-mint-name" class="text-xl font-bold text-white mt-1.5">Darphane Adı</h2>
                <div id="drawer-mint-ancient" class="text-xs text-amber-300/90 font-serif italic">Antik Ad</div>
            </div>
            <button id="close-drawer-btn" type="button" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Çekmece İçeriği -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            <!-- Temel Bilgiler Grid -->
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div class="bg-slate-800/80 p-2.5 rounded-lg border border-slate-700/60">
                    <span class="text-slate-400 block text-[11px]">Modern Konum</span>
                    <span id="drawer-mint-modern" class="font-medium text-slate-200">-</span>
                </div>
                <div class="bg-slate-800/80 p-2.5 rounded-lg border border-slate-700/60">
                    <span class="text-slate-400 block text-[11px]">Aktif Dönem</span>
                    <span id="drawer-mint-period" class="font-medium text-amber-300">-</span>
                </div>
            </div>

            <!-- Tarihsel Önem -->
            <div class="bg-slate-800/50 p-3 rounded-lg border border-slate-800">
                <h4 class="text-xs font-semibold text-slate-300 mb-1 flex items-center gap-1.5">
                    <span>📜</span> Tarihsel ve Nümizmatik Önemi
                </h4>
                <p id="drawer-mint-significance" class="text-xs text-slate-400 leading-relaxed">
                    Yükleniyor...
                </p>
            </div>

            <!-- En Popüler 5 Sikke Bölümü -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-xs font-bold text-slate-200 tracking-wide uppercase flex items-center gap-1.5">
                        <span>⭐</span> Bu Darphanede Basılmış Önemli Sikkeler
                    </h3>
                    <span id="drawer-coins-count" class="text-[11px] text-slate-400 font-mono">0 Sikke</span>
                </div>

                <div id="drawer-top-coins-list" class="space-y-2">
                    <!-- Dinamik Yüklenecek Sikke Kartları -->
                </div>
            </div>
        </div>

        <!-- Çekmece Alt Buton: Tek Tıkla Filtreye Geçiş -->
        <div class="p-4 border-t border-slate-800 bg-slate-900">
            <a id="drawer-view-all-link" href="#" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-lg text-sm transition shadow-lg shadow-amber-500/20">
                <span>Bu Darphaneye Ait Tüm Sikkeleri Gör</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </div>
</div>

<!-- Leaflet.js CSS ve JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<style>
    /* Leaflet Harita Karanlık Tema Düzeltmeleri */
    .leaflet-popup-content-wrapper {
        background: #0f172a !important;
        color: #f1f5f9 !important;
        border: 1px solid #334155 !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5) !important;
    }
    .leaflet-popup-tip {
        background: #0f172a !important;
        border: 1px solid #334155 !important;
    }
    .leaflet-container {
        font-family: inherit;
    }
    .custom-mint-marker {
        display: flex;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle, #fbbf24 0%, #b45309 100%);
        border: 2px solid #ffffff;
        box-shadow: 0 0 12px rgba(251, 191, 36, 0.8), 0 4px 6px -1px rgba(0,0,0,0.5);
        color: #1e293b;
        font-weight: 800;
        font-size: 14px;
        border-radius: 50%;
        transition: transform 0.2s ease;
        cursor: pointer;
    }
    .custom-mint-marker:hover {
        transform: scale(1.25);
        box-shadow: 0 0 18px rgba(251, 191, 36, 1);
    }
    .custom-mint-marker.active {
        border-color: #ef4444;
        box-shadow: 0 0 22px rgba(239, 68, 68, 1);
        transform: scale(1.3);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Leaflet Harita Başlatma (Akdeniz merkezli)
    const map = L.map('ancient-map', {
        zoomControl: true,
        attributionControl: true
    }).setView([38.5, 23.0], 5);

    // 2. CartoDB Dark Matter / Positron Kiremit Katmanı
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 18,
        minZoom: 3
    }).addTo(map);

    let mintGeoJsonData = null;
    let markersLayer = L.layerGroup().addTo(map);
    let trajanLayer = null;
    let routesLayer = null;
    let markerInstances = {};

    const initialMintSlug = "{{ $selectedMintSlug ?? '' }}";

    // 3. Özel Sikke Pin İkonu
    function createMintIcon(isActive = false) {
        return L.divIcon({
            className: 'custom-mint-marker-wrapper',
            html: `<div class="custom-mint-marker ${isActive ? 'active' : ''}" style="width: 28px; height: 28px;">🪙</div>`,
            iconSize: [28, 28],
            iconAnchor: [14, 14],
            popupAnchor: [0, -14]
        });
    }

    // 4. M.S. 117 Trajan Sınırları ve Antik Rotalar (GeoJSON)
    fetch('/api/v1/geo/boundaries')
        .then(res => res.json())
        .then(data => {
            // Trajan Sınır Katmanı
            const trajanFeatures = data.features.filter(f => f.id === 'trajan_117_border');
            if (trajanFeatures.length > 0) {
                trajanLayer = L.geoJSON(trajanFeatures, {
                    style: function (feature) {
                        return {
                            color: feature.properties.stroke_color || '#dc2626',
                            weight: feature.properties.stroke_width || 2,
                            fillColor: feature.properties.fill_color || '#b91c1c',
                            fillOpacity: feature.properties.fill_opacity || 0.12,
                            dashArray: '3, 6'
                        };
                    },
                    onEachFeature: function (feature, layer) {
                        layer.bindTooltip(`<strong>${feature.properties.name}</strong><br><small>${feature.properties.description}</small>`, {
                            sticky: true
                        });
                    }
                }).addTo(map);
            }

            // Antik Yollar ve Deniz Rotaları Katmanı
            const routeFeatures = data.features.filter(f => f.id !== 'trajan_117_border');
            if (routeFeatures.length > 0) {
                routesLayer = L.geoJSON(routeFeatures, {
                    style: function (feature) {
                        return {
                            color: feature.properties.stroke_color || '#d97706',
                            weight: feature.properties.stroke_width || 3,
                            dashArray: feature.properties.dash_array || '5, 5'
                        };
                    },
                    onEachFeature: function (feature, layer) {
                        layer.bindTooltip(`<strong>${feature.properties.name}</strong><br><small>${feature.properties.route}</small>`, {
                            sticky: true
                        });
                    }
                }).addTo(map);
            }
        })
        .catch(err => console.error('Tarihsel sınırlar yüklenemedi:', err));

    // Katman toggle dinleyicileri
    document.getElementById('layer-trajan').addEventListener('change', function (e) {
        if (!trajanLayer) return;
        if (e.target.checked) {
            map.addLayer(trajanLayer);
        } else {
            map.removeLayer(trajanLayer);
        }
    });

    document.getElementById('layer-routes').addEventListener('change', function (e) {
        if (!routesLayer) return;
        if (e.target.checked) {
            map.addLayer(routesLayer);
        } else {
            map.removeLayer(routesLayer);
        }
    });

    // 5. Darphaneler GeoJSON Yükleme (/api/v1/mints/geojson)
    fetch('/api/v1/mints/geojson')
        .then(res => res.json())
        .then(geoJson => {
            mintGeoJsonData = geoJson;
            renderMintMarkers(geoJson);

            // Eğer URL parametresinde mint varsa doğrudan odaklan
            if (initialMintSlug && markerInstances[initialMintSlug]) {
                selectMint(initialMintSlug, true);
            }
        })
        .catch(err => console.error('Darphane verileri yüklenemedi:', err));

    function renderMintMarkers(geoJson) {
        markersLayer.clearLayers();
        markerInstances = {};

        geoJson.features.forEach(feature => {
            const coords = feature.geometry.coordinates; // [lng, lat]
            const props = feature.properties;

            const marker = L.marker([coords[1], coords[0]], {
                icon: createMintIcon(false),
                title: props.name
            });

            // Mini Popup İçeriği
            const popupContent = `
                <div class="p-2 min-w-[200px]">
                    <div class="font-bold text-amber-400 text-sm mb-0.5">${props.name}</div>
                    <div class="text-xs text-slate-300 italic mb-1">${props.ancient_name}</div>
                    <div class="text-[11px] text-slate-400 mb-2">${props.modern_name} &bull; ${props.active_period}</div>
                    <button onclick="window.selectMintBySlug('${props.slug}', false)" class="w-full text-center py-1 px-2 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded text-xs transition">
                        Detayları ve Sikkeleri Gör (${props.coins_count})
                    </button>
                </div>
            `;
            marker.bindPopup(popupContent);

            marker.on('click', () => {
                selectMint(props.slug, false);
            });

            markersLayer.addLayer(marker);
            markerInstances[props.slug] = {
                marker: marker,
                data: props,
                latLng: [coords[1], coords[0]]
            };
        });
    }

    // 6. Darphane Seçme ve Çekmeceyi Açma (Drawer)
    window.selectMintBySlug = function (slug, autoPan = true) {
        selectMint(slug, autoPan);
    };

    function selectMint(slug, autoPan = true) {
        const item = markerInstances[slug];
        if (!item) return;

        // Haritada pini vurgula ve odaklan
        Object.values(markerInstances).forEach(m => m.marker.setIcon(createMintIcon(false)));
        item.marker.setIcon(createMintIcon(true));

        if (autoPan) {
            map.flyTo(item.latLng, 8, { duration: 1.2 });
        }

        // Çekmece alanlarını doldur
        const data = item.data;
        document.getElementById('drawer-mint-name').textContent = data.name;
        document.getElementById('drawer-mint-ancient').textContent = data.ancient_name || data.name;
        document.getElementById('drawer-mint-modern').textContent = data.modern_name || '-';
        document.getElementById('drawer-mint-period').textContent = data.active_period || '-';
        document.getElementById('drawer-mint-significance').textContent = data.historical_significance || 'Bu darphane antik çağ boyunca Akdeniz ticaretinde önemli bir rol oynamıştır.';
        document.getElementById('drawer-coins-count').textContent = `${data.coins_count} Sikke Kayıtlı`;

        // Tek tıkla filtreye geçiş bağlantısı
        document.getElementById('drawer-view-all-link').href = data.filter_url || `/coins?mint=${data.slug}`;

        // En popüler 5 sikke listesini oluştur
        const topCoinsContainer = document.getElementById('drawer-top-coins-list');
        topCoinsContainer.innerHTML = '';

        if (data.top_coins && data.top_coins.length > 0) {
            data.top_coins.forEach(coin => {
                const card = document.createElement('a');
                card.href = coin.detail_url;
                card.className = 'flex items-center gap-3 p-2 bg-slate-800/80 hover:bg-slate-700/80 rounded-lg border border-slate-700/70 transition group';
                card.innerHTML = `
                    <img src="${coin.thumbnail_url}" alt="${coin.title}" class="w-12 h-12 object-cover rounded border border-slate-600 bg-slate-900 shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-xs text-slate-200 group-hover:text-amber-400 transition truncate">
                            ${coin.title}
                        </div>
                        <div class="text-[11px] text-slate-400 flex items-center gap-2 mt-0.5">
                            <span>${coin.denomination}</span>
                            <span class="text-amber-400/80 uppercase">${coin.metal}</span>
                            <span class="text-slate-500 font-mono">👁️ ${coin.view_count}</span>
                        </div>
                    </div>
                    <span class="text-xs text-slate-400 group-hover:text-amber-400">&rarr;</span>
                `;
                topCoinsContainer.appendChild(card);
            });
        } else {
            topCoinsContainer.innerHTML = `
                <div class="text-center py-6 text-xs text-slate-500 border border-dashed border-slate-800 rounded-lg">
                    Bu darphanede basılmış henüz katalog sikkesi bulunmuyor.
                </div>
            `;
        }

        // Çekmeceyi aç
        document.getElementById('mint-drawer').classList.remove('translate-x-full');
    }

    // Çekmeceyi Kapatma
    document.getElementById('close-drawer-btn').addEventListener('click', function () {
        document.getElementById('mint-drawer').classList.add('translate-x-full');
        Object.values(markerInstances).forEach(m => m.marker.setIcon(createMintIcon(false)));
    });

    // Sol Listeden Darphaneye Tıklama
    document.querySelectorAll('.mint-item').forEach(button => {
        button.addEventListener('click', function () {
            const slug = this.dataset.mintSlug;
            selectMint(slug, true);
        });
    });

    // Sol Panelde Darphane Arama Filtresi
    document.getElementById('mint-search-input').addEventListener('input', function (e) {
        const query = e.target.value.toLowerCase().trim();
        document.querySelectorAll('.mint-item').forEach(item => {
            const name = (item.dataset.mintName || '').toLowerCase();
            const ancient = (item.dataset.ancientName || '').toLowerCase();
            if (name.includes(query) || ancient.includes(query)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
</script>
@endsection

