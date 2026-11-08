<?php $__env->startSection('title', 'Pembayaran'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('backend.saas._header', ['judul' => 'Pembayaran', 'sub' => 'Konfirmasi transfer'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="row g-4 mb-6">
        <div class="col-md-4"><div class="card card-flush"><div class="card-body py-5"><div class="fs-2 fw-bold text-warning"><?php echo e($ringkasan['menunggu']); ?></div><div class="text-muted fs-7">Menunggu konfirmasi</div></div></div></div>
        <div class="col-md-4"><div class="card card-flush"><div class="card-body py-5"><div class="fs-2 fw-bold text-primary">Rp <?php echo e(number_format($ringkasan['bulan_ini'], 0, ',', '.')); ?></div><div class="text-muted fs-7">Diterima bulan ini</div></div></div></div>
        <div class="col-md-4"><div class="card card-flush"><div class="card-body py-5"><div class="fs-2 fw-bold text-success">Rp <?php echo e(number_format($ringkasan['total'], 0, ',', '.')); ?></div><div class="text-muted fs-7">Total diterima</div></div></div></div>
    </div>

    <div class="card card-flush">
        <div class="card-header py-5">
            <form method="GET" class="d-flex flex-wrap gap-3 w-100">
                <input type="text" name="q" value="<?php echo e($cari); ?>" class="form-control form-control-sm w-250px" placeholder="Cari kode / tim" />
                <select name="status" class="form-select form-select-sm w-200px" onchange="this.form.submit()">
                    <option value="semua" <?php if($status === 'semua'): echo 'selected'; endif; ?>>Semua</option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = \App\Enums\StatusPembayaran::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($s->value); ?>" <?php if($status === $s->value): echo 'selected'; endif; ?>><?php echo e($s->label()); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
                <button class="btn btn-sm btn-light-primary">Cari</button>
            </form>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-4">
                    <thead><tr class="text-muted fw-bold fs-7 text-uppercase"><th>Kode</th><th>Tim</th><th>Jenis</th><th>Paket</th><th>Nominal</th><th>Status</th><th>Tanggal</th><th></th></tr></thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $bayar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr>
                                <td class="fw-bold"><?php echo e($p->kode); ?></td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->team): ?>
                                        <span class="badge badge-light-dark font-monospace"><?php echo e($p->team->slug); ?></span> <?php echo e($p->team->nama); ?>

                                    <?php else: ?> - <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td><?php echo e($p->jenisLabel()); ?></td>
                                <td><?php echo e($p->plan?->nama ?? '-'); ?></td>
                                <td><?php echo e($p->nominalLabel()); ?></td>
                                <td>
                                    <span class="badge badge-light-<?php echo e($p->status->warna()); ?>"><?php echo e($p->status->label()); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->bukti_path): ?> <i class="ki-outline ki-paper-clip text-muted" title="Ada bukti"></i> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="fs-7"><?php echo e($p->created_at->translatedFormat('d M Y H:i')); ?></td>
                                <td class="text-end"><a href="<?php echo e(route('saas.bayar.show', $p)); ?>" class="btn btn-sm btn-light"><?php echo e($p->isMenunggu() ? 'Proses' : 'Detail'); ?></a></td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr><td colspan="8" class="text-center text-muted py-10">Tidak ada pembayaran.</td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php echo e($bayar->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/backend/saas/bayar/index.blade.php ENDPATH**/ ?>