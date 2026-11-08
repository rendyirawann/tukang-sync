<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <base href="<?php echo e(url('/')); ?>/" />

    
    <?php echo $__env->make('partials.theme-boot', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('partials.meta', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!--begin::Fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=<?php echo e(str_replace(' ', '+', $brand['font'])); ?>:wght@300;400;500;600;700;800&display=swap" />
    <!--end::Fonts-->

    <!--begin::Vendor Stylesheets-->
    <link href="<?php echo e(asset('assets/plugins/custom/datatables/datatables.bundle.css')); ?>" rel="stylesheet" type="text/css" />
    <!--end::Vendor Stylesheets-->

    <!--begin::Global Stylesheets Bundle-->
    <link href="<?php echo e(asset('assets/plugins/global/plugins.bundle.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('assets/css/style.bundle.css')); ?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo e(asset('assets/css/app-shell.css')); ?>" rel="stylesheet" type="text/css" />
    <!--end::Global Stylesheets Bundle-->

    <style>
        :root {
            --bs-font-sans-serif: '<?php echo e($brand['font']); ?>', system-ui, sans-serif;
            --bs-body-font-family: '<?php echo e($brand['font']); ?>', system-ui, sans-serif;
        }

        body,
        h1, h2, h3, h4, h5, h6,
        .h1, .h2, .h3, .h4, .h5, .h6 {
            font-family: '<?php echo e($brand['font']); ?>', system-ui, -apple-system, 'Segoe UI', sans-serif;
        }

        /* --- Off-canvas sidebar (kept as a drawer at every width so the
               content area always uses the full viewport) --- */
        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .45);
            z-index: 104;
            opacity: 0;
            visibility: hidden;
            transition: opacity .25s ease, visibility .25s ease;
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        #kt_app_sidebar {
            position: fixed;
            inset-block: 0;
            inset-inline-start: 0;
            width: min(88vw, 290px);
            z-index: 105;
            background: var(--bs-body-bg);
            border-inline-end: 1px solid var(--bs-gray-200);
            transform: translateX(-100%);
            transition: transform .28s cubic-bezier(.4, 0, .2, 1);
            overflow-y: auto;
            overscroll-behavior: contain;
        }

        #kt_app_sidebar.active {
            transform: translateX(0);
        }

        [data-bs-theme="dark"] #kt_app_sidebar {
            background: #1e1e2d;
            border-inline-end-color: rgba(255, 255, 255, .07);
        }

        body.app-sidebar-open {
            overflow: hidden;
        }

        /* --- Shell --- */
        .app-header {
            background: var(--bs-body-bg);
            border-bottom: 1px solid var(--bs-gray-200);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        [data-bs-theme="dark"] .app-header {
            border-bottom-color: rgba(255, 255, 255, .08);
        }

        .app-header__bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-height: var(--app-header-h);
        }

        .app-main {
            display: flex;
            flex-direction: column;
            min-height: 100dvh;
        }

        .app-content {
            flex: 1 1 auto;
            padding-block: clamp(14px, 2vw, 26px);
        }

        @media (prefers-reduced-motion: reduce) {
            #kt_app_sidebar,
            .sidebar-overlay {
                transition: none;
            }
        }
    </style>

    <script>
        // Click-jacking backstop for browsers that ignore X-Frame-Options.
        if (window.top !== window.self) {
            window.top.location.replace(window.self.location.href);
        }
    </script>

    <?php echo $__env->yieldPushContent('stylesheets'); ?>
</head>

<body id="kt_body" class="app-blank">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(request()->boolean('welcome')): ?>
        
        <div id="app-reveal" aria-hidden="true"></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="app-main">

        <!--begin::Header-->
        <header class="app-header" id="kt_header">
            <div class="container-fluid">
                <div class="app-header__bar">

                    <!--begin::Brand + sidebar toggle-->
                    <div class="d-flex align-items-center gap-3 min-w-0">
                        <button type="button"
                            class="btn btn-icon btn-active-color-primary w-35px h-35px ms-n2"
                            data-app-sidebar-toggle aria-controls="kt_app_sidebar" aria-expanded="false"
                            aria-label="Buka menu navigasi">
                            <i class="ki-duotone ki-abstract-14 fs-2"><span class="path1"></span><span class="path2"></span></i>
                        </button>

                        <a href="<?php echo e(route('dashboard')); ?>" class="app-brand">
                            <img src="<?php echo e($brand['logo_url']); ?>" alt="" class="app-brand__mark" width="34" height="34" />
                            <span class="min-w-0">
                                <span class="app-brand__name d-block"><?php echo e($brand['name']); ?></span>
                                <span class="app-brand__tagline"><?php echo e($brand['tagline']); ?></span>
                            </span>
                        </a>
                    </div>
                    <!--end::Brand-->

                    <!--begin::Topbar-->
                    <?php echo $__env->make('backend.layout.navbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <!--end::Topbar-->
                </div>
            </div>

            <!--begin::Primary menu (desktop)-->
            <div class="container-fluid d-none d-lg-block border-top" id="kt_header_nav">
                <?php echo $__env->make('backend.layout.menu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
            <!--end::Primary menu-->
        </header>
        <!--end::Header-->

        <!--begin::Content-->
        <main class="app-content" id="kt_content">
            <div class="container-fluid">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </main>
        <!--end::Content-->

        <?php echo $__env->make('backend.layout.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>

    <!--begin::Sidebar drawer-->
    <?php echo $__env->make('backend.layout.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="sidebar-overlay" id="kt_sidebar_overlay"></div>
    <!--end::Sidebar drawer-->

    <!--begin::Javascript-->
    <script>
        var hostUrl = "<?php echo e(asset('assets/')); ?>";
    </script>
    <script src="<?php echo e(asset('assets/plugins/global/plugins.bundle.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/scripts.bundle.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/custom/datatables/datatables.bundle.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/widgets.bundle.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/app-shell.js')); ?>"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.toastr) {
                toastr.options = {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toastr-top-right',
                    timeOut: 5000
                };
                <?php if(session('success')): ?> toastr.success(<?php echo json_encode(session('success'), 15, 512) ?>); <?php endif; ?>
                <?php if(session('error')): ?> toastr.error(<?php echo json_encode(session('error'), 15, 512) ?>); <?php endif; ?>
                <?php if(session('warning')): ?> toastr.warning(<?php echo json_encode(session('warning'), 15, 512) ?>); <?php endif; ?>
                <?php if(session('info')): ?> toastr.info(<?php echo json_encode(session('info'), 15, 512) ?>); <?php endif; ?>
            }

            <?php if(auth()->guard()->check()): ?>
            // Server-initiated logout (password change, ban, admin action).
            var forceLogoutWatcher = setInterval(function () {
                if (!window.Echo) {
                    return;
                }
                clearInterval(forceLogoutWatcher);
                window.Echo.private('App.Models.User.' + <?php echo json_encode(auth()->id(), 15, 512) ?>)
                    .listen('ForceLogoutNotification', function (e) {
                        Swal.fire({
                            title: 'Keamanan Akun',
                            text: e.message,
                            icon: 'warning',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            confirmButtonText: 'OK, Logout',
                            confirmButtonColor: '#d33'
                        }).then(function () {
                            window.location.href = <?php echo json_encode(route('login'), 15, 512) ?>;
                        });
                    });
            }, 500);
            setTimeout(function () { clearInterval(forceLogoutWatcher); }, 15000);
            <?php endif; ?>
        });
    </script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
    <!--end::Javascript-->

    <?php echo $__env->make('partials._rt', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/backend/layout/app.blade.php ENDPATH**/ ?>