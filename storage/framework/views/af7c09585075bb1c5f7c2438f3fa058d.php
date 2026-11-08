<?php $__env->startSection('title', 'Daftarkan Tim'); ?>
<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('backend.saas._header', ['judul' => 'Daftarkan Tim', 'sub' => 'Tim baru'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <form method="POST" action="<?php echo e(route('saas.tim.store')); ?>" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <div class="row g-6">
            <div class="col-lg-7">
                <div class="card card-flush mb-6">
                    <div class="card-header"><h3 class="card-title">Data tim</h3></div>
                    <div class="card-body pt-0">
                        <div class="mb-4">
                            <label class="form-label required">Nama tim / usaha</label>
                            <input name="nama_tim" value="<?php echo e(old('nama_tim')); ?>" class="form-control" required />
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Kota</label>
                                <input name="kota" value="<?php echo e(old('kota')); ?>" class="form-control" />
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Alamat</label>
                                <input name="alamat" value="<?php echo e(old('alamat')); ?>" class="form-control" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-flush mb-6">
                    <div class="card-header"><h3 class="card-title">Akun penanggung jawab</h3></div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label required">Nama</label>
                                <input name="nama" value="<?php echo e(old('nama')); ?>" class="form-control" required />
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label required">Peran</label>
                                <select name="peran" class="form-select">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = \App\Enums\Peran::pendaftar(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <option value="<?php echo e($p->value); ?>" <?php if(old('peran') === $p->value): echo 'selected'; endif; ?>><?php echo e($p->label()); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label required">Email</label>
                                <input type="email" name="email" value="<?php echo e(old('email')); ?>" class="form-control" required />
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label required">No. WhatsApp</label>
                                <input name="no_wa" value="<?php echo e(old('no_wa')); ?>" class="form-control" required />
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label required">Password</label>
                                <input type="text" name="password" class="form-control" autocomplete="new-password" required />
                                <div class="form-text">Minimal 8 karakter, huruf dan angka. Sampaikan ke pelanggan.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card card-flush mb-6">
                    <div class="card-header"><h3 class="card-title">Paket</h3></div>
                    <div class="card-body pt-0">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <label class="d-flex align-items-center gap-4 border rounded p-4 mb-3" style="cursor:pointer">
                                <input type="radio" name="plan_id" value="<?php echo e($plan->id); ?>" class="form-check-input"
                                    <?php if(old('plan_id', $plans->first()->id) == $plan->id): echo 'checked'; endif; ?> />
                                <span class="flex-grow-1">
                                    <span class="fw-bold d-block"><?php echo e($plan->nama); ?></span>
                                    <span class="text-muted fs-7"><?php echo e($plan->periodeLabel()); ?> · maks <?php echo e($plan->max_users); ?> akun</span>
                                </span>
                                <span class="fw-bold text-primary"><?php echo e($plan->hargaLabel()); ?></span>
                            </label>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>

                <div class="card card-flush mb-6">
                    <div class="card-header"><h3 class="card-title">Pembayaran</h3></div>
                    <div class="card-body pt-0">
                        <label class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="langsung_aktif" value="1" <?php if(old('langsung_aktif', true)): echo 'checked'; endif; ?> />
                            <span class="form-check-label">Pembayaran sudah diterima, langsung aktifkan</span>
                        </label>
                        <div class="mb-4">
                            <label class="form-label">Rekening tujuan</label>
                            <select name="bank_account_id" class="form-select">
                                <option value="">-</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $rekening; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e($r->id); ?>" <?php if(old('bank_account_id') == $r->id): echo 'selected'; endif; ?>><?php echo e($r->bank); ?> <?php echo e($r->nomor); ?> a.n. <?php echo e($r->atas_nama); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Catatan</label>
                            <input name="catatan" value="<?php echo e(old('catatan')); ?>" class="form-control" />
                        </div>
                        <?php echo $__env->make('backend.saas._bukti_input', ['id' => 'buat'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                </div>

                <button class="btn btn-primary w-100">Simpan &amp; buat kode tim</button>
            </div>
        </div>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/backend/saas/tim/create.blade.php ENDPATH**/ ?>