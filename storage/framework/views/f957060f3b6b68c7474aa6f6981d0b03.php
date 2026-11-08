<?php $__env->startSection('title', $proyek->nama); ?>

<?php
    $rp = fn ($n) => 'Rp '.number_format((int) $n, 0, ',', '.');
    $kelolaRab = \App\Support\Izin::kelolaRab($user);
    $tabs = ['ringkasan' => 'Ringkasan', 'rab' => 'Struktur & RAB', 'material' => 'Kebutuhan Material', 'log' => 'Log Harian', 'belanja' => 'Belanja'];
?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-5">
    <div>
        <div class="text-muted fs-7"><?php echo e($proyek->kode); ?> · <?php echo e($proyek->lokasi ?: 'Lokasi belum diisi'); ?></div>
        <h2 class="fw-bolder mb-0"><?php echo e($proyek->nama); ?></h2>
    </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\App\Support\Izin::kelolaKegiatan($user)): ?>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('tim.proyek.edit', [$tim->slug, $proyek])); ?>" class="btn btn-sm btn-light">Ubah proyek</a>
            <form method="POST" action="<?php echo e(route('tim.proyek.destroy', [$tim->slug, $proyek])); ?>" data-confirm="Hapus proyek ini beserta seluruh RAB-nya?">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-light-danger">Hapus</button>
            </form>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<ul class="nav nav-tabs nav-line-tabs fs-6 fw-semibold mb-6 flex-nowrap overflow-auto">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <li class="nav-item"><a class="nav-link text-nowrap <?php echo e($tab === $k ? 'active' : ''); ?>" href="<?php echo e(route('tim.proyek.show', [$tim->slug, $proyek, 'tab' => $k])); ?>"><?php echo e($label); ?></a></li>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
</ul>

<?php echo $__env->renderWhen(array_key_exists($tab, $tabs), 'tim.proyek.tab_'.$tab, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('tim.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/proyek/show.blade.php ENDPATH**/ ?>