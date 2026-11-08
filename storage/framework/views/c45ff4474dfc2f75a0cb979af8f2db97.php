<?php $__env->startSection('title', 'Paket Langganan'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('backend.saas._header', [
        'judul' => 'Paket Langganan',
        'sub' => 'Paket',
        'aksi' => '<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#paket-baru"><i class="ki-outline ki-plus fs-5"></i> Tambah paket</button>',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="card card-flush">
        <div class="card-body">
            <table class="table align-middle table-row-dashed fs-6 gy-4">
                <thead><tr class="text-muted fw-bold fs-7 text-uppercase"><th>Paket</th><th>Harga</th><th>Periode</th><th>Maks akun</th><th>Tim</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr>
                            <td><div class="fw-bold"><?php echo e($plan->nama); ?></div><div class="text-muted fs-7"><?php echo e($plan->kode); ?> · <?php echo e($plan->deskripsi); ?></div></td>
                            <td class="fw-bold"><?php echo e($plan->hargaLabel()); ?></td>
                            <td><?php echo e($plan->periodeLabel()); ?></td>
                            <td><?php echo e($plan->max_users); ?></td>
                            <td><?php echo e($plan->teams_count); ?></td>
                            <td><span class="badge badge-light-<?php echo e($plan->aktif ? 'success' : 'secondary'); ?>"><?php echo e($plan->aktif ? 'Aktif' : 'Nonaktif'); ?></span></td>
                            <td class="text-end text-nowrap">
                                <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#paket-<?php echo e($plan->id); ?>">Ubah</button>
                                <form method="POST" action="<?php echo e(route('saas.paket.destroy', $plan)); ?>" class="d-inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-light-danger" onclick="return confirm('Hapus paket ini?')">Hapus</button></form>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr><td colspan="7" class="text-center text-muted">Belum ada paket.</td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $plans->concat([null]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <div class="modal fade" id="<?php echo e($plan ? 'paket-' . $plan->id : 'paket-baru'); ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form method="POST" action="<?php echo e($plan ? route('saas.paket.update', $plan) : route('saas.paket.store')); ?>" class="modal-content">
                    <?php echo csrf_field(); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan): ?> <?php echo method_field('PUT'); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="modal-header"><h3 class="modal-title"><?php echo e($plan ? 'Ubah paket' : 'Paket baru'); ?></h3><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body"><?php echo $__env->make('backend.saas.paket._form', ['plan' => $plan], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
                    <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/backend/saas/paket/index.blade.php ENDPATH**/ ?>