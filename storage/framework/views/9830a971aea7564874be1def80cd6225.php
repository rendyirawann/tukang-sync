<?php $bolehLog = \App\Support\Izin::isiLogHarian($user); ?>
<div class="row g-5">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bolehLog): ?>
    <div class="col-xl-4">
        <div class="card shadow-sm"><div class="card-header"><h3 class="card-title fw-bold">Catat hari ini</h3></div>
            <div class="card-body">
                <form method="POST" action="<?php echo e(route('tim.proyek.log.store', [$tim->slug, $proyek])); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="row g-3">
                        <div class="col-6"><label class="form-label fs-7">Tanggal</label><input type="date" name="tanggal" value="<?php echo e(now()->toDateString()); ?>" class="form-control form-control-sm" required></div>
                        <div class="col-6"><label class="form-label fs-7">Cuaca</label><select name="cuaca" class="form-select form-select-sm"><option value="">-</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $cuaca; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($k); ?>"><?php echo e($v); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></div>
                        <div class="col-12"><label class="form-label fs-7">Jumlah pekerja</label><input type="number" min="0" name="jumlah_pekerja" class="form-control form-control-sm"></div>
                        <div class="col-12"><label class="form-label fs-7 required">Pekerjaan hari ini</label><textarea name="uraian" rows="3" class="form-control form-control-sm" required></textarea></div>
                        <div class="col-12"><label class="form-label fs-7">Kendala</label><textarea name="kendala" rows="2" class="form-control form-control-sm"></textarea></div>
                    </div>
                    <button class="btn btn-primary btn-sm mt-4 w-100">Simpan log</button>
                </form>
            </div></div>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div class="<?php echo e($bolehLog ? 'col-xl-8' : 'col-12'); ?>">
        <div class="card shadow-sm"><div class="card-body">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $log; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="border-bottom py-4">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <div class="fw-bold"><?php echo e($l->tanggal->translatedFormat('l, d M Y')); ?>

                            <span class="text-muted fw-normal fs-8">· <?php echo e(\App\Models\LogHarian::CUACA[$l->cuaca] ?? '-'); ?> · <?php echo e($l->jumlah_pekerja ?? '-'); ?> pekerja · <?php echo e($l->pembuat?->name); ?></span></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bolehLog && ($user->isPengelola() || $l->created_by === $user->id)): ?>
                            <form method="POST" action="<?php echo e(route('tim.proyek.log.destroy', [$tim->slug, $proyek, $l])); ?>" data-confirm="Hapus log ini?"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-light-danger py-1">Hapus</button></form>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="mt-2" style="white-space:pre-line"><?php echo e($l->uraian); ?></div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($l->kendala): ?><div class="mt-2 text-danger fs-7" style="white-space:pre-line">Kendala: <?php echo e($l->kendala); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <div class="text-muted text-center py-8">Belum ada log harian.</div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php echo e($log?->links()); ?>

        </div></div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/proyek/tab_log.blade.php ENDPATH**/ ?>