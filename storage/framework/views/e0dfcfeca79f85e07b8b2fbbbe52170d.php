<?php $__env->startSection('title', 'Tarihsel Dönemler - Antik Sikke Ansiklopedisi'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="font-numis text-2xl sm:text-3xl font-bold text-slate-100 tracking-wide mb-2">
            Tarihsel Nümizmatik Dönemler
        </h1>
        <p class="text-slate-400 text-sm">
            Klasik Grek, Helenistik, Roma Cumhuriyeti, Roma İmparatorluğu ve Bizans sikke basım dönemleri.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $period): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('periods.show', $period->slug)); ?>" class="bg-slate-900 border border-slate-800 hover:border-amber-500/50 rounded-2xl p-6 transition group flex flex-col justify-between">
                <div>
                    <span class="text-xs font-mono text-amber-400 block mb-2"><?php echo e($period->formatted_date_range); ?></span>
                    <h2 class="font-numis font-bold text-slate-100 text-lg mb-2 group-hover:text-amber-400 transition">
                        <?php echo e($period->name); ?>

                    </h2>
                    <p class="text-xs text-slate-400 line-clamp-3 leading-relaxed mb-4">
                        <?php echo e($period->description); ?>

                    </p>
                </div>
                <div class="pt-4 border-t border-slate-800 flex items-center justify-between text-xs text-slate-400">
                    <span><?php echo e($period->coins_count); ?> Sikke Kaydı</span>
                    <span><?php echo e($period->rulers_count); ?> Hükümdar</span>
                </div>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\taner\Documents\GitHub\Tarih\resources\views/periods/index.blade.php ENDPATH**/ ?>