<?php $__env->startSection('title', 'Tim Pelanggan'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('backend.saas._header', [
        'judul' => 'Tim Pelanggan',
        'sub' => 'Tim',
        'aksi' => '<a href="' . route('saas.tim.create') . '" class="btn btn-sm btn-primary"><i class="ki-outline ki-plus fs-5"></i> Daftarkan tim</a>',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="row g-4 mb-6">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [['Total', $ringkasan['total'], 'primary', ''], ['Aktif', $ringkasan['aktif'], 'success', 'aktif'], ['Menunggu bayar', $ringkasan['menunggu'], 'warning', 'menunggu'], ['Masa habis', $ringkasan['habis'], 'danger', 'habis']]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$l, $n, $w, $s]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="col-6 col-lg-3">
                <a href="<?php echo e(route('saas.tim.index', $s ? ['status' => $s] : [])); ?>" class="card card-flush h-100">
                    <div class="card-body py-5">
                        <div class="fs-2 fw-bold text-<?php echo e($w); ?>"><?php echo e($n); ?></div>
                        <div class="text-muted fs-7"><?php echo e($l); ?></div>
                    </div>
                </a>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>

    <div class="card card-flush">
        <div class="card-header py-5">
            <form method="GET" class="d-flex flex-wrap gap-3 w-100">
                <input type="text" name="q" value="<?php echo e($cari); ?>" class="form-control form-control-sm w-250px"
                    placeholder="Cari nama, kode, email, WA" />
                <select name="status" class="form-select form-select-sm w-200px" onchange="this.form.submit()">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $filter; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v => $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <option value="<?php echo e($v); ?>" <?php if($status === $v): echo 'selected'; endif; ?>><?php echo e($l); ?></option>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </select>
                <button class="btn btn-sm btn-light-primary">Cari</button>
            </form>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-4">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>Kode</th><th>Tim</th><th>Paket</th><th>Status</th><th>Masa aktif</th><th>Akun</th><th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tim; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr>
                                <td><span class="badge badge-light-dark fs-6 font-monospace"><?php echo e($t->slug); ?></span></td>
                                <td>
                                    <a href="<?php echo e(route('saas.tim.show', $t->slug)); ?>" class="text-gray-900 fw-bold text-hover-primary"><?php echo e($t->nama); ?></a>
                                    <div class="text-muted fs-7"><?php echo e($t->kota); ?> <?php echo e($t->no_wa ? '· ' . $t->no_wa : ''); ?></div>
                                </td>
                                <td><?php echo e($t->plan?->nama ?? '-'); ?></td>
                                <td>
                                    <span class="badge badge-light-<?php echo e($t->status->warna()); ?>"><?php echo e($t->status->label()); ?></span>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($t->hanyaBaca()): ?>
                                        <span class="badge badge-light-danger">Masa habis</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <?php echo e($t->masaBerlakuLabel()); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! is_null($t->sisaHari()) && $t->isAktif()): ?>
                                        <div class="text-muted fs-8">sisa <?php echo e($t->sisaHari()); ?> hari</div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td><?php echo e($t->users_count); ?>/<?php echo e($t->max_users); ?></td>
                                <td class="text-end">
                                    <a href="<?php echo e(route('saas.tim.show', $t->slug)); ?>" class="btn btn-sm btn-light">Detail</a>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr><td colspan="7" class="text-center text-muted py-10">Belum ada tim.</td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php echo e($tim->links()); ?>

        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/backend/saas/tim/index.blade.php ENDPATH**/ ?>