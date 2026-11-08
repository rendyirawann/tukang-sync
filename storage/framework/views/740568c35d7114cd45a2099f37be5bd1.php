<?php $__env->startSection('title', $analisa->exists ? 'Ubah Analisa' : 'Analisa Baru'); ?>

<?php
    $items = old('items', $analisa->exists ? $analisa->items->map(fn ($i) => ['material_id' => $i->material_id, 'koefisien' => $i->koefisien])->all() : [['material_id' => '', 'koefisien' => '']]);
?>

<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e($analisa->exists ? route('tim.analisa.update', [$tim->slug, $analisa]) : route('tim.analisa.store', $tim->slug)); ?>">
    <?php echo csrf_field(); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($analisa->exists): ?> <?php echo method_field('PUT'); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div class="card shadow-sm mb-5"><div class="card-body p-8"><div class="row g-4">
        <div class="col-md-2"><label class="form-label">Kode</label><input name="kode" value="<?php echo e(old('kode', $analisa->kode)); ?>" class="form-control" maxlength="30"></div>
        <div class="col-md-6"><label class="form-label required">Nama pekerjaan</label><input name="nama" value="<?php echo e(old('nama', $analisa->nama)); ?>" class="form-control" required maxlength="200"></div>
        <div class="col-md-2"><label class="form-label required">Kategori</label><select name="kategori" class="form-select"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $kategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($k); ?>" <?php if(old('kategori', $analisa->kategori?->value) === $k): echo 'selected'; endif; ?>><?php echo e($v); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></div>
        <div class="col-md-2"><label class="form-label required">Per satuan</label><select name="satuan" class="form-select"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $satuan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($k); ?>" <?php if(old('satuan', $analisa->satuan) === $k): echo 'selected'; endif; ?>><?php echo e($v); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></select></div>
        <div class="col-12"><label class="form-label">Keterangan</label><input name="keterangan" value="<?php echo e(old('keterangan', $analisa->keterangan)); ?>" class="form-control" maxlength="255"></div>
    </div></div></div>

    <div class="card shadow-sm"><div class="card-header align-items-center"><h3 class="card-title fw-bold">Kebutuhan per 1 satuan</h3>
        <div class="fw-bolder fs-4" style="color:var(--tp-primary)">Rp <span id="totalAnalisa">0</span></div></div>
        <div class="card-body pt-0">
            <table class="table fs-7" id="tabelItem">
                <thead><tr class="text-muted fs-8 text-uppercase"><th>Material / upah</th><th style="width:150px">Koefisien</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th><th></th></tr></thead>
                <tbody></tbody>
            </table>
            <button type="button" class="btn btn-sm btn-light" id="tambahItem">+ Baris</button>
        </div>
    </div>
    <div class="mt-5 d-flex gap-2"><button class="btn btn-primary">Simpan analisa</button><a href="<?php echo e(route('tim.analisa.index', $tim->slug)); ?>" class="btn btn-light">Batal</a></div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    var material = <?php echo json_encode($material, 15, 512) ?>, awal = <?php echo json_encode(array_values($items), 15, 512) ?>, tbody = document.querySelector('#tabelItem tbody'), n = 0;
    var rp = function (x) { return Math.round(x).toLocaleString('id-ID'); };
    function baris(it) {
        var i = n++, tr = document.createElement('tr');
        var opsi = '<option value="">— pilih —</option>' + material.map(function (m) {
            return '<option value="' + m.id + '" data-harga="' + m.harga + '" ' + (String(m.id) === String(it.material_id) ? 'selected' : '') + '>' + m.nama + ' (' + m.satuan + ')</option>';
        }).join('');
        tr.innerHTML = '<td><select name="items[' + i + '][material_id]" class="form-select form-select-sm" required>' + opsi + '</select></td>' +
            '<td><input type="number" step="0.0001" min="0" name="items[' + i + '][koefisien]" value="' + (it.koefisien || '') + '" class="form-control form-control-sm" required></td>' +
            '<td class="text-end h"></td><td class="text-end fw-bold s"></td><td><button type="button" class="btn btn-sm btn-icon btn-light-danger">×</button></td>';
        tr.querySelector('button').onclick = function () { tr.remove(); hitung(); };
        tr.querySelectorAll('select,input').forEach(function (e) { e.addEventListener('input', hitung); e.addEventListener('change', hitung); });
        tbody.appendChild(tr);
    }
    function hitung() {
        var tot = 0;
        tbody.querySelectorAll('tr').forEach(function (tr) {
            var o = tr.querySelector('select').selectedOptions[0], h = o && o.dataset.harga ? parseFloat(o.dataset.harga) : 0;
            var k = parseFloat(tr.querySelector('input').value) || 0;
            tr.querySelector('.h').textContent = rp(h); tr.querySelector('.s').textContent = rp(h * k); tot += h * k;
        });
        document.getElementById('totalAnalisa').textContent = rp(tot);
    }
    awal.forEach(baris); hitung();
    document.getElementById('tambahItem').onclick = function () { baris({}); };
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('tim.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/katalog/analisa_form.blade.php ENDPATH**/ ?>