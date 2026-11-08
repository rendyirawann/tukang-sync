
<aside id="kt_app_sidebar" class="d-flex flex-column" aria-label="Navigasi utama" aria-hidden="true">

    <!--begin::Header-->
    <div class="d-flex align-items-center justify-content-between gap-2 px-5 pt-5 pb-4">
        <a href="<?php echo e(route('dashboard')); ?>" class="app-brand">
            <img src="<?php echo e($brand['logo_url']); ?>" alt="" class="app-brand__mark" width="34" height="34" />
            <span class="min-w-0">
                <span class="app-brand__name d-block"><?php echo e($brand['name']); ?></span>
                <span class="app-brand__tagline"><?php echo e($brand['tagline']); ?></span>
            </span>
        </a>
        <button type="button" class="btn btn-icon btn-sm btn-active-color-primary" data-app-sidebar-toggle
            aria-label="Tutup menu navigasi">
            <i class="ki-outline ki-cross fs-2"></i>
        </button>
    </div>
    <!--end::Header-->

    <div class="separator mx-5 mb-3"></div>

    <!--begin::Menu-->
    <nav class="px-4 flex-column-fluid">
        <div class="menu menu-column menu-rounded menu-sub-indention menu-active-bg fw-semibold fs-6" data-kt-menu="true">

            <div class="menu-item">
                <a class="menu-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>" href="<?php echo e(route('dashboard')); ?>">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-element-11 fs-3">
                            <span class="path1"></span><span class="path2"></span>
                            <span class="path3"></span><span class="path4"></span>
                        </i>
                    </span>
                    <span class="menu-title">Dashboard</span>
                </a>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin|superadmin')): ?>
                <?php ($saasMenunggu = \App\Models\TeamPayment::where('status', 'menunggu')->count()); ?>
                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">Langganan</span>
                    </div>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                    ['saas.tim.*', 'saas.tim.index', 'Tim', 'ki-people'],
                    ['saas.bayar.*', 'saas.bayar.index', 'Pembayaran', 'ki-wallet'],
                    ['saas.paket.*', 'saas.paket.index', 'Paket', 'ki-price-tag'],
                    ['saas.rekening.*', 'saas.rekening.index', 'Rekening', 'ki-bank'],
                    ['saas.kontak.*', 'saas.kontak.index', 'Kontak WhatsApp', 'ki-whatsapp'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$pola, $rute, $judul, $ikon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="menu-item">
                        <a class="menu-link <?php echo e(request()->routeIs($pola) ? 'active' : ''); ?>" href="<?php echo e(route($rute)); ?>">
                            <span class="menu-icon"><i class="ki-outline <?php echo e($ikon); ?> fs-3"></i></span>
                            <span class="menu-title"><?php echo e($judul); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rute === 'saas.bayar.index' && $saasMenunggu > 0): ?>
                                <span class="menu-badge"><span class="badge badge-warning"><?php echo e($saasMenunggu); ?></span></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </a>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                <div class="menu-item pt-5">
                    <div class="menu-content">
                        <span class="menu-heading fw-bold text-uppercase fs-7">Manajemen</span>
                    </div>
                </div>

                <div data-kt-menu-trigger="click"
                    class="menu-item menu-accordion <?php echo e(request()->is('admin/users*') || request()->is('admin/roles*') ? 'here show' : ''); ?>">
                    <span class="menu-link">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-people fs-3">
                                <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                                <span class="path4"></span><span class="path5"></span>
                            </i>
                        </span>
                        <span class="menu-title">User Management</span>
                        <span class="menu-arrow"></span>
                    </span>
                    <div class="menu-sub menu-sub-accordion <?php echo e(request()->is('admin/users*') || request()->is('admin/roles*') ? 'show' : ''); ?>">
                        <div class="menu-item">
                            <a class="menu-link <?php echo e(request()->is('admin/users*') ? 'active' : ''); ?>" href="<?php echo e(route('users.index')); ?>">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Users</span>
                            </a>
                        </div>
                        <div class="menu-item">
                            <a class="menu-link <?php echo e(request()->is('admin/roles*') ? 'active' : ''); ?>" href="<?php echo e(route('roles.index')); ?>">
                                <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                                <span class="menu-title">Roles &amp; Permissions</span>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="menu-item pt-5">
                <div class="menu-content">
                    <span class="menu-heading fw-bold text-uppercase fs-7">Akun Saya</span>
                </div>
            </div>

            <div data-kt-menu-trigger="click"
                class="menu-item menu-accordion <?php echo e(request()->is('admin/my-*') || request()->is('admin/mmy-*') ? 'here show' : ''); ?>">
                <span class="menu-link">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-profile-circle fs-3">
                            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                        </i>
                    </span>
                    <span class="menu-title">My Account</span>
                    <span class="menu-arrow"></span>
                </span>
                <div class="menu-sub menu-sub-accordion <?php echo e(request()->is('admin/my-*') || request()->is('admin/mmy-*') ? 'show' : ''); ?>">
                    <div class="menu-item">
                        <a class="menu-link <?php echo e(request()->routeIs('account.index') ? 'active' : ''); ?>" href="<?php echo e(route('account.index')); ?>">
                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                            <span class="menu-title">Overview</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link <?php echo e(request()->is('admin/my-security*') ? 'active' : ''); ?>" href="<?php echo e(route('my-security.index')); ?>">
                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                            <span class="menu-title">Security</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link <?php echo e(request()->is('admin/my-activity*') ? 'active' : ''); ?>" href="<?php echo e(route('my-activity.index')); ?>">
                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                            <span class="menu-title">Activity</span>
                        </a>
                    </div>
                    <div class="menu-item">
                        <a class="menu-link <?php echo e(request()->is('admin/mmy-login-session*') ? 'active' : ''); ?>" href="<?php echo e(route('my-login-session.index')); ?>">
                            <span class="menu-bullet"><span class="bullet bullet-dot"></span></span>
                            <span class="menu-title">Login Sessions</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="menu-item pt-5">
                <div class="menu-content">
                    <span class="menu-heading fw-bold text-uppercase fs-7">Sistem</span>
                </div>
            </div>

            
            <div class="menu-item">
                <a class="menu-link <?php echo e(request()->is('admin/log-activity*') ? 'active' : ''); ?>" href="<?php echo e(route('log-activity.index')); ?>">
                    <span class="menu-icon">
                        <i class="ki-duotone ki-notepad fs-3">
                            <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                            <span class="path4"></span><span class="path5"></span>
                        </i>
                    </span>
                    <span class="menu-title">Log Aktivitas</span>
                </a>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (\Illuminate\Support\Facades\Blade::check('role', 'Superadmin|superadmin')): ?>

                <div class="menu-item">
                    <a class="menu-link <?php echo e(request()->routeIs('settings.*') ? 'active' : ''); ?>" href="<?php echo e(route('settings.index')); ?>">
                        <span class="menu-icon">
                            <i class="ki-duotone ki-setting-2 fs-3"><span class="path1"></span><span class="path2"></span></i>
                        </span>
                        <span class="menu-title">Settings</span>
                    </a>
                </div>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        </div>
    </nav>
    <!--end::Menu-->

    <!--begin::Footer-->
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
        <div class="px-5 py-5 mt-auto">
            <div class="separator mb-4"></div>
            <div class="d-flex align-items-center gap-3 min-w-0">
                <?php if (isset($component)) { $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.avatar','data' => ['size' => 36]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 36]); ?>
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
                    <span class="fw-bold fs-7 text-gray-800 text-truncate"><?php echo e(auth()->user()->name); ?></span>
                    <span class="text-muted fs-8"><?php echo e(auth()->user()->role_label); ?></span>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <!--end::Footer-->
</aside>
<?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/backend/layout/sidebar.blade.php ENDPATH**/ ?>