<?php $__env->startSection('title', 'Status Tim '.$tim->slug); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-10" style="max-width:760px">
    <div class="card shadow-sm mb-6">
        <div class="card-body p-10 text-center">
            <div class="text-muted mb-2">Kode tim Anda</div>
            <div class="fw-bolder font-monospace mb-3" style="font-size:3.5rem;letter-spacing:.25em;color:var(--tp-primary)"><?php echo e($tim->slug); ?></div>
            <div class="fw-semibold fs-4 mb-3"><?php echo e($tim->nama); ?></div>
            <span class="badge badge-light-<?php echo e($tim->status->warna()); ?> fs-6"><?php echo e($tim->status->label()); ?></span>
            <p class="text-muted mt-4 mb-0">Simpan kode ini. Halaman login tim Anda: <a href="<?php echo e(route('tim.login', $tim->slug)); ?>"><?php echo e(route('tim.login', $tim->slug)); ?></a></p>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tim->isAktif() && ! $bayar): ?>
        <div class="card shadow-sm"><div class="card-body p-8 text-center">
            <h3 class="fw-bold">Tim sudah aktif 🎉</h3>
            <p class="text-muted">Masa berlaku: <?php echo e($tim->masaBerlakuLabel()); ?></p>
            <a href="<?php echo e(route('tim.login', $tim->slug)); ?>" class="btn btn-primary btn-lg">Masuk ke panel tim</a>
        </div></div>
    <?php elseif($tim->status->value === 'ditangguhkan'): ?>
        <div class="alert alert-danger">Akses tim ini sedang ditangguhkan. Silakan hubungi admin.</div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bayar): ?>
        <div class="card shadow-sm mb-6"><div class="card-body p-8">
            <h4 class="fw-bold mb-5">Tagihan <?php echo e($bayar->jenisLabel()); ?></h4>
            <table class="table table-row-dashed mb-0">
                <tr><td class="text-muted">Invoice</td><td class="fw-bold font-monospace"><?php echo e($bayar->kode); ?></td></tr>
                <tr><td class="text-muted">Paket</td><td class="fw-bold"><?php echo e($bayar->plan?->nama); ?></td></tr>
                <tr><td class="text-muted">Nominal</td><td class="fw-bolder fs-3" style="color:var(--tp-primary)"><?php echo e($bayar->nominalLabel()); ?></td></tr>
            </table>
        </div></div>

        <div class="card shadow-sm mb-6"><div class="card-body p-8">
            <h4 class="fw-bold mb-5">Transfer ke salah satu rekening</h4>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rekening; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="d-flex justify-content-between align-items-center border rounded p-4 mb-3">
                    <div><div class="fw-bold"><?php echo e($r->bank); ?></div>
                        <div class="fs-4 fw-bolder font-monospace"><?php echo e($r->nomor); ?></div>
                        <div class="text-muted fs-7">a.n. <?php echo e($r->atas_nama); ?></div></div>
                    <button type="button" class="btn btn-sm btn-light" data-copy="<?php echo e($r->nomor); ?>">Salin</button>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <p class="text-muted">Nomor rekening akan diberikan admin lewat WhatsApp.</p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div></div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($linkWa): ?>
            <div class="card shadow-sm"><div class="card-body p-8 text-center">
                <p class="mb-5">Setelah transfer, kirim bukti ke WhatsApp admin<?php echo e($kontak ? ' ('.$kontak->nama.')' : ''); ?>. Akun aktif setelah admin mengonfirmasi.</p>
                <a href="<?php echo e($linkWa); ?>" id="tombolWa" target="_blank" rel="noopener" class="btn btn-lg btn-success">Konfirmasi via WhatsApp</a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($baru): ?><div class="text-muted fs-7 mt-3" id="infoWa">Membuka WhatsApp otomatis…</div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($baru && $linkWa): ?>
<?php $__env->startPush('scripts'); ?>
<script>
    setTimeout(function () { window.location.href = <?php echo json_encode($linkWa, 15, 512) ?>; }, 1500);
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php echo $__env->make('tim.layout.polos', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/status.blade.php ENDPATH**/ ?>