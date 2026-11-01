<?php $__env->startSection('title', 'Günün Sikkesi - Günlük Nümizmatik Bilgi Yarışması'); ?>
<?php $__env->startSection('meta_description', 'Her gün 1 gizemli antik sikke! Hükümdar ve lejantı doğru tahmin et, nümizmatik puanı ve rozetler kazan.'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-5xl mx-auto px-4 py-8">
    <!-- Üst Başlık ve Seri Göstergesi -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mb-8 pb-6 border-b border-slate-800">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-300 border border-amber-500/20 mb-2">
                <span>🎯</span> Günlük Nümizmatik Meydan Okuma
            </div>
            <h1 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight">
                Günün Gizemli Sikkesi
            </h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">
                Her gün yenilenen nümizmatik soruyu çözün, serinizi koruyun ve profil rozetleri kazanın.
            </p>
        </div>

        <!-- Kullanıcı Puan ve Seri Rozeti -->
        <div class="flex items-center gap-3 bg-slate-900 border border-slate-800 p-3 rounded-2xl shadow-xl shrink-0">
            <div class="text-center px-3 border-r border-slate-800">
                <span class="text-[10px] uppercase font-mono text-slate-500 block">Günlük Seri</span>
                <span class="text-lg font-black text-amber-400 flex items-center justify-center gap-1">
                    <span>🔥</span> <span id="user-streak-count"><?php echo e($streak->current_streak_days ?? 0); ?></span> Gün
                </span>
            </div>
            <div class="text-center px-3">
                <span class="text-[10px] uppercase font-mono text-slate-500 block">Nümizmatik Puanı</span>
                <span class="text-lg font-black text-white font-mono flex items-center justify-center gap-1">
                    <span>🪙</span> <span id="user-total-points"><?php echo e($user->numismatic_points ?? 0); ?></span>
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Sol Alan: Sikke Fotoğrafı ve Bilgi İpuçları -->
        <div class="lg:col-span-5 bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl flex flex-col items-center text-center">
            <span class="text-xs uppercase font-mono tracking-wider text-amber-400 font-bold bg-amber-500/10 px-3 py-1 rounded-full border border-amber-500/20 mb-4">
                <?php echo e($challenge['coin_hint']['period']); ?> &bull; <?php echo e($challenge['coin_hint']['denomination']); ?>

            </span>

            <!-- Gizemli Sikke Görseli -->
            <div class="w-full aspect-square bg-slate-950 rounded-xl p-4 flex items-center justify-center border border-slate-800/80 mb-4 shadow-inner relative group overflow-hidden">
                <img id="challenge-coin-image" src="<?php echo e($challenge['image_url']); ?>" alt="Günün Sikkesi" class="max-h-full max-w-full object-contain rounded-lg shadow-2xl transition duration-500 group-hover:scale-105">
                <div class="absolute bottom-2 left-2 bg-black/60 backdrop-blur text-[10px] text-slate-400 font-mono px-2 py-0.5 rounded">
                    Maden: <?php echo e(ucfirst($challenge['coin_hint']['metal'])); ?>

                </div>
            </div>

            <p class="text-xs text-slate-400 leading-relaxed">
                İpucu: Sikkenin portre hatlarını, saç dalgalarını ve lejant karakterlerini dikkatle inceleyin.
            </p>
        </div>

        <!-- Sağ Alan: Soru, Şıklar ve Sonuç Paneli -->
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 sm:p-8 shadow-xl">
                <!-- Soru Metni -->
                <h2 class="text-lg sm:text-xl font-bold text-white mb-6 flex items-start gap-2.5 leading-snug">
                    <span class="text-amber-400 text-2xl font-serif">❓</span>
                    <span><?php echo e($challenge['question_text']); ?></span>
                </h2>

                <!-- 4 Şıklı Seçenekler -->
                <div id="options-container" class="space-y-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $challenge['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" 
                                onclick="submitAnswer(<?php echo e($challenge['id']); ?>, <?php echo e($index); ?>, this)" 
                                class="quiz-option-btn w-full text-left p-4 rounded-xl bg-slate-950 border border-slate-800 hover:border-amber-500/60 hover:bg-slate-800/60 transition flex items-center justify-between group">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-700 flex items-center justify-center text-xs font-mono font-bold text-slate-300 group-hover:border-amber-500 group-hover:text-amber-400 transition">
                                    <?php echo e(['A', 'B', 'C', 'D'][$index] ?? $index + 1); ?>

                                </span>
                                <span class="text-sm font-semibold text-slate-200 group-hover:text-white transition">
                                    <?php echo e($option); ?>

                                </span>
                            </div>
                            <span class="text-slate-600 group-hover:text-amber-400 text-sm font-bold transition">&rarr;</span>
                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <!-- Sonuç Açıklama Kutusu (Cevaplandıktan Sonra Açılır) -->
                <div id="result-box" class="hidden mt-6 p-5 rounded-xl border transition-all duration-300">
                    <div class="flex items-center gap-2 mb-2">
                        <span id="result-status-icon" class="text-xl"></span>
                        <h3 id="result-status-title" class="font-bold text-base"></h3>
                    </div>
                    <p id="result-explanation-text" class="text-xs sm:text-sm text-slate-300 leading-relaxed mb-4"></p>

                    <!-- Yeni Rozet Bildirimi -->
                    <div id="badge-alert-box" class="hidden p-3 bg-amber-500/20 border border-amber-500/40 rounded-xl mb-4 flex items-center gap-3 text-amber-200 text-xs">
                        <span class="text-2xl">🏆</span>
                        <div>
                            <strong class="block text-amber-300 text-sm">Tebrikler! Yeni Rozet Kazandınız!</strong>
                            <span id="badge-alert-text">Profilinize yeni bir nümizmat rozeti eklendi.</span>
                        </div>
                    </div>

                    <!-- Sosyal Paylaşım Butonları -->
                    <div class="pt-4 border-t border-slate-800/80">
                        <span class="text-xs text-slate-400 block mb-2.5 font-semibold">Skorunu Arkadaşlarınla Paylaş:</span>
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- WhatsApp -->
                            <a id="share-whatsapp-btn" href="#" target="_blank" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                                <span>📱 WhatsApp</span>
                            </a>
                            <!-- X (Twitter) -->
                            <a id="share-x-btn" href="#" target="_blank" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-lg text-xs font-bold transition border border-slate-700 flex items-center gap-1.5">
                                <span>🐦 X (Twitter)</span>
                            </a>
                            <!-- Görsel İndir -->
                            <a id="share-image-btn" href="<?php echo e(route('quiz.share-card')); ?>" target="_blank" class="px-3.5 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-lg text-xs font-bold transition flex items-center gap-1.5">
                                <span>🖼️ Paylaşım Kartı</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kullanıcı Kazanılan Rozetler Şeridi -->
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
                    <h3 class="text-xs uppercase font-bold text-slate-300 tracking-wider flex items-center gap-1.5">
                        <span>🏆</span> Nümizmatik Başarı Rozetleriniz
                    </h3>
                    <span class="text-xs text-amber-400 font-mono"><?php echo e($achievements->count()); ?> / 4 Rozet</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                    <?php
                        $badges = [
                            'novice_collector' => ['name' => 'Çırak Nümizmat', 'icon' => '🪙', 'desc' => 'İlk doğru cevap'],
                            'roman_expert' => ['name' => 'Roma Uzmanı', 'icon' => '🏛️', 'desc' => 'Roma serisi'],
                            'legend_reader' => ['name' => 'Epigrafi Ustası', 'icon' => '📜', 'desc' => 'Lejant çözümü'],
                            'keen_eye' => ['name' => 'Keskin Göz', 'icon' => '🦅', 'desc' => 'Hata ve sahte tespiti'],
                        ];
                        $unlockedTypes = $achievements->pluck('badge_type')->toArray();
                    ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $badges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $isUnlocked = in_array($type, $unlockedTypes); ?>
                        <div class="p-3 rounded-xl border <?php echo e($isUnlocked ? 'bg-amber-500/10 border-amber-500/40 text-amber-300' : 'bg-slate-950/40 border-slate-800/80 text-slate-600 opacity-50'); ?> transition">
                            <span class="text-2xl block mb-1"><?php echo e($info['icon']); ?></span>
                            <span class="text-xs font-bold block truncate <?php echo e($isUnlocked ? 'text-white' : 'text-slate-500'); ?>"><?php echo e($info['name']); ?></span>
                            <span class="text-[10px] text-slate-500 block truncate"><?php echo e($info['desc']); ?></span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let hasAnswered = false;

function submitAnswer(questionId, selectedIndex, buttonElement) {
    if (hasAnswered) return;
    hasAnswered = true;

    // Tüm butonları devre dışı bırak
    document.querySelectorAll('.quiz-option-btn').forEach(btn => {
        btn.disabled = true;
        btn.classList.add('opacity-70', 'cursor-not-allowed');
    });

    fetch('<?php echo e(route('quiz.answer')); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({
            question_id: questionId,
            selected_option: selectedIndex
        })
    })
    .then(res => res.json())
    .then(data => {
        const resultBox = document.getElementById('result-box');
        const iconEl = document.getElementById('result-status-icon');
        const titleEl = document.getElementById('result-status-title');
        const expEl = document.getElementById('result-explanation-text');
        const buttons = document.querySelectorAll('.quiz-option-btn');

        resultBox.classList.remove('hidden');

        // Doğru ve yanlış butonları stillendir
        buttons.forEach((btn, idx) => {
            if (idx === data.correct_option_index) {
                btn.classList.remove('bg-slate-950', 'border-slate-800');
                btn.classList.add('bg-emerald-950/80', 'border-emerald-500', 'text-emerald-200');
            } else if (idx === selectedIndex && !data.is_correct) {
                btn.classList.remove('bg-slate-950', 'border-slate-800');
                btn.classList.add('bg-red-950/80', 'border-red-500', 'text-red-200');
            }
        });

        if (data.is_correct) {
            resultBox.classList.add('bg-emerald-950/40', 'border-emerald-500/50');
            iconEl.textContent = '🎉';
            titleEl.textContent = 'Tebrikler! Doğru Cevap (+50 Puan)';
            titleEl.className = 'font-bold text-base text-emerald-400';
        } else {
            resultBox.classList.add('bg-red-950/40', 'border-red-500/50');
            iconEl.textContent = '❌';
            titleEl.textContent = 'Maalesef Yanlış Cevap';
            titleEl.className = 'font-bold text-base text-red-400';
        }

        expEl.textContent = data.explanation;

        // Sayı sayaçlarını güncelle
        if (data.current_streak_days) {
            document.getElementById('user-streak-count').textContent = data.current_streak_days;
        }
        if (data.total_points) {
            document.getElementById('user-total-points').textContent = data.total_points;
        }

        // Yeni rozet açıldıysa bildir
        if (data.new_badge) {
            const badgeBox = document.getElementById('badge-alert-box');
            badgeBox.classList.remove('hidden');
        }

        // Sosyal Paylaşım Linkleri
        const shareText = encodeURIComponent(data.share_text);
        const shareUrl = encodeURIComponent(window.location.href);

        document.getElementById('share-whatsapp-btn').href = `https://api.whatsapp.com/send?text=${shareText}%20${shareUrl}`;
        document.getElementById('share-x-btn').href = `https://twitter.com/intent/tweet?text=${shareText}&url=${shareUrl}`;
        if (data.share_card_url) {
            document.getElementById('share-image-btn').href = data.share_card_url;
        }
    })
    .catch(err => {
        console.error('Cevap gönderilemedi:', err);
    });
}
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\taner\Documents\GitHub\Tarih\resources\views/quiz/index.blade.php ENDPATH**/ ?>