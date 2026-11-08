<?php
    $grup = $rekap->groupBy('kategori');
    $angka = fn ($n) => rtrim(rtrim(number_format((float) $n, 3, ',', '.'), '0'), ',');
?>
<div class="card shadow-sm">
    <div class="card-header align-items-center"><h3 class="card-title fw-bold">Daftar kebutuhan material & upah</h3>
        <div class="text-muted fs-7">Realisasi dihitung dari belanja yang ditautkan ke material katalog.</div></div>
    <div class="card-body pt-0 table-responsive">
        <table class="table table-row-dashed fs-7">
            <thead><tr class="text-muted fs-8 text-uppercase"><th>Nama</th><th class="text-end">Kebutuhan</th><th>Satuan</th><th class="text-end">Biaya RAB</th><th class="text-end">Qty dibeli</th><th class="text-end">Biaya realisasi</th><th class="text-end">Sisa anggaran</th></tr></thead>
            <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $grup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kategori => $baris): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr class="bg-light"><td colspan="7" class="fw-bold text-uppercase fs-8 ps-3"><?php echo e(\App\Models\Material::KATEGORI[$kategori] ?? ucfirst((string) $kategori)); ?> — <?php echo e($rp($baris->sum('subtotal'))); ?></td></tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $baris; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr>
                        <td><?php echo e($m['nama']); ?></td>
                        <td class="text-end fw-bold"><?php echo e($angka($m['qty'])); ?></td>
                        <td><?php echo e($m['satuan']); ?></td>
                        <td class="text-end"><?php echo e($rp($m['subtotal'])); ?></td>
                        <td class="text-end"><?php echo e($angka($m['qty_realisasi'])); ?></td>
                        <td class="text-end"><?php echo e($rp($m['biaya_realisasi'])); ?></td>
                        <td class="text-end <?php echo e($m['selisih'] < 0 ? 'text-danger fw-bold' : ''); ?>"><?php echo e($rp($m['selisih'])); ?></td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr><td colspan="7" class="text-center text-muted py-8">Belum ada kebutuhan. Pasang analisa pada item RAB agar kebutuhan material terhitung otomatis.</td></tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rekap->isNotEmpty()): ?>
                <tfoot><tr class="fw-bolder"><td colspan="3">TOTAL</td><td class="text-end"><?php echo e($rp($rekap->sum('subtotal'))); ?></td><td></td><td class="text-end"><?php echo e($rp($rekap->sum('biaya_realisasi'))); ?></td><td class="text-end"><?php echo e($rp($rekap->sum('selisih'))); ?></td></tr></tfoot>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </table>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/proyek/tab_material.blade.php ENDPATH**/ ?>