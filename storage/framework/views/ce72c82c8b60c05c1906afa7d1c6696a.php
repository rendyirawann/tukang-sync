<?php $__env->startSection('title', 'Rekening Pembayaran'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('backend.saas._header', [
        'judul' => 'Rekening Pembayaran',
        'sub' => 'Rekening',
        'aksi' => '<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#rek-baru"><i class="ki-outline ki-plus fs-5"></i> Tambah rekening</button>',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="alert alert-light-info">Rekening aktif tampil di formulir pendaftaran dan halaman status pembayaran tim.</div>

    <div class="card card-flush">
        <div class="card-body">
            <table class="table align-middle table-row-dashed fs-6 gy-4">
                <thead><tr class="text-muted fw-bold fs-7 text-uppercase"><th>Bank</th><th>Nomor</th><th>Atas nama</th><th>Keterangan</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rekening; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr>
                            <td class="fw-bold"><?php echo e($r->bank); ?></td>
                            <td class="font-monospace"><?php echo e($r->nomor); ?></td>
                            <td><?php echo e($r->atas_nama); ?></td>
                            <td class="text-muted fs-7"><?php echo e($r->keterangan); ?></td>
                            <td><span class="badge badge-light-<?php echo e($r->aktif ? 'success' : 'secondary'); ?>"><?php echo e($r->aktif ? 'Aktif' : 'Nonaktif'); ?></span></td>
                            <td class="text-end text-nowrap">
                                <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#rek-<?php echo e($r->id); ?>">Ubah</button>
                                <form method="POST" action="<?php echo e(route('saas.rekening.destroy', $r)); ?>" class="d-inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-light-danger" onclick="return confirm('Hapus rekening ini?')">Hapus</button></form>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr><td colspan="6" class="text-center text-muted">Belum ada rekening.</td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $rekening->concat([null]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <div class="modal fade" id="<?php echo e($r ? 'rek-' . $r->id : 'rek-baru'); ?>" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="<?php echo e($r ? route('saas.rekening.update', $r) : route('saas.rekening.store')); ?>" class="modal-content">
                    <?php echo csrf_field(); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r): ?> <?php echo method_field('PUT'); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="modal-header"><h3 class="modal-title"><?php echo e($r ? 'Ubah rekening' : 'Rekening baru'); ?></h3><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label required">Bank</label><input name="bank" value="<?php echo e($r?->bank); ?>" class="form-control" placeholder="BCA / BRI / Mandiri / DANA" required /></div>
                        <div class="mb-3"><label class="form-label required">Nomor rekening</label><input name="nomor" value="<?php echo e($r?->nomor); ?>" class="form-control" required /></div>
                        <div class="mb-3"><label class="form-label required">Atas nama</label><input name="atas_nama" value="<?php echo e($r?->atas_nama); ?>" class="form-control" required /></div>
                        <div class="mb-3"><label class="form-label">Keterangan</label><input name="keterangan" value="<?php echo e($r?->keterangan); ?>" class="form-control" /></div>
                        <div class="mb-3"><label class="form-label">Urutan</label><input type="number" min="0" name="urutan" value="<?php echo e($r?->urutan ?? 0); ?>" class="form-control" /></div>
                        <label class="form-check form-switch"><input class="form-check-input" type="checkbox" name="aktif" value="1" <?php if($r?->aktif ?? true): echo 'checked'; endif; ?> /><span class="form-check-label">Aktif</span></label>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/backend/saas/rekening/index.blade.php ENDPATH**/ ?>