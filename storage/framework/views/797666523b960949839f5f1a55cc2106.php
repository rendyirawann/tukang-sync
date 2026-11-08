<!DOCTYPE html>
<html lang="id">
<head>
    <?php echo $__env->make('tim.layout.head', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <style>
        body { background: #f4f6fa; }
        .pub-nav { background: var(--tp-dark); }
        .pub-nav a { color: #e9eef7; }
        .hero { background: linear-gradient(135deg, #0D1830, #2B5288 60%, #16243C); color: #fff; }
        .card-plan.populer { border: 2px solid var(--tp-gold); }
    </style>
    <?php echo $__env->yieldPushContent('stylesheets'); ?>
</head>
<body class="app-blank">
    <nav class="pub-nav py-3">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="<?php echo e(route('beranda')); ?>" class="d-flex align-items-center gap-2 text-white fw-bold fs-4">
                <img src="<?php echo e($brand['logo_url']); ?>" alt="" width="32" height="32"> Transaksi Pekerjaan
            </a>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('beranda')); ?>#masuk" class="btn btn-sm btn-outline btn-outline-light text-white">Masuk</a>
                <a href="<?php echo e(route('daftar')); ?>" class="btn btn-sm btn-gold">Daftar</a>
            </div>
        </div>
    </nav>
    <?php echo $__env->yieldContent('content'); ?>
    <footer class="text-center text-muted py-8 fs-7">&copy; <?php echo e(date('Y')); ?> Transaksi Pekerjaan</footer>
    <?php echo $__env->make('tim.layout.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/tim/layout/polos.blade.php ENDPATH**/ ?>