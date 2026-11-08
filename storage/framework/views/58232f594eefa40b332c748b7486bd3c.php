<?php $__env->startSection('title', 'Ubah Tim ' . $team->slug); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('backend.saas._header', ['judul' => 'Ubah tim ' . $team->nama, 'sub' => $team->slug], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <form method="POST" action="<?php echo e(route('saas.tim.update', $team->slug)); ?>" class="card card-flush">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label required">Nama tim</label>
                    <input name="nama" value="<?php echo e(old('nama', $team->nama)); ?>" class="form-control" required />
                </div>
                <div class="col-md-3 mb-4">
                    <label class="form-label">Kota</label>
                    <input name="kota" value="<?php echo e(old('kota', $team->kota)); ?>" class="form-control" />
                </div>
                <div class="col-md-3 mb-4">
                    <label class="form-label">No. WhatsApp</label>
                    <input name="no_wa" value="<?php echo e(old('no_wa', $team->no_wa)); ?>" class="form-control" />
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">Alamat</label>
                    <input name="alamat" value="<?php echo e(old('alamat', $team->alamat)); ?>" class="form-control" />
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="<?php echo e(old('email', $team->email)); ?>" class="form-control" />
                </div>
                <div class="col-md-4 mb-4">
                    <label class="form-label">Paket</label>
                    <select name="plan_id" class="form-select">
                        <option value="">-</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($p->id); ?>" <?php if(old('plan_id', $team->plan_id) == $p->id): echo 'selected'; endif; ?>><?php echo e($p->nama); ?> (<?php echo e($p->hargaLabel()); ?>)</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>
                <div class="col-md-2 mb-4">
                    <label class="form-label required">Maks akun</label>
                    <input type="number" min="1" max="50" name="max_users" value="<?php echo e(old('max_users', $team->max_users)); ?>" class="form-control" />
                </div>
                <div class="col-md-3 mb-4">
                    <label class="form-label">Aktif sampai</label>
                    <input type="date" name="aktif_sampai" value="<?php echo e(old('aktif_sampai', $team->aktif_sampai?->format('Y-m-d'))); ?>" class="form-control" />
                </div>
                <div class="col-md-3 mb-4 d-flex align-items-end">
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="lifetime" value="1" <?php if(old('lifetime', $team->lifetime)): echo 'checked'; endif; ?> />
                        <span class="form-check-label">Lifetime</span>
                    </label>
                </div>
                <div class="col-12 mb-4">
                    <label class="form-label">Catatan admin (internal)</label>
                    <textarea name="catatan_admin" rows="3" class="form-control"><?php echo e(old('catatan_admin', $team->catatan_admin)); ?></textarea>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="<?php echo e(route('saas.tim.show', $team->slug)); ?>" class="btn btn-light">Batal</a>
            <button class="btn btn-primary">Simpan</button>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/backend/saas/tim/edit.blade.php ENDPATH**/ ?>