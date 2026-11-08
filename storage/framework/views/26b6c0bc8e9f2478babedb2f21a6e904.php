<?php $__env->startSection('title', 'Tim ' . $team->slug); ?>
<?php $__env->startSection('content'); ?>
    <?php ($penuh = $team->kuotaTersisa() < 1); ?>
    <?php echo $__env->make('backend.saas._header', [
        'judul' => $team->nama,
        'sub' => 'Tim ' . $team->slug,
        'aksi' => '<a href="' . route('saas.tim.edit', $team->slug) . '" class="btn btn-sm btn-light-primary"><i class="ki-outline ki-pencil fs-5"></i> Ubah</a>',
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="row g-6">
        <div class="col-xl-4">
            <div class="card card-flush mb-6">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-5">
                        <span class="badge badge-light-dark fs-2 font-monospace px-4 py-3"><?php echo e($team->slug); ?></span>
                        <span class="badge badge-light-<?php echo e($team->status->warna()); ?>"><?php echo e($team->status->label()); ?></span>
                    </div>
                    <label class="form-label text-muted fs-7">Link login tim</label>
                    <div class="input-group input-group-sm mb-5">
                        <input id="link-login" class="form-control" readonly value="<?php echo e(route('tim.login', $team->slug)); ?>" />
                        <button type="button" class="btn btn-light-primary"
                            onclick="navigator.clipboard.writeText(document.getElementById('link-login').value); this.innerText='Tersalin'">Salin</button>
                    </div>
                    <table class="table table-row-dashed fs-7 mb-0">
                        <tr><td class="text-muted">Paket</td><td class="fw-bold"><?php echo e($team->plan?->nama ?? '-'); ?></td></tr>
                        <tr><td class="text-muted">Masa aktif</td><td class="fw-bold">
                            <?php echo e($team->masaBerlakuLabel()); ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! is_null($team->sisaHari()) && $team->isAktif()): ?> <span class="text-muted">(sisa <?php echo e($team->sisaHari()); ?> hari)</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($team->hanyaBaca()): ?> <span class="badge badge-light-danger">Habis — hanya baca</span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td></tr>
                        <tr><td class="text-muted">Akun</td><td class="fw-bold"><?php echo e($anggota->count()); ?>/<?php echo e($team->max_users); ?></td></tr>
                        <tr><td class="text-muted">Kota</td><td><?php echo e($team->kota ?? '-'); ?></td></tr>
                        <tr><td class="text-muted">Email</td><td><?php echo e($team->email ?? '-'); ?></td></tr>
                        <tr><td class="text-muted">WhatsApp</td><td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($team->no_wa): ?>
                                <?php ($wa = preg_replace('/\D+/', '', $team->no_wa)); ?>
                                <a href="https://wa.me/<?php echo e(str_starts_with($wa, '0') ? '62' . substr($wa, 1) : $wa); ?>" target="_blank" rel="noopener"><?php echo e($team->no_wa); ?></a>
                            <?php else: ?> - <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td></tr>
                        <tr><td class="text-muted">Terdaftar</td><td><?php echo e($team->created_at->translatedFormat('d M Y')); ?></td></tr>
                    </table>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($team->catatan_admin): ?>
                        <div class="notice bg-light-warning rounded p-3 mt-4 fs-7"><?php echo e($team->catatan_admin); ?></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <div class="card-footer d-flex flex-wrap gap-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($team->status === \App\Enums\StatusTim::Ditangguhkan || $team->status === \App\Enums\StatusTim::Menunggu): ?>
                        <form method="POST" action="<?php echo e(route('saas.tim.status', $team->slug)); ?>">
                            <?php echo csrf_field(); ?> <input type="hidden" name="status" value="aktif" />
                            <button class="btn btn-sm btn-light-success" onclick="return confirm('Aktifkan tim ini?')">Aktifkan</button>
                        </form>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($team->status !== \App\Enums\StatusTim::Ditangguhkan): ?>
                        <form method="POST" action="<?php echo e(route('saas.tim.status', $team->slug)); ?>">
                            <?php echo csrf_field(); ?> <input type="hidden" name="status" value="ditangguhkan" />
                            <button class="btn btn-sm btn-light-warning" onclick="return confirm('Tangguhkan tim ini? Semua anggotanya akan keluar.')">Tangguhkan</button>
                        </form>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <form method="POST" action="<?php echo e(route('saas.tim.destroy', $team->slug)); ?>">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button class="btn btn-sm btn-light-danger" onclick="return confirm('Hapus tim <?php echo e($team->slug); ?>? Semua akun dinonaktifkan.')">Hapus</button>
                    </form>
                </div>
            </div>

            <form method="POST" action="<?php echo e(route('saas.tim.bayar', $team->slug)); ?>" enctype="multipart/form-data" class="card card-flush mb-6">
                <?php echo csrf_field(); ?>
                <div class="card-header"><h3 class="card-title">Catat pembayaran / perpanjang</h3></div>
                <div class="card-body pt-0">
                    <div class="mb-4">
                        <label class="form-label required">Paket</label>
                        <select name="plan_id" class="form-select">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($p->id); ?>" <?php if($team->plan_id == $p->id): echo 'selected'; endif; ?>><?php echo e($p->nama); ?> — <?php echo e($p->hargaLabel()); ?> <?php echo e($p->periodeLabel()); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Rekening tujuan</label>
                        <select name="bank_account_id" class="form-select">
                            <option value="">-</option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $rekening; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($r->id); ?>"><?php echo e($r->bank); ?> <?php echo e($r->nomor); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Catatan</label>
                        <input name="catatan" class="form-control" />
                    </div>
                    <?php echo $__env->make('backend.saas._bukti_input', ['id' => 'tim'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <label class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" name="langsung_terima" value="1" checked />
                        <span class="form-check-label">Sudah dibayar, langsung terima</span>
                    </label>
                </div>
                <div class="card-footer"><button class="btn btn-primary w-100">Simpan pembayaran</button></div>
            </form>
        </div>

        <div class="col-xl-8">
            <div class="card card-flush mb-6">
                <div class="card-header">
                    <h3 class="card-title">Akun tim <span class="text-muted fs-7 ms-2"><?php echo e($anggota->count()); ?>/<?php echo e($team->max_users); ?></span></h3>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-3">
                            <thead>
                                <tr class="text-muted fw-bold fs-7 text-uppercase"><th>Nama</th><th>Peran</th><th>Status</th><th>Login terakhir</th><th class="text-end">Aksi</th></tr>
                            </thead>
                            <tbody>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $anggota; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <tr>
                                        <td><div class="fw-bold"><?php echo e($u->name); ?></div><div class="text-muted fs-7"><?php echo e($u->email); ?> <?php echo e($u->no_wa ? '· ' . $u->no_wa : ''); ?></div></td>
                                        <td><?php echo e($u->peran?->label()); ?></td>
                                        <td><span class="badge badge-light-<?php echo e($u->is_active ? 'success' : 'secondary'); ?>"><?php echo e($u->is_active ? 'Aktif' : 'Nonaktif'); ?></span></td>
                                        <td class="fs-7"><?php echo e($u->last_login?->diffForHumans() ?? '-'); ?></td>
                                        <td class="text-end text-nowrap">
                                            <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#ubah-<?php echo e($u->id); ?>">Ubah</button>
                                            <form method="POST" action="<?php echo e(route('saas.tim.anggota.destroy', [$team->slug, $u->id])); ?>" class="d-inline">
                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                <button class="btn btn-sm btn-light-danger" onclick="return confirm('Hapus akun <?php echo e($u->email); ?>?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <form method="POST" action="<?php echo e(route('saas.tim.anggota.store', $team->slug)); ?>" class="border rounded p-5 mt-4">
                        <?php echo csrf_field(); ?>
                        <h4 class="fs-6 mb-4">Tambah akun</h4>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($penuh): ?>
                            <div class="alert alert-warning mb-0">Kuota akun penuh (<?php echo e($team->max_users); ?>). Naikkan "Maks akun" di Ubah tim bila perlu.</div>
                        <?php else: ?>
                            <div class="row g-3">
                                <div class="col-md-4"><input name="name" class="form-control form-control-sm" placeholder="Nama" required /></div>
                                <div class="col-md-4"><input type="email" name="email" class="form-control form-control-sm" placeholder="Email" required /></div>
                                <div class="col-md-4"><input name="no_wa" class="form-control form-control-sm" placeholder="No. WA" /></div>
                                <div class="col-md-4">
                                    <select name="peran" class="form-select form-select-sm">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = \App\Enums\Peran::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <option value="<?php echo e($p->value); ?>" <?php if($p === \App\Enums\Peran::Anggota): echo 'selected'; endif; ?>><?php echo e($p->label()); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-4"><input name="jabatan" class="form-control form-control-sm" placeholder="Jabatan" /></div>
                                <div class="col-md-4"><input type="text" name="password" class="form-control form-control-sm" placeholder="Password awal" required /></div>
                            </div>
                            <button class="btn btn-sm btn-primary mt-4">Tambah</button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="card card-flush">
                <div class="card-header"><h3 class="card-title">Riwayat pembayaran</h3></div>
                <div class="card-body pt-0">
                    <table class="table align-middle table-row-dashed fs-6 gy-3">
                        <thead><tr class="text-muted fw-bold fs-7 text-uppercase"><th>Kode</th><th>Jenis</th><th>Paket</th><th>Nominal</th><th>Status</th><th>Tanggal</th></tr></thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $team->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <tr>
                                    <td><a href="<?php echo e(route('saas.bayar.show', $p)); ?>" class="fw-bold"><?php echo e($p->kode); ?></a></td>
                                    <td><?php echo e($p->jenisLabel()); ?></td>
                                    <td><?php echo e($p->plan?->nama ?? '-'); ?></td>
                                    <td><?php echo e($p->nominalLabel()); ?></td>
                                    <td><span class="badge badge-light-<?php echo e($p->status->warna()); ?>"><?php echo e($p->status->label()); ?></span></td>
                                    <td class="fs-7"><?php echo e($p->created_at->translatedFormat('d M Y H:i')); ?></td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr><td colspan="6" class="text-center text-muted">Belum ada pembayaran.</td></tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $anggota; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <div class="modal fade" id="ubah-<?php echo e($u->id); ?>" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="<?php echo e(route('saas.tim.anggota.update', [$team->slug, $u->id])); ?>" class="modal-content">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <div class="modal-header"><h3 class="modal-title">Ubah akun</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Nama</label><input name="name" value="<?php echo e($u->name); ?>" class="form-control" required /></div>
                        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" value="<?php echo e($u->email); ?>" class="form-control" required /></div>
                        <div class="mb-3"><label class="form-label">No. WA</label><input name="no_wa" value="<?php echo e($u->no_wa); ?>" class="form-control" /></div>
                        <div class="mb-3"><label class="form-label">Jabatan</label><input name="jabatan" value="<?php echo e($u->jabatan); ?>" class="form-control" /></div>
                        <div class="mb-3"><label class="form-label">Peran</label>
                            <select name="peran" class="form-select">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = \App\Enums\Peran::cases(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <option value="<?php echo e($p->value); ?>" <?php if($u->peran === $p): echo 'selected'; endif; ?>><?php echo e($p->label()); ?></option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </select>
                        </div>
                        <div class="mb-3"><label class="form-label">Password baru</label><input type="text" name="password" class="form-control" placeholder="Kosongkan bila tidak diganti" /></div>
                        <input type="hidden" name="is_active" value="0" />
                        <label class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" value="1" <?php if($u->is_active): echo 'checked'; endif; ?> /><span class="form-check-label">Aktif</span></label>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('backend.layout.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\myProject\o\o-saas\resources\views/backend/saas/tim/show.blade.php ENDPATH**/ ?>