<?php $__env->startSection('title', 'Dasbor'); ?>

<?php $rp = fn ($n) => 'Rp '.number_format((int) $n, 0, ',', '.'); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-5 mb-6">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
        ['Jumlah proyek', $jumlahProyek, ($perStatus['berjalan'] ?? 0).' berjalan · '.($perStatus['selesai'] ?? 0).' selesai'],
        ['Total RAB', $rp($totalRab), 'semua proyek'],
        ['Progres rata-rata', $progres.'%', 'tertimbang nilai RAB'],
        ['Belanja bulan ini', $rp($belanjaBulanIni), now()->translatedFormat('F Y')],
    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$judul, $nilai, $sub]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <div class="col-6 col-xl-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="text-muted fs-7 mb-1"><?php echo e($judul); ?></div>
                    <div class="fs-2 fw-bolder" style="color:var(--tp-primary)"><?php echo e($nilai); ?></div>
                    <div class="text-muted fs-8"><?php echo e($sub); ?></div>
                </div>
            </div>
        </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
</div>

<div class="row g-5">
    <div class="col-xl-8">
        <div class="card shadow-sm h-100">
            <div class="card-header align-items-center">
                <h3 class="card-title fw-bold">Proyek terbaru</h3>
                <a href="<?php echo e(route('tim.proyek.index', $tim->slug)); ?>" class="btn btn-sm btn-light">Semua proyek</a>
            </div>
            <div class="card-body pt-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $proyek; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <a href="<?php echo e(route('tim.proyek.show', [$tim->slug, $k])); ?>" class="d-block py-4 border-bottom text-gray-800">
                        <div class="d-flex justify-content-between gap-3">
                            <div class="min-w-0"><div class="fw-bold text-truncate"><?php echo e($k->nama); ?></div>
                                <div class="text-muted fs-8"><?php echo e($k->lokasi ?: '-'); ?> · <?php echo e(ucfirst($k->status->value ?? (string) $k->status)); ?></div></div>
                            <div class="text-end"><div class="fw-bold"><?php echo e($rp($k->rab_total)); ?></div><div class="text-muted fs-8"><?php echo e($k->rab_progres); ?>%</div></div>
                        </div>
                        <div class="progress progress-thin mt-2"><div class="progress-bar" style="width:<?php echo e($k->rab_progres); ?>%;background:var(--tp-gold)"></div></div>
                    </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="text-center py-10 text-muted">Belum ada proyek.
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(\App\Support\Izin::kelolaKegiatan(auth()->user())): ?>
                            <div class="mt-3"><a href="<?php echo e(route('tim.proyek.create', $tim->slug)); ?>" class="btn btn-primary btn-sm">Buat proyek pertama</a></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card shadow-sm mb-5 bg-navy text-white">
            <div class="card-body">
                <div class="fs-7" style="color:#c8d3e6">Langganan</div>
                <div class="fs-3 fw-bold"><?php echo e($tim->plan?->nama ?? '-'); ?></div>
                <div class="mb-3 text-gold fw-semibold"><?php echo e($tim->lifetime ? 'Lifetime' : 'Berlaku sampai '.$tim->masaBerlakuLabel()); ?></div>
                <a href="<?php echo e(route('tim.langganan.index', $tim->slug)); ?>" class="btn btn-sm btn-gold">Kelola langganan</a>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header"><h3 class="card-title fw-bold">Log harian terbaru</h3></div>
            <div class="card-body pt-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $log; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="py-3 border-bottom">
                        <div class="d-flex justify-content-between fs-8 text-muted"><span><?php echo e($l->tanggal->translatedFormat('d M Y')); ?></span><span><?php echo e($l->pembuat?->name); ?></span></div>
                        <div class="fw-semibold fs-7"><?php echo e($l->kegiatan?->nama); ?></div>
                        <div class="fs-7"><?php echo e(\Illuminate\Support\Str::limit($l->uraian, 90)); ?></div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="text-muted py-6 text-center">Belum ada catatan lapangan.</div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('tim.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/dashboard.blade.php ENDPATH**/ ?>