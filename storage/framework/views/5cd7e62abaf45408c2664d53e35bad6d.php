<?php $__env->startSection('title', 'Langganan'); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-5">
    <div class="col-xl-5">
        <div class="card shadow-sm bg-navy text-white mb-5"><div class="card-body p-8">
            <div style="color:#c8d3e6">Paket saat ini</div>
            <div class="fs-2hx fw-bolder"><?php echo e($tim->plan?->nama ?? '-'); ?></div>
            <div class="fs-5 text-gold fw-semibold mb-2"><?php echo e($tim->lifetime ? 'Lifetime — tanpa masa berakhir' : 'Berlaku sampai '.$tim->masaBerlakuLabel()); ?></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $tim->lifetime && $tim->sisaHari() !== null): ?><div style="color:#c8d3e6">Sisa <?php echo e($tim->sisaHari()); ?> hari</div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="mt-4" style="color:#c8d3e6">Kode tim <span class="kode-badge"><?php echo e($tim->slug); ?></span> · maks. <?php echo e($tim->max_users); ?> akun</div>
        </div></div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($boleh): ?>
        <div class="card shadow-sm"><div class="card-body p-8">
            <h4 class="fw-bold mb-4"><?php echo e($tim->lifetime ? 'Ganti paket' : 'Perpanjang / ganti paket'); ?></h4>
            <form method="POST" action="<?php echo e(route('tim.langganan.store', $tim->slug)); ?>">
                <?php echo csrf_field(); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <label class="d-flex align-items-center border rounded p-4 mb-3 gap-3" style="cursor:pointer">
                        <input class="form-check-input" type="radio" name="plan_id" value="<?php echo e($p->id); ?>" <?php if($loop->first): echo 'checked'; endif; ?> required>
                        <span class="flex-grow-1"><span class="fw-bold d-block"><?php echo e($p->nama); ?></span><span class="text-muted fs-7"><?php echo e($p->periodeLabel()); ?></span></span>
                        <span class="fw-bolder" style="color:var(--tp-primary)"><?php echo e($p->hargaLabel()); ?></span>
                    </label>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rekening->isNotEmpty()): ?>
                    <div class="text-muted fs-7 mb-3">Transfer ke: <?php echo e($rekening->map(fn ($r) => $r->bank.' '.$r->nomor.' a.n. '.$r->atas_nama)->implode(' · ')); ?></div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button class="btn btn-success w-100">Buat tagihan & konfirmasi via WhatsApp</button>
            </form>
        </div></div>
        <?php else: ?>
            <div class="alert alert-info">Hanya owner/kontraktor yang dapat memperpanjang langganan.</div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <div class="col-xl-7">
        <div class="card shadow-sm"><div class="card-header"><h3 class="card-title fw-bold">Riwayat pembayaran</h3></div>
            <div class="card-body pt-0 table-responsive">
                <table class="table table-row-dashed fs-7">
                    <thead><tr class="text-muted fs-8 text-uppercase"><th>Invoice</th><th>Jenis</th><th>Paket</th><th class="text-end">Nominal</th><th>Status</th><th>Periode</th></tr></thead>
                    <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $riwayat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr><td class="font-monospace"><?php echo e($b->kode); ?></td><td><?php echo e($b->jenisLabel()); ?></td><td><?php echo e($b->plan?->nama); ?></td>
                            <td class="text-end fw-bold"><?php echo e($b->nominalLabel()); ?></td>
                            <td><span class="badge badge-light-<?php echo e($b->status->warna()); ?>"><?php echo e($b->status->label()); ?></span></td>
                            <td class="text-muted"><?php echo e($b->periode_mulai?->format('d/m/Y')); ?><?php echo e($b->periode_selesai ? ' – '.$b->periode_selesai->format('d/m/Y') : ($b->periode_mulai ? ' – selamanya' : '')); ?></td></tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr><td colspan="6" class="text-center text-muted py-8">Belum ada pembayaran.</td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('tim.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/langganan/index.blade.php ENDPATH**/ ?>