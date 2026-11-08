<?php $__env->startSection('title', 'Anggota Tim'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
    <div class="fs-5">Kuota akun: <span class="fw-bold"><?php echo e($anggota->count()); ?> dari <?php echo e($tim->max_users); ?> akun</span> (termasuk owner)</div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($boleh && $anggota->count() < $tim->max_users): ?>
        <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#fAnggota">+ Tambah anggota</button>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($boleh): ?>
<div class="collapse mb-5" id="fAnggota"><div class="card card-body shadow-sm">
    <?php echo $__env->make('tim.anggota._form', ['u' => null, 'aksi' => route('tim.anggota.store', $tim->slug)], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<div class="row g-5">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $anggota; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm h-100 <?php echo e($a->is_active ? '' : 'opacity-50'); ?>"><div class="card-body">
                <div class="d-flex gap-3 align-items-center mb-3">
                    <span class="symbol symbol-45px symbol-circle"><span class="symbol-label fw-bold text-white" style="background:var(--tp-primary)"><?php echo e($a->initials); ?></span></span>
                    <div class="min-w-0"><div class="fw-bold text-truncate"><?php echo e($a->name); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($a->id === auth()->id()): ?><span class="text-muted fs-8">(Anda)</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div>
                        <div class="text-muted fs-8 text-truncate"><?php echo e($a->email); ?></div></div>
                </div>
                <span class="badge <?php echo e($a->isPengelola() ? 'text-gold' : 'badge-light-primary'); ?>" <?php if($a->isPengelola()): ?> style="background:rgba(201,164,76,.15)" <?php endif; ?>><?php echo e($a->peran?->label()); ?></span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($a->is_active)): ?><span class="badge badge-light-danger">Nonaktif</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div class="text-muted fs-8 mt-2"><?php echo e($a->peran?->keterangan()); ?></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($boleh): ?>
                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-sm btn-light" data-bs-toggle="collapse" data-bs-target="#eA<?php echo e($loop->index); ?>">Ubah</button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($a->id !== auth()->id()): ?>
                            <form method="POST" action="<?php echo e(route('tim.anggota.destroy', [$tim->slug, $a])); ?>" data-confirm="Hapus akun <?php echo e($a->name); ?>?"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-light-danger">Hapus</button></form>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="collapse mt-4" id="eA<?php echo e($loop->index); ?>"><?php echo $__env->make('tim.anggota._form', ['u' => $a, 'aksi' => route('tim.anggota.update', [$tim->slug, $a])], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div></div>
        </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('tim.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/anggota/index.blade.php ENDPATH**/ ?>