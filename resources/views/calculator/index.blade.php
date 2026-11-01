@extends('layouts.app')

@section('title', 'Antik Sikke Tarih ve Regnal Yıl Hesaplayıcı')
@section('meta_description', 'Roma İmparatorluk unvanları (TR P, COS), Grek Seleukos takvimi (A.S.) ve Osmanlı Cülus yıllarını kesin Miladi yıla çeviren nümizmatik hesaplayıcı.')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <!-- Başlık ve Tanıtım -->
    <div class="mb-8 text-center">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-300 border border-amber-500/20 mb-3">
            <span>⏳</span> Nümizmatik Kronoloji Motoru
        </span>
        <h1 class="text-3xl font-extrabold text-white tracking-tight sm:text-4xl">
            Antik Sikke Tarih ve Regnal Yıl Hesaplayıcı
        </h1>
        <p class="mt-3 text-slate-400 text-sm max-w-2xl mx-auto">
            Roma imparatorlarının tribünlük yetkileri (<span class="font-mono text-amber-300">TR P</span>) ve konsüllükleri (<span class="font-mono text-amber-300">COS</span>), Grek Seleukos (<span class="font-mono text-amber-300">A.S.</span>) çağı ve Osmanlı cülus senelerini kesin Miladi yıla dönüştürün.
        </p>
    </div>

    <!-- Sistem Seçim Sekmeleri (Tabs) -->
    <div class="flex border-b border-slate-800 mb-6 bg-slate-900/60 p-1 rounded-xl">
        <button type="button" onclick="switchTab('roman')" id="tab-btn-roman" class="tab-btn flex-1 py-2.5 px-3 rounded-lg text-xs sm:text-sm font-bold text-center transition flex items-center justify-center gap-2 bg-amber-500 text-slate-950 shadow-md">
            <span>🏛️</span> Roma Unvanları (TR P / COS)
        </button>
        <button type="button" onclick="switchTab('seleucid')" id="tab-btn-seleucid" class="tab-btn flex-1 py-2.5 px-3 rounded-lg text-xs sm:text-sm font-semibold text-slate-400 hover:text-slate-200 text-center transition flex items-center justify-center gap-2">
            <span>🏺</span> Grek / Seleukos (A.S.)
        </button>
        <button type="button" onclick="switchTab('islamic')" id="tab-btn-islamic" class="tab-btn flex-1 py-2.5 px-3 rounded-lg text-xs sm:text-sm font-semibold text-slate-400 hover:text-slate-200 text-center transition flex items-center justify-center gap-2">
            <span>🌙</span> Osmanlı Cülus (Sene-i Cülus)
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Sol Form Alanı -->
        <div class="lg:col-span-7 bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">

            <!-- 1. ROMA MODÜLÜ FORMU -->
            <form id="form-roman" class="calculator-form space-y-5" onsubmit="event.preventDefault(); calculateDate('roman');">
                <input type="hidden" name="system" value="roman">

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                        1. Roma İmparatoru Seçin
                    </label>
                    <select id="roman-ruler-select" name="ruler" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-amber-400 transition">
                        @foreach($romanEmperors as $emp)
                            <option value="{{ $emp['slug'] }}" {{ $emp['slug'] === 'trajan' ? 'selected' : '' }}>
                                {{ $emp['name'] }} ({{ $emp['reign'] }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                            2. TR P (Tribünlük Sayısı)
                        </label>
                        <input type="number" id="roman-trp-input" name="tr_p" value="9" min="1" max="40" placeholder="Örn: 9 (TR P IX)" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-amber-400 transition font-mono">
                        <span class="text-[11px] text-slate-500 mt-1 block">Yıllık yenilenen unvan</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                            3. COS (Konsüllük Sayısı)
                        </label>
                        <input type="number" id="roman-cos-input" name="cos" value="5" min="1" max="20" placeholder="Örn: 5 (COS V)" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-amber-400 transition font-mono">
                        <span class="text-[11px] text-slate-500 mt-1 block">Konsüllük dönemi</span>
                    </div>
                </div>

                <!-- Veya Ham Metin Girişi -->
                <div class="pt-2 border-t border-slate-800">
                    <label class="block text-xs font-semibold text-slate-400 mb-1">
                        Veya Sikke Lejant Kısaltmasını Yapıştırın:
                    </label>
                    <input type="text" id="roman-raw-input" name="raw_titles" placeholder="Örn: TR P IX COS IIII veya TR P XVII COS VI" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-slate-300 focus:outline-none focus:border-amber-400 transition font-mono">
                </div>

                <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl text-sm transition shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2">
                    <span>⚡ Kesin Miladi Tarihi Hesapla</span>
                </button>
            </form>

            <!-- 2. GREK / SELEUKOS MODÜLÜ FORMU -->
            <form id="form-seleucid" class="calculator-form space-y-5 hidden" onsubmit="event.preventDefault(); calculateDate('seleucid');">
                <input type="hidden" name="system" value="seleucid">

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                        Seleukos Yılı (A.S. - Anno Seleuci) veya Grek Harfi
                    </label>
                    <input type="text" id="seleucid-input" name="as_year" value="112" placeholder="Örn: 112 veya ΒΙΡ, ΣΞΓ" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-3 text-base text-slate-100 focus:outline-none focus:border-amber-400 transition font-mono tracking-wider">
                    <span class="text-xs text-slate-500 mt-1.5 block">
                        Seleukos Takvimi M.Ö. 312 sonbaharında başlar. Hem sayısal (112) hem de Grek alfabesi rakamları (ΒΙΡ) desteklenir.
                    </span>
                </div>

                <!-- Grek Rakam Hızlı Tuşları -->
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">
                        Hızlı Grek Harfleri (Tıklayarak Ekleyin)
                    </label>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach(['Α'=>'1', 'Β'=>'2', 'Γ'=>'3', 'Δ'=>'4', 'Ε'=>'5', 'Ι'=>'10', 'Κ'=>'20', 'Λ'=>'30', 'Μ'=>'40', 'Ρ'=>'100', 'Σ'=>'200', 'Τ'=>'300'] as $gChar => $val)
                            <button type="button" onclick="appendGreekChar('{{ $gChar }}')" class="px-2 py-1 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs rounded border border-slate-700 font-mono transition" title="{{ $gChar }} = {{ $val }}">
                                {{ $gChar }} <span class="text-[10px] text-slate-500">({{ $val }})</span>
                            </button>
                        @endforeach
                        <button type="button" onclick="document.getElementById('seleucid-input').value = ''" class="px-2 py-1 bg-red-950/40 hover:bg-red-900/60 text-red-300 text-xs rounded border border-red-800/50 transition">
                            Temizle
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl text-sm transition shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2">
                    <span>⚡ Miladi Karşılığını Bul</span>
                </button>
            </form>

            <!-- 3. OSMANLI / İSLAMİ MODÜLÜ FORMU -->
            <form id="form-islamic" class="calculator-form space-y-5 hidden" onsubmit="event.preventDefault(); calculateDate('islamic');">
                <input type="hidden" name="system" value="islamic">

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                        1. Padişah / Sultan Seçin
                    </label>
                    <select id="islamic-sultan-select" name="sultan" onchange="updateAccessionYearFromSultan(this)" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-amber-400 transition">
                        @foreach($ottomanSultans as $sultan)
                            <option value="{{ $sultan['slug'] }}" data-accession="{{ $sultan['accession_hijri'] }}" {{ $sultan['slug'] === 'mahmud-ii' ? 'selected' : '' }}>
                                {{ $sultan['name'] }} (Cülus: {{ $sultan['accession_hijri'] }} H. / {{ $sultan['accession_gregorian'] }} M.)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                            2. Cülus Yılı (Hicri)
                        </label>
                        <input type="number" id="islamic-accession-input" name="accession_hijri" value="1223" min="699" max="1342" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-amber-400 transition font-mono">
                        <span class="text-[11px] text-slate-500 mt-1 block">Örn: 1223 (II. Mahmud)</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                            3. Sene-i Cülus (Saltanat Yılı)
                        </label>
                        <input type="number" id="islamic-regnal-input" name="regnal_year" value="15" min="1" max="50" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-sm text-slate-100 focus:outline-none focus:border-amber-400 transition font-mono font-bold text-amber-400">
                        <span class="text-[11px] text-slate-500 mt-1 block">Sikke üzerindeki sene (Örn: 15)</span>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold rounded-xl text-sm transition shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2">
                    <span>⚡ Kesin Miladi Yılı Hesapla</span>
                </button>
            </form>
        </div>

        <!-- Sağ Sonuç Kartı (Interactive Date Result Widget) -->
        <div class="lg:col-span-5 space-y-4">
            <div id="result-card" class="bg-gradient-to-b from-slate-900 to-slate-950 border-2 border-amber-500/40 rounded-2xl p-6 shadow-2xl relative overflow-hidden">
                <!-- Arka plan parlama efekti -->
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs uppercase font-mono tracking-wider text-amber-400 font-bold bg-amber-500/10 px-2.5 py-1 rounded-md border border-amber-500/20">
                        HESAPLANAN TARİH
                    </span>
                    <span id="result-system-badge" class="text-xs text-slate-400 font-medium">
                        Roma İmparatorluğu
                    </span>
                </div>

                <!-- Büyük Sonuç İfadesi (Verdict) -->
                <div id="result-verdict" class="text-xl sm:text-2xl font-black text-amber-300 leading-snug mb-3">
                    {{ $defaultCalculation['verdict'] ?? 'Bu sikke tam olarak M.S. 104-105 kışında basılmıştır.' }}
                </div>

                <!-- Detay Rozetleri -->
                <div class="grid grid-cols-2 gap-2 my-4 text-xs font-mono">
                    <div class="bg-slate-800/80 p-2.5 rounded-lg border border-slate-700/60">
                        <span class="text-slate-400 text-[10px] block font-sans">Miladi Tarih Aralığı</span>
                        <span id="result-gregorian-range" class="text-sm font-bold text-white">
                            {{ $defaultCalculation['formatted_date'] ?? 'M.S. 105' }}
                        </span>
                    </div>
                    <div class="bg-slate-800/80 p-2.5 rounded-lg border border-slate-700/60">
                        <span class="text-slate-400 text-[10px] block font-sans">Tahmini Mevsim / Dönem</span>
                        <span id="result-season" class="text-sm font-bold text-amber-400">
                            {{ $defaultCalculation['exact_season'] ?? 'Kış Dönemi' }}
                        </span>
                    </div>
                </div>

                <!-- Bilimsel ve Epigrafik Açıklama -->
                <div class="bg-slate-950/70 p-3.5 rounded-xl border border-slate-800/80 mt-4">
                    <h4 class="text-xs font-bold text-slate-300 mb-1.5 flex items-center gap-1.5">
                        <span>📜</span> Kronolojik Eşleştirme Detayı:
                    </h4>
                    <p id="result-explanation" class="text-xs text-slate-400 leading-relaxed">
                        {{ $defaultCalculation['explanation'] ?? 'Yükleniyor...' }}
                    </p>
                </div>

                <!-- İpucu Kutusu -->
                <div class="mt-4 pt-3 border-t border-slate-800/60 flex items-center justify-between text-[11px] text-slate-500">
                    <span>💡 Nümizmatik takvim formülleri baz alınmıştır.</span>
                    <button type="button" onclick="copyResultText()" class="hover:text-amber-400 transition flex items-center gap-1">
                        <span>📋</span> Kopyala
                    </button>
                </div>
            </div>

            <!-- Yardımcı Bilgi Kartı -->
            <div class="bg-slate-900/60 border border-slate-800 rounded-xl p-4 text-xs text-slate-400 space-y-2">
                <div class="font-bold text-slate-300 flex items-center gap-1">
                    <span>📚</span> Hızlı Referans İpuçları
                </div>
                <p>
                    <strong>TR P (Tribunicia Potestas):</strong> Roma'da yıllık yenilenen tribünlük yetkisidir. Çoğu imparatorda her yıl 10 Aralık'ta yenilenir.
                </p>
                <p>
                    <strong>COS (Consul):</strong> İmparatorun konsüllük makamıdır. Bir kez alındığında yeni bir konsüllüğe kadar sikke üzerinde sabit kalır.
                </p>
                <p>
                    <strong>Cülus & Sene-i Cülus:</strong> Osmanlı paralarında tahta çıkış yılı (Örn: 1223) sabit darbedilir, saltanat yılı (Örn: 15) sikkenin arka yüzünde belirtilir.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
let currentActiveSystem = 'roman';

function switchTab(system) {
    currentActiveSystem = system;

    // Buton stillerini güncelle
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('bg-amber-500', 'text-slate-950', 'shadow-md');
        btn.classList.add('text-slate-400');
    });
    const activeBtn = document.getElementById(`tab-btn-${system}`);
    activeBtn.classList.remove('text-slate-400');
    activeBtn.classList.add('bg-amber-500', 'text-slate-950', 'shadow-md');

    // Formları gizle/göster
    document.querySelectorAll('.calculator-form').forEach(f => f.classList.add('hidden'));
    document.getElementById(`form-${system}`).classList.remove('hidden');

    // Hesaplamayı otomatik tetikle
    calculateDate(system);
}

function updateAccessionYearFromSultan(selectElem) {
    const selectedOption = selectElem.options[selectElem.selectedIndex];
    const accession = selectedOption.dataset.accession;
    if (accession) {
        document.getElementById('islamic-accession-input').value = accession;
        calculateDate('islamic');
    }
}

function appendGreekChar(char) {
    const input = document.getElementById('seleucid-input');
    input.value += char;
    calculateDate('seleucid');
}

function calculateDate(system) {
    let payload = { system: system };

    if (system === 'roman') {
        payload.ruler = document.getElementById('roman-ruler-select').value;
        payload.tr_p = document.getElementById('roman-trp-input').value;
        payload.cos = document.getElementById('roman-cos-input').value;
        payload.raw_titles = document.getElementById('roman-raw-input').value;
    } else if (system === 'seleucid') {
        payload.as_year = document.getElementById('seleucid-input').value;
    } else if (system === 'islamic') {
        payload.sultan = document.getElementById('islamic-sultan-select').value;
        payload.accession_hijri = document.getElementById('islamic-accession-input').value;
        payload.regnal_year = document.getElementById('islamic-regnal-input').value;
    }

    fetch('/api/v1/dating/convert', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(response => {
        if (response.success && response.data) {
            const data = response.data;
            document.getElementById('result-verdict').textContent = data.verdict || data.formatted_date;
            document.getElementById('result-gregorian-range').textContent = data.formatted_date;
            document.getElementById('result-season').textContent = data.exact_season || data.era || 'Kesin Yıl';
            document.getElementById('result-explanation').textContent = data.explanation;

            const badgeNames = {
                'roman': 'Roma İmparatorluğu',
                'seleucid': 'Grek / Seleukos Çağı',
                'islamic_ottoman': 'Osmanlı & İslami'
            };
            document.getElementById('result-system-badge').textContent = badgeNames[data.system] || data.system;
        } else {
            document.getElementById('result-verdict').textContent = response.message || 'Hesaplama yapılamadı.';
        }
    })
    .catch(err => {
        console.error('Hesaplama hatası:', err);
    });
}

function copyResultText() {
    const verdict = document.getElementById('result-verdict').textContent;
    const explanation = document.getElementById('result-explanation').textContent;
    navigator.clipboard.writeText(`${verdict} - ${explanation}`).then(() => {
        alert('Hesaplama sonucu panoya kopyalandı.');
    });
}

// Sayfa yüklendiğinde otomatik dinleyiciler
document.addEventListener('DOMContentLoaded', function () {
    // Roma form alanlarında değişiklikte canlı hesapla
    ['roman-ruler-select', 'roman-trp-input', 'roman-cos-input', 'roman-raw-input'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', () => calculateDate('roman'));
            el.addEventListener('change', () => calculateDate('roman'));
        }
    });

    // Seleukos alanında
    document.getElementById('seleucid-input')?.addEventListener('input', () => calculateDate('seleucid'));

    // Osmanlı alanlarında
    ['islamic-accession-input', 'islamic-regnal-input'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', () => calculateDate('islamic'));
    });
});
</script>
@endsection

