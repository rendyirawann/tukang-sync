<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <?php echo $__env->make('partials.theme-boot', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script>
        // Marks when the entry preloader became visible, so app-auth.js can
        // hold it for exactly 1.2s no matter how fast the page loaded.
        window.__authPreloadStart = Date.now();

        // Click-jacking backstop for browsers that ignore X-Frame-Options.
        if (window.top !== window.self) {
            window.top.location.replace(window.self.location.href);
        }
    </script>

    <?php echo $__env->make('partials.meta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=<?php echo e(str_replace(' ', '+', $brand['font'])); ?>:wght@300;400;500;600;700;800&display=swap" />

    <link href="<?php echo e(asset('assets/plugins/global/plugins.bundle.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('assets/css/style.bundle.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('assets/css/app-auth.css')); ?>" rel="stylesheet" type="text/css" />

    <style>
        :root {
            --bs-font-sans-serif: '<?php echo e($brand['font']); ?>', system-ui, sans-serif;
            /* Still artwork behind the animated canvas — also the fallback
               when the Vanta CDN is unavailable. */
            --auth-bg-light: url('<?php echo e(asset('assets/media/auth/bg11.jpg')); ?>');
            --auth-bg-dark: url('<?php echo e(asset('assets/media/auth/bg11-dark.jpg')); ?>');
        }

        body,
        .auth-card,
        .auth-story {
            font-family: '<?php echo e($brand['font']); ?>', system-ui, -apple-system, 'Segoe UI', sans-serif;
        }
    </style>

    <?php echo $__env->yieldPushContent('stylesheets'); ?>
</head>

<body class="auth-body">

    
    <div id="auth-preloader" role="status" aria-live="polite">
        <div class="auth-preloader__mark">
            <span class="auth-preloader__ring" aria-hidden="true"></span>
            <span class="auth-preloader__ring auth-preloader__ring--alt" aria-hidden="true"></span>
            <img src="<?php echo e($brand['logo_url']); ?>" alt="" width="56" height="56" />
        </div>
        <div class="auth-preloader__label"><?php echo e($brand['name']); ?></div>
        <div class="auth-preloader__bar" aria-hidden="true"><span></span></div>
        <span class="visually-hidden">Memuat aplikasi…</span>
    </div>

    <div class="auth-shell">
        <div class="auth-bg" aria-hidden="true"></div>

        
        
        <div id="auth-vanta" aria-hidden="true"
            data-color-light="#4f46e5" data-bg-light="#eef2f8"
            data-color-dark="#818cf8" data-bg-dark="#0b1120"></div>

        <!--begin::Story panel-->
        <section class="auth-story">
            <a href="<?php echo e(url('/')); ?>" class="auth-story__brand text-decoration-none">
                <img src="<?php echo e($brand['logo_url']); ?>" alt="" class="auth-story__logo" width="52" height="52" />
                <span>
                    <span class="auth-story__name d-block"><?php echo e($brand['name']); ?></span>
                    <span class="auth-story__tagline"><?php echo e($brand['tagline']); ?></span>
                </span>
            </a>

            <div>
                <h1 class="auth-story__headline">Kelola operasional dalam satu panel.</h1>
                <p class="auth-story__lead"><?php echo e($brand['description']); ?></p>

                <ul class="auth-story__points">
                    <li>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5" /></svg>
                        Manajemen pengguna, peran, dan hak akses granular
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5" /></svg>
                        Jejak audit lengkap untuk setiap perubahan data
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5" /></svg>
                        Sesi login terpantau dengan proteksi berlapis
                    </li>
                </ul>
            </div>

            <p class="auth-story__foot mb-0">
                &copy; <?php echo e(now()->year); ?> <?php echo e($brand['owner']); ?> &middot; <?php echo e($brand['name']); ?>

            </p>
        </section>
        <!--end::Story panel-->

        <!--begin::Form panel-->
        <main class="auth-panel">
            <?php echo $__env->yieldContent('content'); ?>
        </main>
        <!--end::Form panel-->
    </div>

    <!--begin::Sign-in progress overlay-->
    <div id="auth-progress" role="status" aria-live="polite" aria-hidden="true">
        <div class="auth-progress__ring" aria-hidden="true"></div>
        <p class="auth-progress__step" data-progress-step>Memverifikasi kredensial</p>
        <div class="auth-progress__dots" data-progress-dots aria-hidden="true">
            <i></i><i></i><i></i>
        </div>
    </div>
    <!--end::Sign-in progress overlay-->

    <script src="<?php echo e(asset('assets/plugins/global/plugins.bundle.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/scripts.bundle.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/app-auth.js')); ?>"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>

    <?php echo $__env->make('partials._rt', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/auth/app.blade.php ENDPATH**/ ?>