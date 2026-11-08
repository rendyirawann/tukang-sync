<?php $__env->startSection('title', 'Kontak WhatsApp'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('backend.saas._header', [
        'judul' => 'Kontak WhatsApp',
        'sub' => 'Kontak',
        'aksi' => '<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kontak-baru"><i class="ki-outline ki-plus fs-5"></i> Tambah kontak</button>',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="alert alert-light-info">Kontak <b>utama</b> menjadi tujuan tombol "Konfirmasi via WhatsApp" setelah pelanggan mendaftar.</div>

    <div class="card card-flush">
        <div class="card-body">
            <table class="table align-middle table-row-dashed fs-6 gy-4">
                <thead><tr class="text-muted fw-bold fs-7 text-uppercase"><th>Nama</th><th>No. WhatsApp</th><th>Keterangan</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $kontak; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr>
                            <td class="fw-bold"><?php echo e($k->nama); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($k->utama): ?> <span class="badge badge-light-primary">Utama</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></td>
                            <td><a href="<?php echo e($k->linkWa()); ?>" target="_blank" rel="noopener"><?php echo e($k->no_wa); ?></a></td>
                            <td class="text-muted fs-7"><?php echo e($k->keterangan); ?></td>
                            <td><span class="badge badge-light-<?php echo e($k->aktif ? 'success' : 'secondary'); ?>"><?php echo e($k->aktif ? 'Aktif' : 'Nonaktif'); ?></span></td>
                            <td class="text-end text-nowrap">
                                <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kontak-<?php echo e($k->id); ?>">Ubah</button>
                                <form method="POST" action="<?php echo e(route('saas.kontak.destroy', $k)); ?>" class="d-inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-light-danger" onclick="return confirm('Hapus kontak ini?')">Hapus</button></form>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr><td colspan="5" class="text-center text-muted">Belum ada kontak.</td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $kontak->concat([null]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <div class="modal fade" id="<?php echo e($k ? 'kontak-' . $k->id : 'kontak-baru'); ?>" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="<?php echo e($k ? route('saas.kontak.update', $k) : route('saas.kontak.store')); ?>" class="modal-content">
                    <?php echo csrf_field(); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($k): ?> <?php echo method_field('PUT'); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="modal-header"><h3 class="modal-title"><?php echo e($k ? 'Ubah kontak' : 'Kontak baru'); ?></h3><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label required">Nama</label><input name="nama" value="<?php echo e($k?->nama); ?>" class="form-control" required /></div>
                        <div class="mb-3"><label class="form-label required">No. WhatsApp</label><input name="no_wa" value="<?php echo e($k?->no_wa); ?>" class="form-control" placeholder="08xxxxxxxxxx" required /></div>
                        <div class="mb-3"><label class="form-label">Keterangan</label><input name="keterangan" value="<?php echo e($k?->keterangan); ?>" class="form-control" /></div>
                        <div class="mb-3"><label class="form-label">Urutan</label><input type="number" min="0" name="urutan" value="<?php echo e($k?->urutan ?? 0); ?>" class="form-control" /></div>
                        <label class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="aktif" value="1" <?php if($k?->aktif ?? true): echo 'checked'; endif; ?> /><span class="form-check-label">Aktif</span></label>
                        <label class="form-check form-switch"><input class="form-check-input" type="checkbox" name="utama" value="1" <?php if($k?->utama): echo 'checked'; endif; ?> /><span class="form-check-label">Kontak utama</span></label>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/backend/saas/kontak/index.blade.php ENDPATH**/ ?>