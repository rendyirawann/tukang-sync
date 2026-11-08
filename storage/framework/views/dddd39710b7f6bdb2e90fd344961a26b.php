<?php $__env->startSection('title', 'Proyek & RAB'); ?>

<?php $__env->startSection('content'); ?>
<div class="card shadow-sm">
    <div class="card-header align-items-center flex-wrap gap-3 py-4">
        <form class="d-flex gap-2 flex-wrap" method="GET">
            <input name="q" value="<?php echo e($cari); ?>" class="form-control form-control-sm w-200px" placeholder="Cari nama, kode, lokasi">
            <select name="status" class="form-select form-select-sm w-150px" onchange="this.form.submit()">
                <option value="">Semua status</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = \App\Enums\StatusKegiatan::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($s->value); ?>" <?php if($status === $s->value): echo 'selected'; endif; ?>><?php echo e(ucfirst($s->value)); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select>
            <button class="btn btn-sm btn-light">Cari</button>
        </form>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\App\Support\Izin::kelolaKegiatan(auth()->user())): ?>
            <a href="<?php echo e(route('tim.proyek.create', $tim->slug)); ?>" class="btn btn-sm btn-primary">+ Proyek baru</a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="card-body pt-0 table-responsive">
        <table class="table table-row-dashed align-middle">
            <thead><tr class="text-muted fw-bold fs-7 text-uppercase"><th>Proyek</th><th>Lokasi</th><th>Status</th><th class="text-end">Pagu</th><th class="text-end">Total RAB</th></tr></thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $proyek; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr>
                        <td><a href="<?php echo e(route('tim.proyek.show', [$tim->slug, $k])); ?>" class="fw-bold text-gray-800 text-hover-primary"><?php echo e($k->nama); ?></a>
                            <div class="text-muted fs-8"><?php echo e($k->kode); ?></div></td>
                        <td><?php echo e($k->lokasi ?: '-'); ?></td>
                        <td><span class="badge badge-light-primary"><?php echo e(ucfirst($k->status->value ?? (string) $k->status)); ?></span></td>
                        <td class="text-end">Rp <?php echo e(number_format((int) $k->pagu, 0, ',', '.')); ?></td>
                        <td class="text-end fw-bold">Rp <?php echo e(number_format((int) $k->rab_total, 0, ',', '.')); ?></td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr><td colspan="5" class="text-center text-muted py-10">Belum ada proyek.</td></tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
        <?php echo e($proyek->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('tim.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/proyek/index.blade.php ENDPATH**/ ?>