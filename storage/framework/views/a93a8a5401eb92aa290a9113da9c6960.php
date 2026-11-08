
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($daftar->isNotEmpty()): ?>
<div class="table-responsive">
<table class="table table-sm table-row-dashed mb-2 fs-7">
    <tbody>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $daftar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <tr>
            <td><a href="<?php echo e(route('tim.proyek.pekerjaan.show', [$tim->slug, $proyek, $p])); ?>" class="fw-semibold text-gray-800 text-hover-primary"><?php echo e($p->nama); ?></a>
                <div class="text-muted fs-8"><?php echo e($p->komponen?->label()); ?> · <?php echo e(rtrim(rtrim(number_format($p->volume, 3, ',', '.'), '0'), ',')); ?> <?php echo e($p->satuanSimbol()); ?></div></td>
            <td class="text-end fw-bold text-nowrap"><?php echo e($rp($p->total)); ?></td>
            <td style="width:170px">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isiProgres): ?>
                    <form method="POST" action="<?php echo e(route('tim.proyek.pekerjaan.progres', [$tim->slug, $proyek, $p])); ?>" class="d-flex gap-1 align-items-center">
                        <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                        <input type="number" name="progres" min="0" max="100" value="<?php echo e($p->progres); ?>" class="form-control form-control-sm w-70px">
                        <span>%</span><button class="btn btn-sm btn-icon btn-light" title="Simpan progres">✓</button>
                    </form>
                <?php else: ?>
                    <div class="progress progress-thin"><div class="progress-bar" style="width:<?php echo e($p->progres); ?>%;background:var(--tp-gold)"></div></div>
                    <span class="fs-8"><?php echo e($p->progres); ?>%</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </td>
        </tr>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </tbody>
</table>
</div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/proyek/_pekerjaan_rows.blade.php ENDPATH**/ ?>