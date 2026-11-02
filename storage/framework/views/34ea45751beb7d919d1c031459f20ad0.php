<div class="my-12 ios-glass-card p-6 md:p-10 text-center relative overflow-hidden">
    <!-- Ambient Blur -->
    <div class="absolute -top-16 -right-16 w-48 h-48 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-16 -left-16 w-48 h-48 bg-blue-500/10 dark:bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="inline-flex items-center gap-2 bg-amber-500/15 border border-amber-500/30 text-amber-700 dark:text-gold-antique text-xs px-3.5 py-1.5 rounded-full font-semibold uppercase tracking-wider mb-3 shadow-sm">
        <span>📜</span>
        <span>Haftalık Nümizmatik Bülteni</span>
    </div>

    <h3 class="text-xl md:text-3xl font-extrabold text-slate-900 dark:text-slate-100 font-numis mb-3 tracking-tight">
        "Haftanın Sikkesi" ile Tarihin Derinliklerine Yolculuk Edin
    </h3>
    <p class="text-slate-600 dark:text-slate-300 text-sm md:text-base max-w-xl mx-auto mb-7">
        Her Pazar sabahı nadir bir antik Roma, Bizans veya Grek sikkesinin tarihsel hikayesi, güncel müzayede satış analizleri ve epigrafik çözümleri e-posta kutunuzda.
    </p>

    <form id="newsletter-form" action="<?php echo e(route('newsletter.subscribe')); ?>" method="POST" class="max-w-md mx-auto flex flex-col sm:flex-row gap-2.5">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="source" value="<?php echo e($source ?? 'newsletter_card'); ?>">
        <input 
            type="email" 
            name="email" 
            required 
            placeholder="E-posta adresinizi girin..." 
            class="flex-1 px-4 py-3.5 bg-white/80 dark:bg-black/40 border border-black/10 dark:border-white/15 rounded-2xl text-slate-900 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:bg-white dark:focus:bg-black/60 focus:ring-2 focus:ring-amber-500/30 text-sm shadow-inner transition"
        >
        <button 
            type="submit" 
            class="px-7 py-3.5 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-bold text-sm rounded-2xl transition duration-200 shadow-lg shadow-amber-500/25 whitespace-nowrap active:scale-95"
        >
            Ücretsiz Abone Ol
        </button>
    </form>

    <div id="newsletter-feedback" class="mt-3 text-xs hidden"></div>

    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-4">
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
                    feedback.className = 'mt-3 text-xs text-emerald-600 dark:text-emerald-400 font-semibold';
                    feedback.innerText = '✅ ' + data.message;
                    feedback.classList.remove('hidden');
                    form.reset();
                } else {
                    feedback.className = 'mt-3 text-xs text-rose-600 dark:text-rose-400 font-semibold';
                    feedback.innerText = '⚠️ ' + (data.message || 'Lütfen geçerli bir e-posta adresi girin.');
                    feedback.classList.remove('hidden');
                }
            } catch (err) {
                feedback.className = 'mt-3 text-xs text-rose-600 dark:text-rose-400 font-semibold';
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