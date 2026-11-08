<form method="POST" action="<?php echo e($aksi); ?>" class="row g-3 align-items-end">
    <?php echo csrf_field(); ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r): ?> <?php echo method_field('PUT'); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <div class="col-md-4"><label class="form-label fs-7">Nama ruangan</label><input name="nama" value="<?php echo e($r?->nama); ?>" class="form-control form-control-sm" required maxlength="120" placeholder="Kamar Tidur 1"></div>
    <div class="col-md-2"><label class="form-label fs-7">Panjang m</label><input type="number" step="0.01" min="0" name="panjang" value="<?php echo e($r?->panjang); ?>" class="form-control form-control-sm"></div>
    <div class="col-md-2"><label class="form-label fs-7">Lebar m</label><input type="number" step="0.01" min="0" name="lebar" value="<?php echo e($r?->lebar); ?>" class="form-control form-control-sm"></div>
    <div class="col-md-2"><label class="form-label fs-7">Tinggi m</label><input type="number" step="0.01" min="0" name="tinggi" value="<?php echo e($r?->tinggi); ?>" class="form-control form-control-sm"></div>
    <div class="col-md-2"><button class="btn btn-sm btn-primary w-100">Simpan</button></div>
</form>
<?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/proyek/_form_ruangan.blade.php ENDPATH**/ ?>