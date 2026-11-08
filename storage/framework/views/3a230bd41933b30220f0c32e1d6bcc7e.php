<?php $bolehBelanja = \App\Support\Izin::kelolaBahanBaku($user); ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bolehBelanja): ?>
<div class="card shadow-sm mb-5"><div class="card-body">
    <form method="POST" action="<?php echo e(route('tim.proyek.belanja.store', [$tim->slug, $proyek])); ?>" class="row g-3 align-items-end">
        <?php echo csrf_field(); ?>
        <div class="col-md-3"><label class="form-label fs-7">Material katalog (opsional)</label>
            <select name="material_id" id="pilihMaterial" class="form-select form-select-sm"><option value="">— ketik manual —</option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $materials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($m->id); ?>" data-satuan="<?php echo e($m->satuan); ?>" data-harga="<?php echo e((int) $m->harga); ?>"><?php echo e($m->nama); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </select></div>
        <div class="col-md-3"><label class="form-label fs-7">Nama barang</label><input name="nama" id="belanjaNama" class="form-control form-control-sm" maxlength="150"></div>
        <div class="col-md-1"><label class="form-label fs-7">Qty</label><input type="number" step="0.001" min="0" name="qty" class="form-control form-control-sm" required></div>
        <div class="col-md-1"><label class="form-label fs-7">Satuan</label><input name="satuan" id="belanjaSatuan" class="form-control form-control-sm" maxlength="30"></div>
        <div class="col-md-2"><label class="form-label fs-7">Harga satuan</label><input type="number" min="0" name="harga_satuan" id="belanjaHarga" class="form-control form-control-sm" required></div>
        <div class="col-md-2"><label class="form-label fs-7">Tanggal</label><input type="date" name="tanggal_beli" value="<?php echo e(now()->toDateString()); ?>" class="form-control form-control-sm"></div>
        <div class="col-md-3"><label class="form-label fs-7">Untuk item RAB (opsional)</label>
            <select name="pekerjaan_id" class="form-select form-select-sm"><option value="">—</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pekerjaan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($p->id); ?>"><?php echo e($p->nama); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></div>
        <div class="col-md-3"><label class="form-label fs-7">Toko</label><input name="toko" class="form-control form-control-sm" maxlength="120"></div>
        <div class="col-md-4"><label class="form-label fs-7">Keterangan</label><input name="keterangan" class="form-control form-control-sm"></div>
        <div class="col-md-2"><button class="btn btn-sm btn-primary w-100">Catat belanja</button></div>
    </form>
</div></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<div class="card shadow-sm"><div class="card-body table-responsive">
    <table class="table table-row-dashed fs-7">
        <thead><tr class="text-muted fs-8 text-uppercase"><th>Tanggal</th><th>Barang</th><th class="text-end">Qty</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th><th>Toko</th><th></th></tr></thead>
        <tbody>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $belanja; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <tr>
                <td><?php echo e($b->tanggal_beli?->format('d/m/Y')); ?></td>
                <td class="fw-semibold"><?php echo e($b->nama); ?></td>
                <td class="text-end"><?php echo e(rtrim(rtrim(number_format((float) $b->qty, 3, ',', '.'), '0'), ',')); ?> <?php echo e($b->satuan); ?></td>
                <td class="text-end"><?php echo e($rp($b->harga_satuan)); ?></td>
                <td class="text-end fw-bold"><?php echo e($rp($b->subtotal)); ?></td>
                <td><?php echo e($b->toko); ?></td>
                <td class="text-end"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bolehBelanja): ?><form method="POST" action="<?php echo e(route('tim.proyek.belanja.destroy', [$tim->slug, $proyek, $b])); ?>" data-confirm="Hapus belanja ini?"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-light-danger py-1">Hapus</button></form><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></td>
            </tr>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <tr><td colspan="7" class="text-center text-muted py-8">Belum ada belanja tercatat.</td></tr>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>
    <?php echo e($belanja?->links()); ?>

</div></div>

<?php $__env->startPush('scripts'); ?>
<script>
    (function () {
        var s = document.getElementById('pilihMaterial'); if (!s) return;
        s.addEventListener('change', function () {
            var o = s.selectedOptions[0]; if (!o || !o.value) return;
            document.getElementById('belanjaNama').value = o.text;
            document.getElementById('belanjaSatuan').value = o.dataset.satuan;
            document.getElementById('belanjaHarga').value = o.dataset.harga;
        });
    })();
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/proyek/tab_belanja.blade.php ENDPATH**/ ?>