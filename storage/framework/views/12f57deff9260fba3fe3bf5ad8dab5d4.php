<?php $__env->startSection('title', 'Daftar Tim'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-10" style="max-width:1000px">
    <h1 class="fw-bold mb-2">Daftarkan tim Anda</h1>
    <p class="text-muted mb-8">Setelah mengisi formulir, Anda akan diarahkan ke WhatsApp admin untuk konfirmasi pembayaran. Akun aktif setelah pembayaran dikonfirmasi.</p>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
        <div class="alert alert-danger"><ul class="mb-0"><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?><li><?php echo e($e); ?></li><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?></ul></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <form method="POST" action="<?php echo e(route('daftar.store')); ?>">
        <?php echo csrf_field(); ?>
        <div style="position:absolute;left:-9999px" aria-hidden="true">
            <label>Situs web <input type="text" name="situs_web" tabindex="-1" autocomplete="off"></label>
        </div>
        <div class="row g-8">
            <div class="col-lg-7">
                <div class="card shadow-sm mb-6"><div class="card-body p-8">
                    <h4 class="fw-bold mb-5">Data tim</h4>
                    <div class="mb-4"><label class="form-label required">Nama tim / usaha</label>
                        <input name="nama_tim" value="<?php echo e(old('nama_tim')); ?>" class="form-control" required maxlength="120" placeholder="Mis. CV Bangun Jaya"></div>
                    <div class="row g-4">
                        <div class="col-md-6"><label class="form-label">Kota</label><input name="kota" value="<?php echo e(old('kota')); ?>" class="form-control" maxlength="80"></div>
                        <div class="col-md-6"><label class="form-label">Alamat</label><input name="alamat" value="<?php echo e(old('alamat')); ?>" class="form-control" maxlength="255"></div>
                    </div>
                </div></div>

                <div class="card shadow-sm"><div class="card-body p-8">
                    <h4 class="fw-bold mb-5">Akun penanggung jawab</h4>
                    <div class="mb-4"><label class="form-label required">Peran Anda</label>
                        <div class="d-flex gap-6">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $peran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <label class="form-check form-check-custom">
                                    <input class="form-check-input" type="radio" name="peran" value="<?php echo e($p->value); ?>" <?php echo e(old('peran', 'owner') === $p->value ? 'checked' : ''); ?>>
                                    <span class="form-check-label"><?php echo e($p->label()); ?></span>
                                </label>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </div>
                    <div class="mb-4"><label class="form-label required">Nama lengkap</label><input name="nama" value="<?php echo e(old('nama')); ?>" class="form-control" required maxlength="120"></div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6"><label class="form-label required">Email</label><input type="email" name="email" value="<?php echo e(old('email')); ?>" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label required">No. WhatsApp</label><input name="no_wa" value="<?php echo e(old('no_wa')); ?>" class="form-control" required placeholder="08xxxxxxxxxx"></div>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6"><label class="form-label required">Password</label><input type="password" name="password" class="form-control" required autocomplete="new-password"></div>
                        <div class="col-md-6"><label class="form-label required">Ulangi password</label><input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password"></div>
                    </div>
                    <div class="form-text">Minimal 8 karakter, berisi huruf dan angka.</div>
                </div></div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm mb-6"><div class="card-body p-8">
                    <h4 class="fw-bold mb-5">Paket</h4>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <label class="d-flex align-items-center border rounded p-4 mb-3 gap-3" style="cursor:pointer">
                            <input class="form-check-input" type="radio" name="plan_id" value="<?php echo e($plan->id); ?>" <?php echo e((int) old('plan_id', $pilih?->id) === $plan->id ? 'checked' : ''); ?>>
                            <span class="flex-grow-1"><span class="fw-bold d-block"><?php echo e($plan->nama); ?></span><span class="text-muted fs-7"><?php echo e($plan->periodeLabel()); ?></span></span>
                            <span class="fw-bolder" style="color:var(--tp-primary)"><?php echo e($plan->hargaLabel()); ?></span>
                        </label>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div></div>

                <div class="card shadow-sm mb-6"><div class="card-body p-8">
                    <h4 class="fw-bold mb-4">Transfer ke rekening</h4>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $rekening; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="border rounded p-4 mb-3">
                            <div class="fw-bold"><?php echo e($r->bank); ?></div>
                            <div class="fs-4 fw-bolder font-monospace"><?php echo e($r->nomor); ?></div>
                            <div class="text-muted fs-7">a.n. <?php echo e($r->atas_nama); ?></div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <p class="text-muted">Rekening akan diinformasikan admin via WhatsApp.</p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <p class="text-muted fs-7 mb-0">Kirim bukti transfer ke WhatsApp admin setelah mendaftar, sebutkan kode tim Anda.</p>
                </div></div>

                <button class="btn btn-gold btn-lg w-100">Daftar &amp; konfirmasi via WhatsApp</button>
            </div>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('tim.layout.polos', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/publik/daftar.blade.php ENDPATH**/ ?>