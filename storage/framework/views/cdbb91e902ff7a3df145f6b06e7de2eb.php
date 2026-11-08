<?php $__env->startSection('title', 'Pembayaran ' . $bayar->kode); ?>
<?php $__env->startSection('content'); ?>
    <?php ($tim = $bayar->team); ?>
    <?php echo $__env->make('backend.saas._header', ['judul' => 'Pembayaran ' . $bayar->kode, 'sub' => 'Pembayaran'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="row g-6">
        <div class="col-xl-5">
            <div class="card card-flush mb-6">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <span class="fs-1 fw-bold"><?php echo e($bayar->nominalLabel()); ?></span>
                        <span class="badge badge-light-<?php echo e($bayar->status->warna()); ?> fs-6"><?php echo e($bayar->status->label()); ?></span>
                    </div>
                    <table class="table table-row-dashed fs-7 mb-0">
                        <tr><td class="text-muted">Jenis</td><td><?php echo e($bayar->jenisLabel()); ?></td></tr>
                        <tr><td class="text-muted">Paket</td><td><?php echo e($bayar->plan?->nama ?? '-'); ?> <?php echo e($bayar->plan ? '(' . $bayar->plan->periodeLabel() . ')' : ''); ?></td></tr>
                        <tr><td class="text-muted">Tim</td><td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tim): ?>
                                <a href="<?php echo e(route('saas.tim.show', $tim->slug)); ?>" class="fw-bold"><?php echo e($tim->nama); ?></a>
                                <span class="badge badge-light-dark font-monospace"><?php echo e($tim->slug); ?></span>
                            <?php else: ?> - <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td></tr>
                        <tr><td class="text-muted">Penanggung jawab</td><td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pemilik): ?>
                                <?php echo e($pemilik->name); ?> (<?php echo e($pemilik->peran?->label()); ?>)<br>
                                <span class="text-muted"><?php echo e($pemilik->email); ?></span>
                                <?php ($wa = preg_replace('/\D+/', '', (string) ($pemilik->no_wa ?: $tim?->no_wa))); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($wa): ?>
                                    <br><a href="https://wa.me/<?php echo e(str_starts_with($wa, '0') ? '62' . substr($wa, 1) : $wa); ?>" target="_blank" rel="noopener" class="btn btn-sm btn-light-success mt-2">
                                        <i class="ki-outline ki-whatsapp fs-5"></i> Chat WhatsApp</a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php else: ?> - <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td></tr>
                        <tr><td class="text-muted">Rekening</td><td><?php echo e($bayar->bankAccount ? $bayar->bankAccount->bank . ' ' . $bayar->bankAccount->nomor : '-'); ?></td></tr>
                        <tr><td class="text-muted">Dibuat</td><td><?php echo e($bayar->created_at->translatedFormat('d M Y H:i')); ?></td></tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bayar->catatan): ?><tr><td class="text-muted">Catatan</td><td><?php echo e($bayar->catatan); ?></td></tr><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bayar->dikonfirmasi_pada): ?>
                            <tr><td class="text-muted">Diproses</td><td><?php echo e($bayar->dikonfirmasi_pada->translatedFormat('d M Y H:i')); ?> oleh <?php echo e($bayar->konfirmator?->name ?? '-'); ?></td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bayar->periode_mulai): ?>
                            <tr><td class="text-muted">Periode</td><td><?php echo e($bayar->periode_mulai->translatedFormat('d M Y')); ?> – <?php echo e($bayar->periode_selesai?->translatedFormat('d M Y') ?? 'selamanya'); ?></td></tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bayar->alasan_tolak): ?><tr><td class="text-muted">Alasan tolak</td><td class="text-danger"><?php echo e($bayar->alasan_tolak); ?></td></tr><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </table>
                </div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bayar->isMenunggu()): ?>
                <form method="POST" action="<?php echo e(route('saas.bayar.terima', $bayar)); ?>" enctype="multipart/form-data" class="card card-flush mb-6 border border-success">
                    <?php echo csrf_field(); ?>
                    <div class="card-header"><h3 class="card-title text-success">Terima (ACC) pembayaran</h3></div>
                    <div class="card-body pt-0">
                        <p class="text-muted fs-7">Tim akan aktif, masa langganan berjalan, dan owner bisa login di <b><?php echo e($tim ? route('tim.login', $tim->slug) : ''); ?></b>.</p>
                        <div class="mb-4">
                            <label class="form-label">Rekening tujuan</label>
                            <select name="bank_account_id" class="form-select">
                                <option value="">-</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $rekening; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e($r->id); ?>" <?php if($bayar->bank_account_id == $r->id): echo 'selected'; endif; ?>><?php echo e($r->bank); ?> <?php echo e($r->nomor); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </div>
                        <div class="mb-4"><label class="form-label">Catatan</label><input name="catatan" class="form-control" /></div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($bayar->punyaBukti())): ?>
                            <?php echo $__env->make('backend.saas._bukti_input', ['id' => 'acc'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <div class="card-footer"><button class="btn btn-success w-100" onclick="return confirm('Terima pembayaran dan aktifkan tim?')">Terima &amp; aktifkan</button></div>
                </form>

                <form method="POST" action="<?php echo e(route('saas.bayar.tolak', $bayar)); ?>" class="card card-flush">
                    <?php echo csrf_field(); ?>
                    <div class="card-body">
                        <label class="form-label">Tolak pembayaran</label>
                        <input name="alasan" class="form-control mb-3" placeholder="Alasan, mis. nominal tidak sesuai" required />
                        <button class="btn btn-light-danger w-100" onclick="return confirm('Tolak pembayaran ini?')">Tolak</button>
                    </div>
                </form>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="col-xl-7">
            <div class="card card-flush">
                <div class="card-header"><h3 class="card-title">Bukti transfer</h3></div>
                <div class="card-body pt-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bayar->punyaBukti()): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bayar->buktiPdf()): ?>
                            <embed src="<?php echo e(route('saas.bayar.bukti', $bayar)); ?>" type="application/pdf" class="w-100 rounded border" style="height: 600px" />
                        <?php else: ?>
                            <a href="<?php echo e(route('saas.bayar.bukti', $bayar)); ?>" target="_blank"><img src="<?php echo e(route('saas.bayar.bukti', $bayar)); ?>" alt="Bukti transfer" class="mw-100 rounded border" /></a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="mt-3"><a href="<?php echo e(route('saas.bayar.bukti', $bayar)); ?>" target="_blank" class="btn btn-sm btn-light">Buka di tab baru</a></div>
                    <?php else: ?>
                        <div class="text-muted mb-4">Belum ada bukti transfer.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <form method="POST" action="<?php echo e(route('saas.bayar.unggah', $bayar)); ?>" enctype="multipart/form-data" class="border-top pt-5 mt-5">
                        <?php echo csrf_field(); ?>
                        <?php echo $__env->make('backend.saas._bukti_input', ['id' => 'unggah'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <button class="btn btn-sm btn-primary mt-4"><?php echo e($bayar->punyaBukti() ? 'Ganti bukti' : 'Simpan bukti'); ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/backend/saas/bayar/show.blade.php ENDPATH**/ ?>