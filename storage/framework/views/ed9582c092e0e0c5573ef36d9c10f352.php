<?php $__env->startSection('title', 'Analisa Harga Satuan'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-5">
    <p class="text-muted mb-0" style="max-width:700px">Analisa (AHSP) adalah resep kebutuhan untuk 1 satuan pekerjaan, mis. 1 m² dinding bata = 70 bata + 0,23 zak semen + upah. Harga satuan dihitung dari harga katalog terkini.</p>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($boleh): ?><a href="<?php echo e(route('tim.analisa.create', $tim->slug)); ?>" class="btn btn-sm btn-primary">+ Analisa baru</a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $analisa->groupBy(fn ($a) => $a->kategori?->label()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat => $daftar): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
    <div class="card shadow-sm mb-5">
        <div class="card-header"><h3 class="card-title fw-bold"><?php echo e($kat); ?></h3></div>
        <div class="card-body pt-0">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $daftar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="border-bottom py-4">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <div><span class="text-muted fs-8"><?php echo e($a->kode); ?></span> <span class="fw-bold"><?php echo e($a->nama); ?></span></div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bolder" style="color:var(--tp-primary)">Rp <?php echo e(number_format($a->hargaSatuan(), 0, ',', '.')); ?> / <?php echo e($a->satuan); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($boleh): ?>
                                <a href="<?php echo e(route('tim.analisa.edit', [$tim->slug, $a])); ?>" class="btn btn-sm btn-light py-1">Ubah</a>
                                <form method="POST" action="<?php echo e(route('tim.analisa.destroy', [$tim->slug, $a])); ?>" data-confirm="Hapus analisa ini? Item RAB yang memakainya tetap menyimpan rinciannya."><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-light-danger py-1">Hapus</button></form>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                    <div class="text-muted fs-8 mt-1">
                        <?php echo e($a->items->map(fn ($i) => rtrim(rtrim(number_format($i->koefisien, 4, ',', '.'), '0'), ',').' '.$i->material?->satuan.' '.$i->material?->nama)->implode(' · ')); ?>

                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    <div class="card"><div class="card-body text-center text-muted py-10">Belum ada analisa.</div></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('tim.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/katalog/analisa.blade.php ENDPATH**/ ?>