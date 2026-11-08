<?php $r = $ringkasan; $selisih = $r['pagu'] - $r['total_rab']; ?>
<div class="row g-5 mb-6">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
        ['Nilai kontrak / pagu', $rp($r['pagu']), null],
        ['Total RAB', $rp($r['total_rab']), $r['jumlah_item'].' item pekerjaan'],
        ['Selisih pagu − RAB', $rp($selisih), $selisih < 0 ? 'RAB melebihi pagu!' : 'margin'],
        ['Progres fisik', $r['progres'].'%', 'senilai '.$rp($r['nilai_progres'])],
        ['Realisasi belanja', $rp($r['realisasi_belanja']), $r['total_rab'] > 0 ? round($r['realisasi_belanja'] / $r['total_rab'] * 100, 1).'% dari RAB' : null],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$j, $v, $s]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <div class="col-6 col-xl">
            <div class="card shadow-sm h-100"><div class="card-body">
                <div class="text-muted fs-8"><?php echo e($j); ?></div>
                <div class="fs-3 fw-bolder <?php echo e($j === 'Selisih pagu − RAB' && $selisih < 0 ? 'text-danger' : ''); ?>" style="<?php echo e($j === 'Selisih pagu − RAB' && $selisih < 0 ? '' : 'color:var(--tp-primary)'); ?>"><?php echo e($v); ?></div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($s): ?><div class="text-muted fs-8"><?php echo e($s); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div></div>
        </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
</div>

<div class="row g-5">
    <div class="col-xl-6">
        <div class="card shadow-sm"><div class="card-header"><h3 class="card-title fw-bold">Per kategori pekerjaan</h3></div>
            <div class="card-body pt-0 table-responsive"><table class="table table-row-dashed">
                <thead><tr class="text-muted fs-8 text-uppercase"><th>Kategori</th><th class="text-end">Item</th><th class="text-end">Total</th><th class="text-end">%</th></tr></thead>
                <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $r['per_kategori']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr><td><?php echo e($k['label']); ?></td><td class="text-end"><?php echo e($k['jumlah_item']); ?></td><td class="text-end fw-bold"><?php echo e($rp($k['total'])); ?></td><td class="text-end"><?php echo e($k['persen']); ?>%</td></tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr><td colspan="4" class="text-muted text-center py-6">Belum ada item RAB.</td></tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table></div></div>
    </div>
    <div class="col-xl-6">
        <div class="card shadow-sm"><div class="card-header"><h3 class="card-title fw-bold">Per lokasi</h3></div>
            <div class="card-body pt-0 table-responsive"><table class="table table-row-dashed">
                <thead><tr class="text-muted fs-8 text-uppercase"><th>Lokasi</th><th class="text-end">Total</th><th style="width:30%">Progres</th></tr></thead>
                <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $r['per_lokasi']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr><td><?php echo e($l['lokasi']); ?></td><td class="text-end fw-bold"><?php echo e($rp($l['total'])); ?></td>
                        <td><div class="d-flex align-items-center gap-2"><div class="progress progress-thin flex-grow-1"><div class="progress-bar" style="width:<?php echo e($l['progres']); ?>%;background:var(--tp-gold)"></div></div><span class="fs-8"><?php echo e($l['progres']); ?>%</span></div></td></tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr><td colspan="3" class="text-muted text-center py-6">Belum ada item RAB.</td></tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table></div></div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/proyek/tab_ringkasan.blade.php ENDPATH**/ ?>