<div class="my-10 bg-gradient-to-r from-amber-950/40 via-slate-900 to-amber-950/40 border border-amber-500/30 rounded-2xl p-6 md:p-8 text-center relative overflow-hidden shadow-2xl">
    <div class="absolute -top-12 -right-12 w-36 h-36 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>
    <div class="absolute -bottom-12 -left-12 w-36 h-36 bg-amber-500/10 rounded-full blur-2xl pointer-events-none"></div>

    <div class="inline-flex items-center gap-2 bg-amber-500/10 border border-amber-500/30 text-amber-400 text-xs px-3 py-1 rounded-full font-semibold uppercase tracking-wider mb-3">
        <span>📜</span>
        <span>Haftalık Nümizmatik Bülteni</span>
    </div>

    <h3 class="text-xl md:text-2xl font-bold text-white mb-2">
        "Haftanın Sikkesi" ile Tarihin Derinliklerine Yolculuk Edin
    </h3>
    <p class="text-slate-300 text-sm md:text-base max-w-xl mx-auto mb-6">
        Her Pazar sabahı nadir bir antik Roma, Bizans veya Grek sikkesinin tarihsel hikayesi, güncel müzayede satış analizleri ve epigrafik çözümleri e-posta kutunuzda.
    </p>

    <form id="newsletter-form" action="<?php echo e(route('newsletter.subscribe')); ?>" method="POST" class="max-w-md mx-auto flex flex-col sm:flex-row gap-2">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="source" value="<?php echo e($source ?? 'newsletter_card'); ?>">
        <input 
            type="email" 
            name="email" 
            required 
            placeholder="E-posta adresinizi girin..." 
            class="flex-1 px-4 py-3 bg-slate-950 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-sm transition"
        >
        <button 
            type="submit" 
            class="px-6 py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm rounded-xl transition duration-200 shadow-lg shadow-amber-500/20 whitespace-nowrap"
        >
            Ücretsiz Abone Ol
        </button>
    </form>

    <div id="newsletter-feedback" class="mt-3 text-xs hidden"></div>

    <p class="text-[11px] text-slate-500 mt-4">
        🔒 Spam göndermiyoruz. İstediğiniz an tek tıkla ayrılabilirsiniz. 2863 sayılı kanun uyarınca salt kültürel ve nümizmatik araştırma amaçlıdır.
    </p>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('newsletter-form');
    const feedback = document.getElementById('newsletter-feedback');

    if (form && feedback) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const emailInput = form.querySelector('input[name="email"]');
            const submitBtn = form.querySelector('button[type="submit"]');

            submitBtn.disabled = true;
            submitBtn.innerText = 'Kaydediliyor...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        email: emailInput.value,
                        source: form.querySelector('input[name="source"]').value
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    feedback.className = 'mt-3 text-xs text-green-400 font-semibold';
                    feedback.innerText = '✅ ' + data.message;
                    feedback.classList.remove('hidden');
                    form.reset();
                } else {
                    feedback.className = 'mt-3 text-xs text-red-400 font-semibold';
                    feedback.innerText = '⚠️ ' + (data.message || 'Lütfen geçerli bir e-posta adresi girin.');
                    feedback.classList.remove('hidden');
                }
            } catch (err) {
                feedback.className = 'mt-3 text-xs text-red-400 font-semibold';
                feedback.innerText = '⚠️ Bir bağlantı hatası oluştu, lütfen tekrar deneyin.';
                feedback.classList.remove('hidden');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerText = 'Ücretsiz Abone Ol';
            }
        });
    }
});
</script>

<?php /**PATH C:\Users\taner\Documents\GitHub\Tarih\resources\views/components/newsletter-card.blade.php ENDPATH**/ ?>