<!-- 2863 Sayılı Kanun Otomatik Hukuki Aydınlatma ve Onay Modalı -->
<div id="legalModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm hidden" role="dialog" aria-modal="true" aria-labelledby="legalModalTitle">
    <div class="relative w-full max-w-lg bg-slate-900 border border-amber-500/40 rounded-2xl p-6 sm:p-8 shadow-2xl text-slate-200">
        <!-- İkon -->
        <div class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center mx-auto mb-4 text-amber-400">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 1.944A11.954 11.954 0 012.166 5C2.056 5.649 2 6.319 2 7c0 5.225 3.34 9.67 8 11.317C14.66 16.67 18 12.225 18 7c0-.682-.057-1.35-.166-2.001A11.954 11.954 0 0110 1.944zM11 14a1 1 0 11-2 0 1 1 0 012 0zm0-7a1 1 0 10-2 0v3a1 1 0 102 0V7z" clip-rule="evenodd" />
            </svg>
        </div>

        <h2 id="legalModalTitle" class="font-numis text-lg font-bold text-slate-100 text-center tracking-wide mb-3">
            Kültür Varlıkları ve Hukuki Aydınlatma Metni
        </h2>

        <!-- Yasal Metin -->
        <div class="bg-slate-950/80 border border-slate-800 rounded-xl p-4 text-xs text-slate-300 leading-relaxed max-h-48 overflow-y-auto mb-4">
            <p class="mb-2 font-semibold text-amber-300">
                2863 Sayılı Kültür ve Tabiat Varlıklarını Koruma Kanunu Uyarınca:
            </p>
            <p class="mb-2">
                Bu platform yalnızca <strong>akademik araştırma, nümizmatik eğitim ve referans amaçlıdır</strong>. Platform üzerinden hiçbir şekilde kültür varlığı alım-satımı, aracılığı veya definecilik faaliyetine izin verilmez.
            </p>
            <p class="mb-2">
                Türkiye Cumhuriyeti sınırları içindeki kültür varlıklarının mülkiyeti, tescili ve bildirimi <strong>2863 sayılı kanun hükümlerine</strong> tabidir. Yasal süresi içinde müzeye bildirilmeyen kültür varlıkları üzerinde hak iddia edilemez ve ticarete konu edilemez.
            </p>
            <p>
                Platformumuzda alım-satım teklifinde bulunmak, define/dedektör araması yapmak veya bu amaçla kullanıcılarla iletişim kurmaya çalışmak kesinlikle yasaktır ve tespiti halinde hesap kalıcı olarak kapatılır.
            </p>
        </div>

        <!-- Onay Kutusu -->
        <label class="flex items-start gap-3 cursor-pointer text-xs text-slate-300 mb-6 select-none">
            <input type="checkbox" id="legalCheckbox" class="mt-0.5 rounded border-slate-700 bg-slate-950 text-amber-500 focus:ring-amber-500">
            <span>2863 sayılı kanun hükümlerini, platform kurallarını ve kullanım şartlarını okudum, anladım ve kabul ediyorum.</span>
        </label>

        <!-- Kabul Butonu -->
        <button type="button" id="acceptLegalBtn" disabled class="w-full bg-slate-800 text-slate-500 font-bold py-3 px-4 rounded-xl text-xs transition cursor-not-allowed">
            Kabul Ediyorum ve Devam Et
        </button>
    </div>
</div>

<script>
    (function() {
        const modal = document.getElementById('legalModal');
        const checkbox = document.getElementById('legalCheckbox');
        const btn = document.getElementById('acceptLegalBtn');
        const storageKey = 'numis_legal_acknowledged_v1';

        // Daha önce onaylanmamışsa modalı göster
        if (!localStorage.getItem(storageKey)) {
            modal.classList.remove('hidden');
        }

        checkbox.addEventListener('change', function() {
            if (this.checked) {
                btn.disabled = false;
                btn.className = 'w-full bg-amber-500 hover:bg-amber-450 text-slate-950 font-bold py-3 px-4 rounded-xl text-xs transition cursor-pointer shadow-lg shadow-amber-500/20';
            } else {
                btn.disabled = true;
                btn.className = 'w-full bg-slate-800 text-slate-500 font-bold py-3 px-4 rounded-xl text-xs transition cursor-not-allowed';
            }
        });

        btn.addEventListener('click', function() {
            if (checkbox.checked) {
                localStorage.setItem(storageKey, 'true');
                modal.classList.add('hidden');
            }
        });
    })();
</script>

