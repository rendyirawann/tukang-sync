<?php $__env->startSection('title', $p->nama); ?>

<?php
    $rp = fn ($n) => 'Rp '.number_format((float) $n, 0, ',', '.');
    $angka = fn ($n) => rtrim(rtrim(number_format((float) $n, 4, ',', '.'), '0'), ',');
    $kelolaRab = \App\Support\Izin::kelolaRab($user);
?>

<?php $__env->startSection('content'); ?>
<div class="mb-4"><a href="<?php echo e(route('tim.proyek.show', [$tim->slug, $proyek, 'tab' => 'rab'])); ?>">← <?php echo e($proyek->nama); ?></a></div>
<div class="row g-5">
    <div class="col-xl-4">
        <div class="card shadow-sm mb-5"><div class="card-body p-8">
            <div class="text-muted fs-8"><?php echo e($p->lokasiLabel()); ?></div>
            <h3 class="fw-bolder"><?php echo e($p->nama); ?></h3>
            <div class="mb-4"><span class="badge badge-light-primary"><?php echo e($p->komponen?->label()); ?></span> <span class="badge badge-light"><?php echo e($p->kategori?->label()); ?></span></div>
            <table class="table table-sm mb-4">
                <tr><td class="text-muted">Jumlah</td><td class="text-end"><?php echo e($angka($p->jumlah)); ?></td></tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['panjang', 'lebar', 'tinggi']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->{$d}): ?><tr><td class="text-muted"><?php echo e(ucfirst($d)); ?></td><td class="text-end"><?php echo e($angka($p->{$d})); ?> m</td></tr><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr><td class="text-muted">Volume</td><td class="text-end fw-bold"><?php echo e($angka($p->volume)); ?> <?php echo e($p->satuanSimbol()); ?> <?php echo e($p->volume_manual ? '(manual)' : ''); ?></td></tr>
                <tr><td class="text-muted">Harga satuan</td><td class="text-end"><?php echo e($rp($p->harga_satuan)); ?> / <?php echo e($p->satuanSimbol()); ?></td></tr>
                <tr><td class="text-muted">Analisa</td><td class="text-end"><?php echo e($p->analisa?->nama ?? 'Borongan'); ?></td></tr>
            </table>
            <div class="fs-2 fw-bolder" style="color:var(--tp-primary)"><?php echo e($rp($p->total)); ?></div>
            <div class="text-muted fs-8 mb-4">Progres <?php echo e($p->progres); ?>% · senilai <?php echo e($rp($p->nilaiProgres())); ?></div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\App\Support\Izin::isiProgres($user)): ?>
                <form method="POST" action="<?php echo e(route('tim.proyek.pekerjaan.progres', [$tim->slug, $proyek, $p])); ?>" class="d-flex gap-2 mb-4">
                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                    <input type="range" min="0" max="100" step="5" name="progres" value="<?php echo e($p->progres); ?>" class="form-range" oninput="this.nextElementSibling.textContent=this.value+'%'"><span class="fw-bold w-50px"><?php echo e($p->progres); ?>%</span>
                    <button class="btn btn-sm btn-gold">Simpan</button>
                </form>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kelolaRab): ?>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="<?php echo e(route('tim.proyek.pekerjaan.edit', [$tim->slug, $proyek, $p])); ?>" class="btn btn-sm btn-primary">Ubah</a>
                    <form method="POST" action="<?php echo e(route('tim.proyek.pekerjaan.destroy', [$tim->slug, $proyek, $p])); ?>" data-confirm="Hapus item RAB ini?"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-light-danger">Hapus</button></form>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div></div>
    </div>
    <div class="col-xl-8">
        <div class="card shadow-sm"><div class="card-header align-items-center">
            <h3 class="card-title fw-bold">Kebutuhan material & upah</h3>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kelolaRab && $p->kebutuhan->isNotEmpty()): ?>
                <form method="POST" action="<?php echo e(route('tim.proyek.pekerjaan.segarkan', [$tim->slug, $proyek, $p])); ?>"><?php echo csrf_field(); ?><button class="btn btn-sm btn-light">Segarkan harga dari katalog</button></form>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="card-body pt-0 table-responsive">
            <table class="table table-row-dashed fs-7">
                <thead><tr class="text-muted fs-8 text-uppercase"><th>Nama</th><th class="text-end">Koef.</th><th class="text-end">Qty</th><th>Satuan</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th></tr></thead>
                <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $p->kebutuhan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr><td><?php echo e($k->nama); ?> <span class="badge badge-light fs-9"><?php echo e(\App\Models\Material::KATEGORI[$k->kategori] ?? $k->kategori); ?></span></td>
                        <td class="text-end text-muted"><?php echo e($k->koefisien !== null ? $angka($k->koefisien) : '-'); ?></td>
                        <td class="text-end fw-bold"><?php echo e($angka($k->qty)); ?></td><td><?php echo e($k->satuan); ?></td>
                        <td class="text-end"><?php echo e($rp($k->harga_satuan)); ?></td><td class="text-end fw-bold"><?php echo e($rp($k->subtotal)); ?></td></tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr><td colspan="6" class="text-center text-muted py-8">Pekerjaan borongan tanpa rincian. Pilih analisa di form ubah untuk menghitung kebutuhan otomatis.</td></tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->kebutuhan->isNotEmpty()): ?><tfoot><tr class="fw-bolder"><td colspan="5">TOTAL</td><td class="text-end"><?php echo e($rp($p->kebutuhan->sum('subtotal'))); ?></td></tr></tfoot><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </table>
        </div></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('tim.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/proyek/pekerjaan_show.blade.php ENDPATH**/ ?>