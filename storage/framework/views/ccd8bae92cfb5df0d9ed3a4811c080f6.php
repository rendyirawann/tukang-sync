<?php
    $u = auth()->user();
    $menu = [
        ['tim.dashboard', 'tim.dashboard', 'ki-element-11', 'Dasbor'],
        ['tim.proyek.index', 'tim.proyek.*', 'ki-abstract-26', 'Proyek & RAB'],
        ['tim.material.index', 'tim.material.*', 'ki-cube-2', 'Katalog Material'],
        ['tim.analisa.index', 'tim.analisa.*', 'ki-calculator', 'Analisa Harga'],
        ['tim.anggota.index', 'tim.anggota.*', 'ki-people', 'Anggota Tim'],
        ['tim.langganan.index', 'tim.langganan.*', 'ki-wallet', 'Langganan'],
        ['tim.akun.index', 'tim.akun.*', 'ki-user', 'Akun Saya'],
    ];
    $sisa = $tim->sisaHari();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <?php echo $__env->make('tim.layout.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>
        body { background: #f3f5f9; }
        [data-bs-theme="dark"] body { background: #0b1220; }
        .tp-side { position: fixed; inset: 0 auto 0 0; width: 250px; background: linear-gradient(180deg, #0F1D33, #16243C); color: #c8d3e6; z-index: 105; display: flex; flex-direction: column; transition: transform .25s; }
        .tp-side .brand { padding: 20px; border-bottom: 1px solid rgba(255,255,255,.07); }
        .tp-side a.nav-i { display: flex; align-items: center; gap: 12px; padding: 11px 20px; color: #c8d3e6; font-weight: 500; border-left: 3px solid transparent; }
        .tp-side a.nav-i i { color: #7f93b5; font-size: 1.35rem; }
        .tp-side a.nav-i:hover { background: rgba(255,255,255,.04); color: #fff; }
        .tp-side a.nav-i.active { background: rgba(201,164,76,.10); color: #fff; border-left-color: var(--tp-gold); }
        .tp-side a.nav-i.active i { color: var(--tp-gold); }
        .tp-main { margin-left: 250px; min-height: 100vh; display: flex; flex-direction: column; }
        .tp-top { background: var(--bs-body-bg); border-bottom: 1px solid var(--bs-gray-200); position: sticky; top: 0; z-index: 100; }
        .tp-overlay { display: none; }
        @media (max-width: 991px) {
            .tp-side { transform: translateX(-100%); }
            .tp-side.open { transform: none; }
            .tp-main { margin-left: 0; }
            .tp-overlay.open { display: block; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 104; }
        }
    </style>
    <?php echo $__env->yieldPushContent('stylesheets'); ?>
</head>
<body class="app-blank">
    <aside class="tp-side" id="tpSide">
        <div class="brand">
            <div class="d-flex align-items-center gap-2 mb-2">
                <img src="<?php echo e($brand['logo_url']); ?>" alt="" width="30" height="30">
                <span class="fw-bold text-white fs-5">Transaksi Pekerjaan</span>
            </div>
            <div class="text-white fw-semibold text-truncate"><?php echo e($tim->nama); ?></div>
            <span class="kode-badge fs-8"><?php echo e($tim->slug); ?></span>
        </div>
        <nav class="py-3 flex-grow-1 overflow-auto">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $menu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$route, $pola, $ikon, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <a href="<?php echo e(route($route, $tim->slug)); ?>" class="nav-i <?php echo e(request()->routeIs($pola) ? 'active' : ''); ?>">
                    <i class="ki-duotone <?php echo e($ikon); ?>"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                    <?php echo e($label); ?>

                </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </nav>
        <form method="POST" action="<?php echo e(route('tim.logout', $tim->slug)); ?>" class="p-4">
            <?php echo csrf_field(); ?>
            <button class="btn btn-sm w-100 btn-outline btn-outline-secondary text-white">Keluar</button>
        </form>
    </aside>
    <div class="tp-overlay" id="tpOverlay"></div>

    <div class="tp-main">
        <header class="tp-top">
            <div class="d-flex align-items-center justify-content-between px-4 px-lg-8 py-3 gap-3">
                <div class="d-flex align-items-center gap-3 min-w-0">
                    <button class="btn btn-icon btn-sm d-lg-none" id="tpToggle" aria-label="Menu">
                        <i class="ki-duotone ki-abstract-14 fs-2"><span class="path1"></span><span class="path2"></span></i>
                    </button>
                    <div class="min-w-0">
                        <div class="fw-bold fs-5 text-truncate"><?php echo $__env->yieldContent('title', 'Dasbor'); ?></div>
                        <div class="text-muted fs-8 text-truncate"><?php echo e($tim->nama); ?> · <span class="kode-badge fs-9"><?php echo e($tim->slug); ?></span></div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tim->hanyaBaca()): ?>
                        <a href="<?php echo e(route('tim.langganan.index', $tim->slug)); ?>" class="badge badge-light-danger fs-8 py-2">Masa habis – hanya baca</a>
                    <?php elseif($tim->lifetime): ?>
                        <span class="badge badge-light-success fs-8 py-2">Lifetime</span>
                    <?php else: ?>
                        <a href="<?php echo e(route('tim.langganan.index', $tim->slug)); ?>" class="badge <?php echo e($sisa !== null && $sisa <= 7 ? 'badge-light-warning' : 'badge-light-primary'); ?> fs-8 py-2">Sisa <?php echo e($sisa); ?> hari</a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="text-end d-none d-sm-block">
                        <div class="fw-semibold fs-7"><?php echo e($u->name); ?></div>
                        <div class="text-muted fs-8"><?php echo e($u->roleLabel()); ?></div>
                    </div>
                    <a href="<?php echo e(route('tim.akun.index', $tim->slug)); ?>" class="symbol symbol-35px symbol-circle">
                        <span class="symbol-label fw-bold text-white" style="background:var(--tp-primary)"><?php echo e($u->initials); ?></span>
                    </a>
                </div>
            </div>
        </header>
        <main class="px-4 px-lg-8 py-6 flex-grow-1">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tim->hanyaBaca()): ?>
                <div class="alert alert-danger d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span>Masa langganan berakhir <?php echo e($tim->masaBerlakuLabel()); ?>. Data tetap bisa dilihat, tetapi tidak bisa ditambah atau diubah.</span>
                    <a href="<?php echo e(route('tim.langganan.index', $tim->slug)); ?>" class="btn btn-sm btn-danger">Perpanjang</a>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>

    <?php echo $__env->make('tim.layout.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script>
        (function () {
            var s = document.getElementById('tpSide'), o = document.getElementById('tpOverlay'), t = document.getElementById('tpToggle');
            function tog() { s.classList.toggle('open'); o.classList.toggle('open'); }
            t && t.addEventListener('click', tog); o.addEventListener('click', tog);
        })();
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/layout/app.blade.php ENDPATH**/ ?>