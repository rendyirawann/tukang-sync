<?php $__env->startSection('title', $p->exists ? 'Ubah Item RAB' : 'Item RAB Baru'); ?>

<?php
    $v = fn ($f) => old($f, $p->{$f} instanceof \BackedEnum ? $p->{$f}->value : $p->{$f});
    $struktur = $proyek->bangunan->map(fn ($b) => [
        'id' => $b->id, 'nama' => $b->nama,
        'lantai' => $b->lantai->map(fn ($l) => ['id' => $l->id, 'nama' => $l->nama,
            'ruangan' => $l->ruangan->map(fn ($r) => ['id' => $r->id, 'nama' => $r->nama])->values()])->values(),
    ])->values();
?>

<?php $__env->startSection('content'); ?>
<div class="mb-4"><a href="<?php echo e(route('tim.proyek.show', [$tim->slug, $proyek, 'tab' => 'rab'])); ?>">← <?php echo e($proyek->nama); ?></a></div>
<form method="POST" action="<?php echo e($p->exists ? route('tim.proyek.pekerjaan.update', [$tim->slug, $proyek, $p]) : route('tim.proyek.pekerjaan.store', [$tim->slug, $proyek])); ?>">
    <?php echo csrf_field(); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->exists): ?> <?php echo method_field('PUT'); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div class="row g-5">
        <div class="col-xl-8">
            <div class="card shadow-sm mb-5"><div class="card-body p-8">
                <h4 class="fw-bold mb-5">Pekerjaan</h4>
                <div class="row g-4">
                    <div class="col-md-4"><label class="form-label">Bangunan</label><select name="bangunan_id" id="fB" class="form-select"></select></div>
                    <div class="col-md-4"><label class="form-label">Lantai</label><select name="lantai_id" id="fL" class="form-select"></select></div>
                    <div class="col-md-4"><label class="form-label">Ruangan</label><select name="ruangan_id" id="fR" class="form-select"></select></div>

                    <div class="col-md-6"><label class="form-label required">Komponen</label>
                        <select name="komponen" id="fKomponen" class="form-select">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $komponen; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($k); ?>" <?php if($v('komponen') === $k): echo 'selected'; endif; ?>><?php echo e($o['label']); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select></div>
                    <div class="col-md-6"><label class="form-label required">Kategori</label>
                        <select name="kategori" id="fKategori" class="form-select">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $kategori; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($k); ?>" <?php if($v('kategori') === $k): echo 'selected'; endif; ?>><?php echo e($l); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select></div>
                    <div class="col-12"><label class="form-label required">Nama pekerjaan</label><input name="nama" value="<?php echo e($v('nama')); ?>" class="form-control" required maxlength="200" placeholder="Mis. Pintu kamar panel kayu 0,9 × 2,1 m"></div>
                </div>
            </div></div>

            <div class="card shadow-sm"><div class="card-body p-8">
                <h4 class="fw-bold mb-5">Volume</h4>
                <div class="row g-4">
                    <div class="col-md-3"><label class="form-label required">Satuan</label>
                        <select name="satuan" id="fSatuan" class="form-select">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $satuan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k => $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($k); ?>" <?php if($v('satuan') === $k): echo 'selected'; endif; ?>><?php echo e($o['label']); ?></option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select></div>
                    <div class="col-md-3"><label class="form-label required">Jumlah (buah)</label><input type="number" step="0.01" min="0" name="jumlah" value="<?php echo e($v('jumlah') ?? 1); ?>" class="form-control" required></div>
                    <div class="col-md-2 dim" data-dim="panjang"><label class="form-label">Panjang (m)</label><input type="number" step="0.001" min="0" name="panjang" value="<?php echo e($v('panjang')); ?>" class="form-control"></div>
                    <div class="col-md-2 dim" data-dim="lebar"><label class="form-label" id="labelLebar">Lebar (m)</label><input type="number" step="0.001" min="0" name="lebar" value="<?php echo e($v('lebar')); ?>" class="form-control"></div>
                    <div class="col-md-2 dim" data-dim="tinggi"><label class="form-label">Tinggi (m)</label><input type="number" step="0.001" min="0" name="tinggi" value="<?php echo e($v('tinggi')); ?>" class="form-control"></div>
                    <div class="col-12 text-muted fs-7" id="rumus"></div>
                    <div class="col-md-4">
                        <label class="form-check form-switch mt-2"><input type="hidden" name="volume_manual" value="0">
                            <input class="form-check-input" type="checkbox" name="volume_manual" value="1" id="fManual" <?php if(old('volume_manual', $p->volume_manual)): echo 'checked'; endif; ?>>
                            <span class="form-check-label">Isi volume manual</span></label></div>
                    <div class="col-md-4" id="boxVolume"><label class="form-label">Volume</label><input type="number" step="0.0001" min="0" name="volume" value="<?php echo e($v('volume')); ?>" class="form-control"></div>
                </div>
            </div></div>
        </div>

        <div class="col-xl-4">
            <div class="card shadow-sm mb-5"><div class="card-body p-8">
                <h4 class="fw-bold mb-2">Harga</h4>
                <p class="text-muted fs-7">Pilih analisa agar kebutuhan material & upah dihitung otomatis. Tanpa analisa, isi harga satuan borongan.</p>
                <label class="form-label">Analisa harga satuan</label>
                <select name="analisa_id" id="fAnalisa" class="form-select mb-4">
                    <option value="">— Tanpa analisa (borongan) —</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $analisa; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><option value="<?php echo e($a->id); ?>" data-satuan="<?php echo e($a->satuan); ?>" <?php if((int) old('analisa_id', $p->analisa_id) === $a->id): echo 'selected'; endif; ?>><?php echo e($a->nama); ?> (/<?php echo e($a->satuan); ?>)</option><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
                <div id="boxHarga"><label class="form-label">Harga satuan borongan (Rp)</label><input type="number" step="1" min="0" name="harga_satuan" value="<?php echo e((int) $v('harga_satuan')); ?>" class="form-control"></div>
            </div></div>
            <div class="card shadow-sm mb-5"><div class="card-body p-8">
                <div class="row g-4">
                    <div class="col-12"><label class="form-label">Progres (%)</label><input type="number" min="0" max="100" name="progres" value="<?php echo e($v('progres') ?? 0); ?>" class="form-control"></div>
                    <div class="col-6"><label class="form-label">Rencana mulai</label><input type="date" name="rencana_mulai" value="<?php echo e(old('rencana_mulai', $p->rencana_mulai?->format('Y-m-d'))); ?>" class="form-control"></div>
                    <div class="col-6"><label class="form-label">Rencana selesai</label><input type="date" name="rencana_selesai" value="<?php echo e(old('rencana_selesai', $p->rencana_selesai?->format('Y-m-d'))); ?>" class="form-control"></div>
                    <div class="col-12"><label class="form-label">Keterangan</label><input name="keterangan" value="<?php echo e($v('keterangan')); ?>" class="form-control" maxlength="255"></div>
                </div>
            </div></div>
            <button class="btn btn-primary w-100">Simpan item RAB</button>
        </div>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    var struktur = <?php echo json_encode($struktur, 15, 512) ?>, komponen = <?php echo json_encode($komponen, 15, 512) ?>, satuan = <?php echo json_encode($satuan, 15, 512) ?>;
    var awal = { b: <?php echo json_encode((string) old('bangunan_id', $p->bangunan_id), 512) ?>, l: <?php echo json_encode((string) old('lantai_id', $p->lantai_id), 512) ?>, r: <?php echo json_encode((string) old('ruangan_id', $p->ruangan_id), 512) ?> };
    var fB = document.getElementById('fB'), fL = document.getElementById('fL'), fR = document.getElementById('fR');
    function isi(sel, daftar, kosong, pilih) {
        sel.innerHTML = '<option value="">' + kosong + '</option>';
        (daftar || []).forEach(function (x) { var o = new Option(x.nama, x.id); if (String(x.id) === String(pilih)) o.selected = true; sel.add(o); });
    }
    function cari(d, id) { return (d || []).find(function (x) { return String(x.id) === String(id); }); }
    function ubahB() { var b = cari(struktur, fB.value); isi(fL, b && b.lantai, '— Seluruh bangunan —', awal.l); awal.l = ''; ubahL(); }
    function ubahL() { var b = cari(struktur, fB.value), l = b && cari(b.lantai, fL.value); isi(fR, l && l.ruangan, '— Seluruh lantai —', awal.r); awal.r = ''; }
    isi(fB, struktur, '— Umum (tanpa lokasi) —', awal.b); ubahB();
    fB.addEventListener('change', ubahB); fL.addEventListener('change', ubahL);

    var fS = document.getElementById('fSatuan'), fK = document.getElementById('fKomponen'), fKat = document.getElementById('fKategori');
    var fM = document.getElementById('fManual'), fA = document.getElementById('fAnalisa');
    function ubahSatuan() {
        var s = satuan[fS.value] || { dimensi: [] };
        document.querySelectorAll('.dim').forEach(function (el) { el.style.display = s.dimensi.indexOf(el.dataset.dim) >= 0 ? '' : 'none'; });
        if (fS.value === 'm2') { document.querySelector('[data-dim="tinggi"]').style.display = ''; document.getElementById('labelLebar').textContent = 'Lebar (m)'; }
        var r = { unit: 'Volume = jumlah', titik: 'Volume = jumlah', ls: 'Volume = jumlah', m1: "Volume (m') = jumlah × panjang", m2: 'Volume (m²) = jumlah × panjang × lebar — untuk dinding kosongkan lebar dan isi tinggi', m3: 'Volume (m³) = jumlah × panjang × lebar × tinggi', kg: 'Isi volume manual (kg)' };
        document.getElementById('rumus').textContent = r[fS.value] || '';
        if (s.otomatis === false) { fM.checked = true; }
        ubahManual(); saringAnalisa();
    }
    function ubahManual() { document.getElementById('boxVolume').style.display = fM.checked ? '' : 'none'; }
    function saringAnalisa() {
        Array.from(fA.options).forEach(function (o) { if (o.value) o.hidden = o.dataset.satuan !== fS.value; });
        if (fA.selectedOptions[0] && fA.selectedOptions[0].hidden) fA.value = '';
        document.getElementById('boxHarga').style.display = fA.value ? 'none' : '';
    }
    fK.addEventListener('change', function () { var k = komponen[fK.value]; if (k) { fS.value = k.satuan; fKat.value = k.kategori; ubahSatuan(); } });
    fS.addEventListener('change', ubahSatuan); fM.addEventListener('change', ubahManual); fA.addEventListener('change', saringAnalisa);
    ubahSatuan();
})();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('tim.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/proyek/pekerjaan_form.blade.php ENDPATH**/ ?>