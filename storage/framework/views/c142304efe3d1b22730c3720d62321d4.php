<form method="POST" action="<?php echo e($aksi); ?>" class="row g-3 align-items-end">
    <?php echo csrf_field(); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($l): ?> <?php echo method_field('PUT'); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div class="col-md-4"><label class="form-label fs-7">Nama lantai</label><input name="nama" value="<?php echo e($l?->nama ?? 'Lantai '.$nomor); ?>" class="form-control form-control-sm" required maxlength="80"></div>
    <div class="col-md-2"><label class="form-label fs-7">Nomor</label><input type="number" name="nomor" value="<?php echo e($nomor); ?>" class="form-control form-control-sm" required></div>
    <div class="col-md-2"><label class="form-label fs-7">Luas m²</label><input type="number" step="0.01" min="0" name="luas" value="<?php echo e($l?->luas); ?>" class="form-control form-control-sm"></div>
    <div class="col-md-2"><label class="form-label fs-7">Tinggi m</label><input type="number" step="0.01" min="0" name="tinggi" value="<?php echo e($l?->tinggi); ?>" class="form-control form-control-sm"></div>
    <div class="col-md-2"><button class="btn btn-sm btn-primary w-100">Simpan</button></div>
</form>
<?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/proyek/_form_lantai.blade.php ENDPATH**/ ?>