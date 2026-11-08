<?php
    $isiProgres = \App\Support\Izin::isiProgres($user);
    $grup = $pekerjaan->groupBy(fn ($p) => ($p->bangunan_id ?? 0).'-'.($p->lantai_id ?? 0).'-'.($p->ruangan_id ?? 0));
    $node = fn ($b, $l, $r) => $grup->get(($b ?? 0).'-'.($l ?? 0).'-'.($r ?? 0), collect());
    $tambah = fn (array $q) => route('tim.proyek.pekerjaan.create', [$tim->slug, $proyek] + $q);
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div class="fs-5">Total RAB: <span class="fw-bolder" style="color:var(--tp-primary)"><?php echo e($rp($ringkasan['total_rab'])); ?></span> · Progres <?php echo e($ringkasan['progres']); ?>%</div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kelolaRab): ?>
        <div class="d-flex gap-2">
            <a href="<?php echo e($tambah([])); ?>" class="btn btn-sm btn-light">+ Item umum</a>
            <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#fBangunanBaru">+ Bangunan</button>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kelolaRab): ?>
<div class="collapse mb-5" id="fBangunanBaru"><div class="card card-body shadow-sm">
    <?php echo $__env->make('tim.proyek._form_bangunan', ['b' => null, 'aksi' => route('tim.proyek.bangunan.store', [$tim->slug, $proyek])], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($node(null, null, null)->isNotEmpty()): ?>
    <div class="card shadow-sm mb-5"><div class="card-body py-4">
        <div class="fw-bold mb-2">Pekerjaan umum (tanpa lokasi)</div>
        <?php echo $__env->make('tim.proyek._pekerjaan_rows', ['daftar' => $node(null, null, null)], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $proyek->bangunan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
    <div class="card shadow-sm mb-5">
        <div class="card-header align-items-center py-3" style="border-left:4px solid var(--tp-gold)">
            <div><h3 class="card-title fw-bold mb-0">🏠 <?php echo e($b->nama); ?></h3>
                <div class="text-muted fs-8"><?php echo e($b->jenisLabel()); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($b->luas_bangunan): ?> · LB <?php echo e($b->luas_bangunan); ?> m²<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($b->luas_tanah): ?> · LT <?php echo e($b->luas_tanah); ?> m²<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></div></div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kelolaRab): ?>
                <div class="d-flex gap-1 flex-wrap">
                    <a href="<?php echo e($tambah(['bangunan_id' => $b->id])); ?>" class="btn btn-sm btn-light">+ Item</a>
                    <button class="btn btn-sm btn-light" data-bs-toggle="collapse" data-bs-target="#fLantai<?php echo e($b->id); ?>">+ Lantai</button>
                    <button class="btn btn-sm btn-light" data-bs-toggle="collapse" data-bs-target="#fEditB<?php echo e($b->id); ?>">Ubah</button>
                    <form method="POST" action="<?php echo e(route('tim.proyek.bangunan.destroy', [$tim->slug, $proyek, $b])); ?>" data-confirm="Hapus bangunan, lantai, ruangan, dan item RAB di dalamnya?"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-light-danger">Hapus</button></form>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
        <div class="card-body py-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kelolaRab): ?>
                <div class="collapse mb-4" id="fEditB<?php echo e($b->id); ?>"><?php echo $__env->make('tim.proyek._form_bangunan', ['b' => $b, 'aksi' => route('tim.proyek.bangunan.update', [$tim->slug, $proyek, $b])], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
                <div class="collapse mb-4" id="fLantai<?php echo e($b->id); ?>"><?php echo $__env->make('tim.proyek._form_lantai', ['l' => null, 'aksi' => route('tim.proyek.lantai.store', [$tim->slug, $proyek, $b]), 'nomor' => $b->lantai->count() + 1], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php echo $__env->make('tim.proyek._pekerjaan_rows', ['daftar' => $node($b->id, null, null)], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $b->lantai; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $l): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="border rounded p-4 mb-3 ms-md-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                        <div class="fw-bold">▤ <?php echo e($l->nama); ?> <span class="text-muted fs-8 fw-normal"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($l->luas): ?> <?php echo e($l->luas); ?> m²<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($l->tinggi): ?> · tinggi <?php echo e($l->tinggi); ?> m <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kelolaRab): ?>
                            <div class="d-flex gap-1 flex-wrap">
                                <a href="<?php echo e($tambah(['bangunan_id' => $b->id, 'lantai_id' => $l->id])); ?>" class="btn btn-xs btn-sm btn-light py-1">+ Item</a>
                                <button class="btn btn-sm btn-light py-1" data-bs-toggle="collapse" data-bs-target="#fRuang<?php echo e($l->id); ?>">+ Ruangan</button>
                                <button class="btn btn-sm btn-light py-1" data-bs-toggle="collapse" data-bs-target="#fEditL<?php echo e($l->id); ?>">Ubah</button>
                                <form method="POST" action="<?php echo e(route('tim.proyek.lantai.destroy', [$tim->slug, $proyek, $l])); ?>" data-confirm="Hapus lantai beserta ruangan & item RAB-nya?"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-light-danger py-1">Hapus</button></form>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kelolaRab): ?>
                        <div class="collapse mb-3" id="fEditL<?php echo e($l->id); ?>"><?php echo $__env->make('tim.proyek._form_lantai', ['l' => $l, 'aksi' => route('tim.proyek.lantai.update', [$tim->slug, $proyek, $l]), 'nomor' => $l->nomor], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
                        <div class="collapse mb-3" id="fRuang<?php echo e($l->id); ?>"><?php echo $__env->make('tim.proyek._form_ruangan', ['r' => null, 'aksi' => route('tim.proyek.ruangan.store', [$tim->slug, $proyek, $l])], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php echo $__env->make('tim.proyek._pekerjaan_rows', ['daftar' => $node($b->id, $l->id, null)], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $l->ruangan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="bg-light rounded p-3 mb-2 ms-md-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="fw-semibold">◻ <?php echo e($r->nama); ?>

                                    <span class="text-muted fs-8 fw-normal"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r->luasLantai()): ?> lantai <?php echo e($r->luasLantai()); ?> m²<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?> <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r->luasDinding()): ?> · dinding <?php echo e($r->luasDinding()); ?> m²<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span></div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kelolaRab): ?>
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo e($tambah(['bangunan_id' => $b->id, 'lantai_id' => $l->id, 'ruangan_id' => $r->id])); ?>" class="btn btn-sm btn-light py-1">+ Item</a>
                                        <button class="btn btn-sm btn-light py-1" data-bs-toggle="collapse" data-bs-target="#fEditR<?php echo e($r->id); ?>">Ubah</button>
                                        <form method="POST" action="<?php echo e(route('tim.proyek.ruangan.destroy', [$tim->slug, $proyek, $r])); ?>" data-confirm="Hapus ruangan beserta item RAB-nya?"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="btn btn-sm btn-light-danger py-1">Hapus</button></form>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($kelolaRab): ?>
                                <div class="collapse mt-3" id="fEditR<?php echo e($r->id); ?>"><?php echo $__env->make('tim.proyek._form_ruangan', ['r' => $r, 'aksi' => route('tim.proyek.ruangan.update', [$tim->slug, $proyek, $r])], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php echo $__env->make('tim.proyek._pekerjaan_rows', ['daftar' => $node($b->id, $l->id, $r->id)], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    <div class="card shadow-sm"><div class="card-body text-center py-12 text-muted">
        Belum ada bangunan. Tambahkan bangunan (mis. "Rumah Utama"), lalu lantai dan ruangannya, kemudian item pekerjaan seperti pondasi, kolom, dinding, pintu, dan jendela.
    </div></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/proyek/tab_rab.blade.php ENDPATH**/ ?>