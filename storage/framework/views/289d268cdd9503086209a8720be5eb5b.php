<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $user = auth()->user();

        // Superadmins get the fleet-wide cards; everyone else sees their own
        // numbers, so the tile set differs by role.
        $tiles = $isSuperadmin
            ? [
                ['label' => 'Total Pengguna', 'value' => $stats['users'], 'icon' => 'ki-people', 'color' => 'primary', 'hint' => $stats['active_users'] . ' aktif'],
                ['label' => 'Role & Hak Akses', 'value' => $stats['roles'], 'icon' => 'ki-shield-tick', 'color' => 'info', 'hint' => 'Terdefinisi'],
                ['label' => 'Sedang Online', 'value' => $stats['online'] ?? '—', 'icon' => 'ki-pulse', 'color' => 'success', 'hint' => '5 menit terakhir'],
                ['label' => 'Aktivitas Hari Ini', 'value' => $stats['activity_today'], 'icon' => 'ki-notepad', 'color' => 'warning', 'hint' => $stats['activity_week'] . ' minggu ini'],
            ]
            : [
                ['label' => 'Aktivitas Hari Ini', 'value' => $stats['activity_today'], 'icon' => 'ki-notepad', 'color' => 'primary', 'hint' => 'Tercatat sistem'],
                ['label' => 'Aktivitas 7 Hari', 'value' => $stats['activity_week'], 'icon' => 'ki-chart-simple', 'color' => 'info', 'hint' => 'Minggu berjalan'],
                ['label' => 'Login Terakhir', 'value' => $stats['last_login'], 'icon' => 'ki-entrance-right', 'color' => 'success', 'hint' => 'Sesi Anda'],
                ['label' => 'IP Terakhir', 'value' => $stats['last_ip'], 'icon' => 'ki-technology-2', 'color' => 'warning', 'hint' => 'Perangkat Anda'],
            ];

        $breakdownTotal = max(1, collect($breakdown)->sum('count'));
    ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($saas)): ?>
        <div class="row g-4 mb-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                ['Tim aktif', $saas['aktif'], 'success', 'ki-people', route('saas.tim.index', ['status' => 'aktif'])],
                ['Menunggu bayar', $saas['menunggu'], 'warning', 'ki-time', route('saas.bayar.index', ['status' => 'menunggu'])],
                ['Masa habis', $saas['habis'], 'danger', 'ki-calendar-remove', route('saas.tim.index', ['status' => 'habis'])],
                ['Pendapatan bulan ini', 'Rp '.number_format($saas['pendapatan'], 0, ',', '.'), 'primary', 'ki-wallet', route('saas.bayar.index', ['status' => 'diterima'])],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$label, $nilai, $warna, $ikon, $url]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="col-6 col-xl-3">
                    <a href="<?php echo e($url); ?>" class="card card-flush h-100 hover-elevate-up">
                        <div class="card-body d-flex align-items-center gap-4 py-5">
                            <span class="symbol symbol-45px"><span class="symbol-label bg-light-<?php echo e($warna); ?>"><i class="ki-outline <?php echo e($ikon); ?> fs-2 text-<?php echo e($warna); ?>"></i></span></span>
                            <div class="min-w-0">
                                <div class="fs-2 fw-bold text-gray-900 text-truncate"><?php echo e($nilai); ?></div>
                                <div class="text-muted fs-7"><?php echo e($label); ?></div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <!--begin::Page header-->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-6">
        <div class="min-w-0">
            <h1 class="fs-2 fw-bold text-gray-900 mb-1">
                Halo, <?php echo e(Str::before($user->name, ' ')); ?> 👋
            </h1>
            <p class="text-muted fs-6 mb-0">
                <?php echo e(now()->translatedFormat('l, d F Y')); ?> &middot; Ringkasan <?php echo e($brand['name']); ?>

            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="<?php echo e($isSuperadmin ? route('log-activity.index') : route('my-activity.index')); ?>"
                class="btn btn-sm btn-light-primary">
                <i class="ki-outline ki-notepad fs-5 me-1"></i> Log Aktivitas
            </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSuperadmin): ?>
                <a href="<?php echo e(route('users.index')); ?>" class="btn btn-sm btn-primary">
                    <i class="ki-outline ki-people fs-5 me-1"></i> Kelola Pengguna
                </a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
    <!--end::Page header-->

    <!--begin::Stat tiles-->
    <div class="row g-4 g-xl-5 mb-5 mb-xl-6">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tiles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tile): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="col-6 col-xl-3">
                <div class="card card-flush app-stat-card shadow-sm">
                    <div class="card-body d-flex flex-column gap-3 p-4 p-xl-6">
                        <div class="d-flex align-items-start justify-content-between gap-2">
                            <span class="app-stat-card__icon bg-light-<?php echo e($tile['color']); ?>">
                                <i class="ki-duotone <?php echo e($tile['icon']); ?> fs-2 text-<?php echo e($tile['color']); ?>">
                                    <span class="path1"></span><span class="path2"></span>
                                    <span class="path3"></span><span class="path4"></span>
                                    <span class="path5"></span>
                                </i>
                            </span>
                        </div>
                        <div class="min-w-0">
                            <div class="fs-2 fs-xl-2hx fw-bold text-gray-900 text-truncate lh-1 mb-1"
                                title="<?php echo e($tile['value']); ?>"><?php echo e($tile['value']); ?></div>
                            <div class="fs-7 fw-semibold text-gray-600"><?php echo e($tile['label']); ?></div>
                            <div class="fs-8 text-muted"><?php echo e($tile['hint']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
    <!--end::Stat tiles-->

    <div class="row g-4 g-xl-5">

        <!--begin::Activity trend-->
        <div class="col-12 col-xxl-8">
            <div class="card card-flush shadow-sm h-100">
                <div class="card-header pt-6 border-0 flex-wrap gap-2">
                    <div class="card-title flex-column align-items-start">
                        <h3 class="fw-bold fs-4 mb-1">Tren Aktivitas</h3>
                        <span class="text-muted fs-7 fw-normal">
                            <?php echo e($isSuperadmin ? 'Seluruh pengguna' : 'Aktivitas Anda'); ?> &middot; 14 hari terakhir
                        </span>
                    </div>
                </div>
                <div class="card-body pt-2 pb-4">
                    <div id="dashboard-trend-chart" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>
        <!--end::Activity trend-->

        <!--begin::Breakdown-->
        <div class="col-12 col-xxl-4">
            <div class="card card-flush shadow-sm h-100">
                <div class="card-header pt-6 border-0">
                    <div class="card-title flex-column align-items-start">
                        <h3 class="fw-bold fs-4 mb-1">Jenis Aktivitas</h3>
                        <span class="text-muted fs-7 fw-normal">Distribusi 14 hari terakhir</span>
                    </div>
                </div>
                <div class="card-body pt-2 d-flex flex-column gap-5">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $breakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php $percent = round($item['count'] / $breakdownTotal * 100); ?>
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2 gap-2">
                                <span class="d-flex align-items-center gap-2 min-w-0">
                                    <i class="ki-outline <?php echo e($item['icon']); ?> fs-5 text-<?php echo e($item['color']); ?>"></i>
                                    <span class="fs-7 fw-semibold text-gray-700 text-truncate"><?php echo e($item['label']); ?></span>
                                </span>
                                <span class="fs-7 fw-bold text-gray-900"><?php echo e($item['count']); ?></span>
                            </div>
                            <div class="progress h-6px bg-light">
                                <div class="progress-bar bg-<?php echo e($item['color']); ?>" role="progressbar"
                                    style="width: <?php echo e($percent); ?>%" aria-valuenow="<?php echo e($percent); ?>"
                                    aria-valuemin="0" aria-valuemax="100"
                                    aria-label="<?php echo e($item['label']); ?>"></div>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        </div>
        <!--end::Breakdown-->

        <!--begin::Recent activity-->
        <div class="col-12 <?php echo e($isSuperadmin ? 'col-xxl-8' : ''); ?>">
            <div class="card card-flush shadow-sm h-100">
                <div class="card-header pt-6 border-0 flex-wrap gap-2">
                    <div class="card-title flex-column align-items-start">
                        <h3 class="fw-bold fs-4 mb-1">Aktivitas Terbaru</h3>
                        <span class="text-muted fs-7 fw-normal">
                            <?php echo e($isSuperadmin ? 'Semua pengguna' : 'Hanya aktivitas Anda'); ?>

                        </span>
                    </div>
                    <div class="card-toolbar">
                        <a href="<?php echo e($isSuperadmin ? route('log-activity.index') : route('my-activity.index')); ?>"
                            class="btn btn-sm btn-light">Lihat semua</a>
                    </div>
                </div>
                <div class="card-body pt-2 pb-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $recent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="d-flex align-items-start gap-3 py-3 <?php echo e(! $loop->last ? 'border-bottom border-gray-200' : ''); ?>">
                            <?php if (isset($component)) { $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.avatar','data' => ['user' => $activity->causer,'size' => 38]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($activity->causer),'size' => 38]); ?>
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
                            <div class="flex-grow-1 min-w-0">
                                <div class="fs-7 fw-semibold text-gray-900"><?php echo e($activity->description); ?></div>
                                <div class="fs-8 text-muted">
                                    <?php echo e($activity->causer->name ?? 'Sistem'); ?>

                                    &middot; <?php echo e($activity->created_at->diffForHumans()); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($activity->properties['ip'])): ?>
                                        &middot; <?php echo e($activity->properties['ip']); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                            <span class="badge badge-light-<?php echo e($activity->log_name ? 'primary' : 'secondary'); ?> fs-9 text-nowrap d-none d-sm-inline">
                                <?php echo e(Str::limit($activity->log_name ?: 'sistem', 18)); ?>

                            </span>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <div class="text-center text-muted py-10">
                            <i class="ki-duotone ki-file-deleted fs-3x text-gray-400 d-block mb-3">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                            Belum ada aktivitas tercatat.
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
        <!--end::Recent activity-->

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSuperadmin): ?>
            <!--begin::Top users-->
            <div class="col-12 col-xxl-4">
                <div class="card card-flush shadow-sm h-100">
                    <div class="card-header pt-6 border-0">
                        <div class="card-title flex-column align-items-start">
                            <h3 class="fw-bold fs-4 mb-1">Pengguna Teraktif</h3>
                            <span class="text-muted fs-7 fw-normal">14 hari terakhir</span>
                        </div>
                    </div>
                    <div class="card-body pt-2 pb-4">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $topUsers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="d-flex align-items-center gap-3 py-3 <?php echo e(! $loop->last ? 'border-bottom border-gray-200' : ''); ?>">
                                <?php if (isset($component)) { $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.avatar','data' => ['user' => $row['user'],'size' => 38]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row['user']),'size' => 38]); ?>
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
                                <div class="flex-grow-1 min-w-0">
                                    <div class="fs-7 fw-semibold text-gray-900 text-truncate"><?php echo e($row['user']->name); ?></div>
                                    <div class="fs-8 text-muted"><?php echo e($row['user']->role_label); ?></div>
                                </div>
                                <span class="badge badge-light-primary fw-bold"><?php echo e($row['total']); ?></span>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <div class="text-center text-muted py-10">Belum ada data.</div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
            <!--end::Top users-->
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php $__env->startPush('scripts'); ?>
        
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var host = document.getElementById('dashboard-trend-chart');

                if (!host || typeof ApexCharts === 'undefined') {
                    return;
                }

                // Read theme tokens from Metronic so the chart follows light/dark.
                var css = getComputedStyle(document.documentElement);
                var primary = css.getPropertyValue('--bs-primary').trim() || '#3E97FF';
                var gridColor = css.getPropertyValue('--bs-gray-200').trim() || '#eff2f5';
                var labelColor = css.getPropertyValue('--bs-gray-500').trim() || '#99a1b7';

                var chart = new ApexCharts(host, {
                    chart: {
                        type: 'area',
                        height: 300,
                        toolbar: { show: false },
                        fontFamily: 'inherit',
                        animations: { enabled: !window.matchMedia('(prefers-reduced-motion: reduce)').matches }
                    },
                    series: [{ name: 'Aktivitas', data: <?php echo json_encode($trend['data'], 15, 512) ?> }],
                    xaxis: {
                        categories: <?php echo json_encode($trend['labels'], 15, 512) ?>,
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: { style: { colors: labelColor, fontSize: '12px' }, rotate: 0, hideOverlappingLabels: true }
                    },
                    yaxis: {
                        min: 0,
                        forceNiceScale: true,
                        labels: { style: { colors: labelColor, fontSize: '12px' } }
                    },
                    stroke: { curve: 'smooth', width: 3 },
                    colors: [primary],
                    fill: {
                        type: 'gradient',
                        gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02, stops: [0, 90, 100] }
                    },
                    dataLabels: { enabled: false },
                    grid: { borderColor: gridColor, strokeDashArray: 4, xaxis: { lines: { show: false } } },
                    tooltip: { theme: document.documentElement.getAttribute('data-bs-theme') || 'light' },
                    markers: { size: 0, hover: { size: 6 } }
                });

                chart.render();
            });
        </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/backend/dashboard/index.blade.php ENDPATH**/ ?>