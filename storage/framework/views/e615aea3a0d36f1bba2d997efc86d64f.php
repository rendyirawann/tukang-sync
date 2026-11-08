<?php $__env->startSection('title', 'Katalog Material'); ?>

<?php $__env->startSection('content'); ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($boleh): ?>
<div class="card shadow-sm mb-5"><div class="card-body">
    <div class="fw-bold mb-3">Tambah material / upah / alat</div>
    <?php echo $__env->make('tim.katalog._form_material', ['m' => null, 'aksi' => route('tim.material.store', $tim->slug)], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<div class="card shadow-sm">
    <div class="card-header align-items-center py-4">
        <form class="d-flex gap-2 flex-wrap" method="GET">
            <input name="q" value="<?php echo e($cari); ?>" class="form-control form-control-sm w-200px" placeholder="Cari nama">
            <select name="kategori" class="form-select form-select-sm w-150px" onchange="this.form.submit()"><option value="">Semua</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = \App\Models\Material::KATEGORI; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($k); ?>" <?php if($kategori === $k): echo 'selected'; endif; ?>><?php echo e($v); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select>
            <button class="btn btn-sm btn-light">Cari</button>
        </form>
        <div class="text-muted fs-7">Harga per daerah berbeda — sesuaikan dengan harga setempat.</div>
    </div>
    <div class="card-body pt-0 table-responsive">
        <table class="table table-row-dashed fs-7">
            <thead><tr class="text-muted fs-8 text-uppercase"><th>Kode</th><th>Nama</th><th>Kategori</th><th>Satuan</th><th class="text-end">Harga</th><th>Toko/merek</th><th></th></tr></thead>
            <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $material; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr class="<?php echo e($m->aktif ? '' : 'opacity-50'); ?>">
                    <td class="text-muted"><?php echo e($m->kode); ?></td><td class="fw-semibold"><?php echo e($m->nama); ?></td>
                    <td><span class="badge badge-light"><?php echo e($m->kategoriLabel()); ?></span></td><td><?php echo e($m->satuan); ?></td>
                    <td class="text-end fw-bold">Rp <?php echo e(number_format($m->harga, 0, ',', '.')); ?></td>
                    <td class="text-muted"><?php echo e(collect([$m->merek, $m->toko])->filter()->implode(' · ')); ?></td>
                    <td class="text-end text-nowrap">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($boleh): ?>
                            <button class="btn btn-sm btn-light py-1" data-bs-toggle="collapse" data-bs-target="#eM<?php echo e($m->id); ?>">Ubah</button>
                            <form method="POST" action="<?php echo e(route('tim.material.destroy', [$tim->slug, $m])); ?>" class="d-inline" data-confirm="Hapus material ini dari katalog?"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-light-danger py-1">Hapus</button></form>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </td>
                </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($boleh): ?>
                    <tr class="collapse" id="eM<?php echo e($m->id); ?>"><td colspan="7"><?php echo $__env->make('tim.katalog._form_material', ['m' => $m, 'aksi' => route('tim.material.update', [$tim->slug, $m])], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td></tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr><td colspan="7" class="text-center text-muted py-8">Katalog kosong.</td></tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
        <?php echo e($material->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('tim.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/katalog/material.blade.php ENDPATH**/ ?>