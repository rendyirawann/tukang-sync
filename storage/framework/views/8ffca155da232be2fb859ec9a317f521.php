<?php $__env->startSection('title', $proyek->exists ? 'Ubah Proyek' : 'Proyek Baru'); ?>

<?php $__env->startSection('content'); ?>
<div class="card shadow-sm" style="max-width:900px">
    <div class="card-body p-8">
        <form method="POST" action="<?php echo e($proyek->exists ? route('tim.proyek.update', [$tim->slug, $proyek]) : route('tim.proyek.store', $tim->slug)); ?>">
            <?php echo csrf_field(); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($proyek->exists): ?> <?php echo method_field('PUT'); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="row g-5">
                <div class="col-md-3"><label class="form-label">Kode</label><input name="kode" value="<?php echo e(old('kode', $proyek->kode)); ?>" class="form-control" maxlength="40" placeholder="PRJ-001"></div>
                <div class="col-md-9"><label class="form-label required">Nama proyek</label><input name="nama" value="<?php echo e(old('nama', $proyek->nama)); ?>" class="form-control" required maxlength="150"></div>
                <div class="col-md-6"><label class="form-label">Lokasi</label><input name="lokasi" value="<?php echo e(old('lokasi', $proyek->lokasi)); ?>" class="form-control" maxlength="150"></div>
                <div class="col-md-6"><label class="form-label">Sumber dana / klien</label><input name="sumber_dana" value="<?php echo e(old('sumber_dana', $proyek->sumber_dana)); ?>" class="form-control" maxlength="100"></div>
                <div class="col-md-4"><label class="form-label">Tanggal mulai</label><input type="date" name="tanggal_mulai" value="<?php echo e(old('tanggal_mulai', $proyek->tanggal_mulai?->format('Y-m-d'))); ?>" class="form-control"></div>
                <div class="col-md-4"><label class="form-label">Tanggal selesai</label><input type="date" name="tanggal_selesai" value="<?php echo e(old('tanggal_selesai', $proyek->tanggal_selesai?->format('Y-m-d'))); ?>" class="form-control"></div>
                <div class="col-md-4"><label class="form-label required">Status</label>
                    <select name="status" class="form-select">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = \App\Enums\StatusKegiatan::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($s->value); ?>" <?php if(old('status', $proyek->status?->value ?? $proyek->status) === $s->value): echo 'selected'; endif; ?>><?php echo e(ucfirst($s->value)); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select></div>
                <div class="col-md-6"><label class="form-label">Nilai kontrak / pagu (Rp)</label><input type="number" min="0" step="1" name="pagu" value="<?php echo e(old('pagu', (int) $proyek->pagu)); ?>" class="form-control"></div>
                <div class="col-12"><label class="form-label">Keterangan</label><textarea name="keterangan" rows="3" class="form-control"><?php echo e(old('keterangan', $proyek->keterangan)); ?></textarea></div>
            </div>
            <div class="d-flex gap-2 mt-8">
                <button class="btn btn-primary">Simpan</button>
                <a href="<?php echo e($proyek->exists ? route('tim.proyek.show', [$tim->slug, $proyek]) : route('tim.proyek.index', $tim->slug)); ?>" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('tim.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/proyek/form.blade.php ENDPATH**/ ?>