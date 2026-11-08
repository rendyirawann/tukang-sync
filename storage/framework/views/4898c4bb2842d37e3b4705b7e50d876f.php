
<?php
    $user = auth()->user();
    $isSuperadmin = $user?->hasRole(['Superadmin', 'superadmin']) ?? false;
?>

<div class="app-topbar">

    <!--begin::Notifications-->
    <div class="position-relative">
        <button type="button"
            class="btn btn-icon btn-color-gray-600 btn-active-color-primary btn-outline btn-active-bg-light w-35px h-35px w-lg-40px h-lg-40px position-relative"
            data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end"
            aria-label="Notifikasi" aria-haspopup="true">
            <i class="ki-duotone ki-notification-bing fs-2">
                <span class="path1"></span><span class="path2"></span><span class="path3"></span>
            </i>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($notificationCount ?? 0) > 0): ?>
                <span class="app-notif-dot" aria-hidden="true"></span>
                <span class="visually-hidden"><?php echo e($notificationCount); ?> notifikasi baru</span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </button>

        <div class="menu menu-sub menu-sub-dropdown menu-column w-300px w-lg-350px" data-kt-menu="true">
            <div class="d-flex align-items-center justify-content-between px-5 pt-5 pb-3">
                <span class="fs-5 fw-bold text-gray-900">Notifikasi</span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(($notificationCount ?? 0) > 0): ?>
                    <span class="badge badge-light-primary fw-semibold"><?php echo e($notificationCount); ?> baru</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="separator"></div>

            <div class="app-notif-list px-3 py-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $notifications ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <a href="<?php echo e($item['url']); ?>" class="app-notif-item">
                        <span class="app-notif-item__icon bg-light-<?php echo e($item['color']); ?>">
                            <i class="ki-duotone <?php echo e($item['icon']); ?> fs-4 text-<?php echo e($item['color']); ?>">
                                <span class="path1"></span><span class="path2"></span>
                                <span class="path3"></span><span class="path4"></span>
                            </i>
                        </span>
                        <span class="d-flex flex-column min-w-0">
                            <span class="fs-7 fw-semibold text-gray-900 text-truncate"><?php echo e($item['title']); ?></span>
                            <span class="fs-8 text-muted"><?php echo e($item['actor']); ?> &middot; <?php echo e($item['time']); ?></span>
                        </span>
                    </a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="text-center text-muted fs-7 py-8">
                        <i class="ki-duotone ki-notification-status fs-2x text-gray-400 d-block mb-3">
                            <span class="path1"></span><span class="path2"></span>
                            <span class="path3"></span><span class="path4"></span>
                        </i>
                        Belum ada aktivitas terbaru.
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="separator"></div>
            <div class="py-3 text-center">
                <a href="<?php echo e(route('log-activity.index')); ?>"
                    class="btn btn-sm btn-light-primary">Lihat semua aktivitas</a>
            </div>
        </div>
    </div>
    <!--end::Notifications-->

    <!--begin::Theme mode-->
    <div class="position-relative">
        <button type="button"
            class="btn btn-icon btn-color-gray-600 btn-active-color-primary btn-outline btn-active-bg-light w-35px h-35px w-lg-40px h-lg-40px"
            data-kt-menu-trigger="{default:'click', lg: 'hover'}" data-kt-menu-attach="parent"
            data-kt-menu-placement="bottom-end" aria-label="Ubah tema tampilan">
            <i class="ki-duotone ki-night-day theme-light-show fs-2">
                <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                <span class="path4"></span><span class="path5"></span><span class="path6"></span>
                <span class="path7"></span><span class="path8"></span><span class="path9"></span>
                <span class="path10"></span>
            </i>
            <i class="ki-duotone ki-moon theme-dark-show fs-2"><span class="path1"></span><span class="path2"></span></i>
        </button>

        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-3 fs-base w-150px"
            data-kt-menu="true" data-kt-element="theme-mode-menu">
            <div class="menu-item px-3 my-0">
                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                    <span class="menu-icon" data-kt-element="icon">
                        <i class="ki-duotone ki-night-day fs-3">
                            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                            <span class="path4"></span><span class="path5"></span><span class="path6"></span>
                            <span class="path7"></span><span class="path8"></span><span class="path9"></span>
                            <span class="path10"></span>
                        </i>
                    </span>
                    <span class="menu-title">Terang</span>
                </a>
            </div>
            <div class="menu-item px-3 my-0">
                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                    <span class="menu-icon" data-kt-element="icon">
                        <i class="ki-duotone ki-moon fs-3"><span class="path1"></span><span class="path2"></span></i>
                    </span>
                    <span class="menu-title">Gelap</span>
                </a>
            </div>
            <div class="menu-item px-3 my-0">
                <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
                    <span class="menu-icon" data-kt-element="icon">
                        <i class="ki-duotone ki-screen fs-3">
                            <span class="path1"></span><span class="path2"></span>
                            <span class="path3"></span><span class="path4"></span>
                        </i>
                    </span>
                    <span class="menu-title">Sistem</span>
                </a>
            </div>
        </div>
    </div>
    <!--end::Theme mode-->

    <!--begin::Account-->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
        <div class="position-relative">
            <button type="button"
                class="btn btn-flex btn-outline btn-active-bg-light align-items-center px-2 px-lg-3 py-1 h-35px h-lg-40px"
                data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end"
                aria-label="Menu akun" aria-haspopup="true">
                <?php if (isset($component)) { $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.avatar','data' => ['user' => $user,'size' => 28]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user),'size' => 28]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b)): ?>
<?php $attributes = $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b; ?>
<?php unset($__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b)): ?>
<?php $component = $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b; ?>
<?php unset($__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b); ?>
<?php endif; ?>
                <span class="d-none d-md-flex flex-column align-items-start ms-2 lh-sm">
                    <span class="fs-8 fw-bold text-gray-800 text-truncate" style="max-width: 130px;"><?php echo e($user->name); ?></span>
                    <span class="fs-9 text-muted"><?php echo e($user->role_label); ?></span>
                </span>
                <i class="ki-outline ki-down fs-7 ms-1 ms-lg-2 text-gray-500"></i>
            </button>

            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-3 fs-6 w-275px"
                data-kt-menu="true">

                <div class="menu-item px-3">
                    <div class="menu-content d-flex align-items-center px-3">
                        <?php if (isset($component)) { $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.avatar','data' => ['user' => $user,'size' => 46,'class' => 'me-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user),'size' => 46,'class' => 'me-4']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b)): ?>
<?php $attributes = $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b; ?>
<?php unset($__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b)): ?>
<?php $component = $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b; ?>
<?php unset($__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b); ?>
<?php endif; ?>
                        <div class="d-flex flex-column min-w-0">
                            <div class="fw-bold fs-6 text-truncate"><?php echo e($user->name); ?></div>
                            <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 align-self-start mt-1">
                                <?php echo e($user->role_label); ?>

                            </span>
                            <span class="fw-semibold text-muted fs-8 text-truncate mt-1"><?php echo e($user->email); ?></span>
                        </div>
                    </div>
                </div>

                <div class="separator my-2"></div>

                <div class="menu-item px-5">
                    <a href="<?php echo e(route('account.index')); ?>" class="menu-link px-5">
                        <span class="menu-icon"><i class="ki-outline ki-profile-circle fs-4"></i></span>
                        <span class="menu-title">Profil Saya</span>
                    </a>
                </div>
                <div class="menu-item px-5">
                    <a href="<?php echo e(route('my-security.index')); ?>" class="menu-link px-5">
                        <span class="menu-icon"><i class="ki-outline ki-shield-tick fs-4"></i></span>
                        <span class="menu-title">Keamanan</span>
                    </a>
                </div>
                <div class="menu-item px-5">
                    <a href="<?php echo e(route('my-activity.index')); ?>" class="menu-link px-5">
                        <span class="menu-icon"><i class="ki-outline ki-notepad fs-4"></i></span>
                        <span class="menu-title">Aktivitas Saya</span>
                    </a>
                </div>
                <div class="menu-item px-5">
                    <a href="<?php echo e(route('my-login-session.index')); ?>" class="menu-link px-5">
                        <span class="menu-icon"><i class="ki-outline ki-technology-2 fs-4"></i></span>
                        <span class="menu-title">Sesi Login</span>
                    </a>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSuperadmin): ?>
                    <div class="separator my-2"></div>
                    <div class="menu-item px-5">
                        <a href="<?php echo e(route('settings.index')); ?>" class="menu-link px-5">
                            <span class="menu-icon"><i class="ki-outline ki-setting-2 fs-4"></i></span>
                            <span class="menu-title">Pengaturan Aplikasi</span>
                        </a>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <div class="separator my-2"></div>

                <div class="menu-item px-5">
                    <form method="POST" action="<?php echo e(route('logout')); ?>" id="logout-form">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="menu-link px-5 w-100 text-start bg-transparent border-0"
                            data-confirm="Anda akan keluar dari sesi ini. Lanjutkan?"
                            data-confirm-title="Keluar dari akun?"
                            data-confirm-ok="Ya, keluar"
                            data-confirm-cancel="Batal"
                            data-confirm-icon="warning">
                            <span class="menu-icon"><i class="ki-outline ki-exit-right fs-4"></i></span>
                            <span class="menu-title">Keluar</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <!--end::Account-->
</div>
<?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/backend/layout/navbar.blade.php ENDPATH**/ ?>