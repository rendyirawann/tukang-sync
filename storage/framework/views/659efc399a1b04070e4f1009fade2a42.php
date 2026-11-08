<?php $__env->startSection('title', 'Catatan Proyek & RAB Konstruksi'); ?>

<?php $__env->startSection('content'); ?>
<section class="hero py-15">
    <div class="container">
        <div class="row align-items-center g-10">
            <div class="col-lg-7">
                <span class="badge text-gold mb-4" style="background:rgba(201,164,76,.15)">Untuk kontraktor, mandor & pemilik proyek</span>
                <h1 class="text-white fw-bolder mb-5" style="font-size:2.6rem;line-height:1.2">Catat proyek, susun RAB, dan pantau biaya konstruksi dari HP maupun web.</h1>
                <p class="fs-5 mb-8" style="color:#c8d3e6">Hitung kebutuhan material per bangunan, lantai, dan ruangan secara otomatis. Pantau progres lapangan, belanja, dan bagi hasil dalam satu tim.</p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="<?php echo e(route('daftar')); ?>" class="btn btn-gold btn-lg">Daftar sekarang</a>
                    <a href="#paket" class="btn btn-lg btn-outline btn-outline-light text-white">Lihat paket</a>
                </div>
            </div>
            <div class="col-lg-5" id="masuk">
                <div class="card shadow-sm">
                    <div class="card-body p-8">
                        <h3 class="fw-bold mb-2">Sudah punya tim?</h3>
                        <p class="text-muted mb-5">Masukkan kode tim 4 karakter yang Anda terima saat mendaftar.</p>
                        <form id="formMasuk" class="d-flex gap-2">
                            <input type="text" name="kode" maxlength="6" class="form-control form-control-lg text-uppercase fw-bold" placeholder="Mis. K7QX" style="letter-spacing:.2em" required>
                            <button class="btn btn-primary btn-lg">Masuk</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container py-15">
    <h2 class="fw-bold text-center mb-10">Semua yang dibutuhkan untuk mencatat proyek</h2>
    <div class="row g-6">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
            ['ki-home-2', 'RAB per bangunan, lantai & ruangan', 'Susun anggaran rinci: pondasi, kolom, dinding, pintu, jendela, atap, sampai titik lampu.'],
            ['ki-cube-2', 'Kebutuhan material otomatis', 'Pilih analisa harga satuan, volume dihitung dari dimensi — jumlah semen, bata, besi keluar sendiri.'],
            ['ki-chart-line-up', 'Progres & log harian', 'Mandor melaporkan progres dan catatan lapangan setiap hari langsung dari HP.'],
            ['ki-wallet', 'Arus kas & bagi hasil', 'Catat belanja, upah, dan termin. Bandingkan rencana dengan realisasi.'],
            ['ki-phone', 'Aplikasi Android + web', 'Satu akun untuk aplikasi HP dan panel web tim.'],
            ['ki-people', 'Hingga 4 akun per tim', 'Owner, kontraktor, mandor, anggota, atau viewer untuk klien.'],
        ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$ikon, $judul, $isi]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body p-7">
                        <i class="ki-duotone <?php echo e($ikon); ?> fs-2x text-gold mb-4"><span class="path1"></span><span class="path2"></span><span class="path3"></span></i>
                        <h4 class="fw-bold"><?php echo e($judul); ?></h4>
                        <p class="text-muted mb-0"><?php echo e($isi); ?></p>
                    </div>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
</section>

<section class="container pb-15" id="paket">
    <h2 class="fw-bold text-center mb-10">Pilih paket</h2>
    <div class="row g-6 justify-content-center">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <div class="col-md-6 col-lg-4">
                <div class="card card-plan h-100 shadow-sm <?php echo e($plan->isLifetime() ? 'populer' : ''); ?>">
                    <div class="card-body p-8 d-flex flex-column">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan->isLifetime()): ?><span class="badge text-gold align-self-start mb-3" style="background:rgba(201,164,76,.15)">Paling hemat</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <h3 class="fw-bold"><?php echo e($plan->nama); ?></h3>
                        <div class="fs-2hx fw-bolder" style="color:var(--tp-primary)"><?php echo e($plan->hargaLabel()); ?></div>
                        <div class="text-muted mb-5"><?php echo e($plan->periodeLabel()); ?></div>
                        <ul class="list-unstyled mb-8 flex-grow-1">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $plan->fitur ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <li class="mb-2">✓ <?php echo e($f); ?></li>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <li class="mb-2">✓ Maksimal <?php echo e($plan->max_users); ?> akun</li>
                        </ul>
                        <a href="<?php echo e(route('daftar', ['paket' => $plan->kode])); ?>" class="btn btn-primary w-100">Daftar paket <?php echo e($plan->nama); ?></a>
                    </div>
                </div>
            </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.getElementById('formMasuk').addEventListener('submit', function (e) {
        e.preventDefault();
        var k = this.kode.value.trim().toUpperCase();
        if (k) { window.location.href = <?php echo json_encode(url('/'), 15, 512) ?> + '/' + encodeURIComponent(k) + '/login'; }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('tim.layout.polos', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/publik/beranda.blade.php ENDPATH**/ ?>