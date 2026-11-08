
<nav class="menu menu-rounded menu-column menu-lg-row menu-root-here-bg-desktop menu-active-bg menu-state-primary menu-title-gray-800 menu-arrow-gray-500 align-items-stretch fw-semibold fs-6 py-1"
    data-kt-menu="true" aria-label="Navigasi utama">

    <div class="menu-item me-lg-2 <?php echo e(request()->routeIs('dashboard') ? 'here show menu-here-bg' : ''); ?>">
        <a class="menu-link py-3" href="<?php echo e(route('dashboard')); ?>">
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
        <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start"
            class="menu-item menu-lg-down-accordion me-lg-2 <?php echo e(request()->routeIs('saas.*') ? 'here show menu-here-bg' : ''); ?>">
            <span class="menu-link py-3">
                <span class="menu-icon"><i class="ki-outline ki-crown fs-3"></i></span>
                <span class="menu-title">Langganan</span>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($saasMenunggu > 0): ?>
                    <span class="badge badge-warning ms-2"><?php echo e($saasMenunggu); ?></span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <span class="menu-arrow d-lg-none"></span>
            </span>
            <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown py-4 w-225px">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                    ['saas.tim.*', 'saas.tim.index', 'Tim', 'ki-people'],
                    ['saas.bayar.*', 'saas.bayar.index', 'Pembayaran', 'ki-wallet'],
                    ['saas.paket.*', 'saas.paket.index', 'Paket', 'ki-price-tag'],
                    ['saas.rekening.*', 'saas.rekening.index', 'Rekening', 'ki-bank'],
                    ['saas.kontak.*', 'saas.kontak.index', 'Kontak WhatsApp', 'ki-whatsapp'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$pola, $rute, $judul, $ikon]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="menu-item">
                        <a class="menu-link <?php echo e(request()->routeIs($pola) ? 'active' : ''); ?>" href="<?php echo e(route($rute)); ?>">
                            <span class="menu-icon"><i class="ki-outline <?php echo e($ikon); ?> fs-4"></i></span>
                            <span class="menu-title"><?php echo e($judul); ?></span>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($rute === 'saas.bayar.index' && $saasMenunggu > 0): ?>
                                <span class="badge badge-light-warning ms-auto"><?php echo e($saasMenunggu); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </a>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>

        <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start"
            class="menu-item menu-lg-down-accordion me-lg-2 <?php echo e(request()->is('admin/users*') || request()->is('admin/roles*') ? 'here show menu-here-bg' : ''); ?>">
            <span class="menu-link py-3">
                <span class="menu-icon">
                    <i class="ki-duotone ki-people fs-3">
                        <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                        <span class="path4"></span><span class="path5"></span>
                    </i>
                </span>
                <span class="menu-title">User Management</span>
                <span class="menu-arrow d-lg-none"></span>
            </span>
            <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown py-4 w-225px">
                <div class="menu-item">
                    <a class="menu-link <?php echo e(request()->is('admin/users*') ? 'active' : ''); ?>" href="<?php echo e(route('users.index')); ?>">
                        <span class="menu-icon"><i class="ki-duotone ki-user fs-4"><span class="path1"></span><span class="path2"></span></i></span>
                        <span class="menu-title">Users</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a class="menu-link <?php echo e(request()->is('admin/roles*') ? 'active' : ''); ?>" href="<?php echo e(route('roles.index')); ?>">
                        <span class="menu-icon"><i class="ki-duotone ki-shield-tick fs-4"><span class="path1"></span><span class="path2"></span></i></span>
                        <span class="menu-title">Roles &amp; Permissions</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="menu-item me-lg-2 <?php echo e(request()->routeIs('settings.*') ? 'here show menu-here-bg' : ''); ?>">
            <a class="menu-link py-3" href="<?php echo e(route('settings.index')); ?>">
                <span class="menu-icon">
                    <i class="ki-duotone ki-setting-2 fs-3"><span class="path1"></span><span class="path2"></span></i>
                </span>
                <span class="menu-title">Settings</span>
            </a>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="menu-item me-lg-2 <?php echo e(request()->is('admin/log-activity*') ? 'here show menu-here-bg' : ''); ?>">
        <a class="menu-link py-3" href="<?php echo e(route('log-activity.index')); ?>">
            <span class="menu-icon">
                <i class="ki-duotone ki-notepad fs-3">
                    <span class="path1"></span><span class="path2"></span><span class="path3"></span>
                    <span class="path4"></span><span class="path5"></span>
                </i>
            </span>
            <span class="menu-title">Log Aktivitas</span>
        </a>
    </div>

</nav>
<?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/backend/layout/menu.blade.php ENDPATH**/ ?>