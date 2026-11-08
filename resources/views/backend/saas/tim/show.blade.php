@extends('backend.layout.app')
@section('title', 'Tim ' . $team->slug)
@section('content')
    @php($penuh = $team->kuotaTersisa() < 1)
    @include('backend.saas._header', [
        'judul' => $team->nama,
        'sub' => 'Tim ' . $team->slug,
        'aksi' => '<a href="' . route('saas.tim.edit', $team->slug) . '" class="btn btn-sm btn-light-primary"><i class="ki-outline ki-pencil fs-5"></i> Ubah</a>',
    ])

    <div class="row g-6">
        <div class="col-xl-4">
            <div class="card card-flush mb-6">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-5">
                        <span class="badge badge-light-dark fs-2 font-monospace px-4 py-3">{{ $team->slug }}</span>
                        <span class="badge badge-light-{{ $team->status->warna() }}">{{ $team->status->label() }}</span>
                    </div>
                    <label class="form-label text-muted fs-7">Link login tim</label>
                    <div class="input-group input-group-sm mb-5">
                        <input id="link-login" class="form-control" readonly value="{{ route('tim.login', $team->slug) }}" />
                        <button type="button" class="btn btn-light-primary"
                            onclick="navigator.clipboard.writeText(document.getElementById('link-login').value); this.innerText='Tersalin'">Salin</button>
                    </div>
                    <table class="table table-row-dashed fs-7 mb-0">
                        <tr><td class="text-muted">Paket</td><td class="fw-bold">{{ $team->plan?->nama ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Masa aktif</td><td class="fw-bold">
                            {{ $team->masaBerlakuLabel() }}
                            @if (! is_null($team->sisaHari()) && $team->isAktif()) <span class="text-muted">(sisa {{ $team->sisaHari() }} hari)</span> @endif
                            @if ($team->hanyaBaca()) <span class="badge badge-light-danger">Habis — hanya baca</span> @endif
                        </td></tr>
                        <tr><td class="text-muted">Akun</td><td class="fw-bold">{{ $anggota->count() }}/{{ $team->max_users }}</td></tr>
                        <tr><td class="text-muted">Kota</td><td>{{ $team->kota ?? '-' }}</td></tr>
                        <tr><td class="text-muted">Email</td><td>{{ $team->email ?? '-' }}</td></tr>
                        <tr><td class="text-muted">WhatsApp</td><td>
                            @if ($team->no_wa)
                                @php($wa = preg_replace('/\D+/', '', $team->no_wa))
                                <a href="https://wa.me/{{ str_starts_with($wa, '0') ? '62' . substr($wa, 1) : $wa }}" target="_blank" rel="noopener">{{ $team->no_wa }}</a>
                            @else - @endif
                        </td></tr>
                        <tr><td class="text-muted">Terdaftar</td><td>{{ $team->created_at->translatedFormat('d M Y') }}</td></tr>
                    </table>
                    @if ($team->catatan_admin)
                        <div class="notice bg-light-warning rounded p-3 mt-4 fs-7">{{ $team->catatan_admin }}</div>
                    @endif
                </div>
                <div class="card-footer d-flex flex-wrap gap-2">
                    @if ($team->status === \App\Enums\StatusTim::Ditangguhkan || $team->status === \App\Enums\StatusTim::Menunggu)
                        <form method="POST" action="{{ route('saas.tim.status', $team->slug) }}">
                            @csrf <input type="hidden" name="status" value="aktif" />
                            <button class="btn btn-sm btn-light-success" onclick="return confirm('Aktifkan tim ini?')">Aktifkan</button>
                        </form>
                    @endif
                    @if ($team->status !== \App\Enums\StatusTim::Ditangguhkan)
                        <form method="POST" action="{{ route('saas.tim.status', $team->slug) }}">
                            @csrf <input type="hidden" name="status" value="ditangguhkan" />
                            <button class="btn btn-sm btn-light-warning" onclick="return confirm('Tangguhkan tim ini? Semua anggotanya akan keluar.')">Tangguhkan</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('saas.tim.destroy', $team->slug) }}">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-light-danger" onclick="return confirm('Hapus tim {{ $team->slug }}? Semua akun dinonaktifkan.')">Hapus</button>
                    </form>
                </div>
            </div>

            <form method="POST" action="{{ route('saas.tim.bayar', $team->slug) }}" enctype="multipart/form-data" class="card card-flush mb-6">
                @csrf
                <div class="card-header"><h3 class="card-title">Catat pembayaran / perpanjang</h3></div>
                <div class="card-body pt-0">
                    <div class="mb-4">
                        <label class="form-label required">Paket</label>
                        <select name="plan_id" class="form-select">
                            @foreach ($plans as $p)
                                <option value="{{ $p->id }}" @selected($team->plan_id == $p->id)>{{ $p->nama }} — {{ $p->hargaLabel() }} {{ $p->periodeLabel() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Rekening tujuan</label>
                        <select name="bank_account_id" class="form-select">
                            <option value="">-</option>
                            @foreach ($rekening as $r)
                                <option value="{{ $r->id }}">{{ $r->bank }} {{ $r->nomor }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Catatan</label>
                        <input name="catatan" class="form-control" />
                    </div>
                    @include('backend.saas._bukti_input', ['id' => 'tim'])
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
                    <h3 class="card-title">Akun tim <span class="text-muted fs-7 ms-2">{{ $anggota->count() }}/{{ $team->max_users }}</span></h3>
                </div>
                <div class="card-body pt-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-row-dashed fs-6 gy-3">
                            <thead>
                                <tr class="text-muted fw-bold fs-7 text-uppercase"><th>Nama</th><th>Peran</th><th>Status</th><th>Login terakhir</th><th class="text-end">Aksi</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($anggota as $u)
                                    <tr>
                                        <td><div class="fw-bold">{{ $u->name }}</div><div class="text-muted fs-7">{{ $u->email }} {{ $u->no_wa ? '· ' . $u->no_wa : '' }}</div></td>
                                        <td>{{ $u->peran?->label() }}</td>
                                        <td><span class="badge badge-light-{{ $u->is_active ? 'success' : 'secondary' }}">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                        <td class="fs-7">{{ $u->last_login?->diffForHumans() ?? '-' }}</td>
                                        <td class="text-end text-nowrap">
                                            <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#ubah-{{ $u->id }}">Ubah</button>
                                            <form method="POST" action="{{ route('saas.tim.anggota.destroy', [$team->slug, $u->id]) }}" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-light-danger" onclick="return confirm('Hapus akun {{ $u->email }}?')">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <form method="POST" action="{{ route('saas.tim.anggota.store', $team->slug) }}" class="border rounded p-5 mt-4">
                        @csrf
                        <h4 class="fs-6 mb-4">Tambah akun</h4>
                        @if ($penuh)
                            <div class="alert alert-warning mb-0">Kuota akun penuh ({{ $team->max_users }}). Naikkan "Maks akun" di Ubah tim bila perlu.</div>
                        @else
                            <div class="row g-3">
                                <div class="col-md-4"><input name="name" class="form-control form-control-sm" placeholder="Nama" required /></div>
                                <div class="col-md-4"><input type="email" name="email" class="form-control form-control-sm" placeholder="Email" required /></div>
                                <div class="col-md-4"><input name="no_wa" class="form-control form-control-sm" placeholder="No. WA" /></div>
                                <div class="col-md-4">
                                    <select name="peran" class="form-select form-select-sm">
                                        @foreach (\App\Enums\Peran::cases() as $p)
                                            <option value="{{ $p->value }}" @selected($p === \App\Enums\Peran::Anggota)>{{ $p->label() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4"><input name="jabatan" class="form-control form-control-sm" placeholder="Jabatan" /></div>
                                <div class="col-md-4"><input type="text" name="password" class="form-control form-control-sm" placeholder="Password awal" required /></div>
                            </div>
                            <button class="btn btn-sm btn-primary mt-4">Tambah</button>
                        @endif
                    </form>
                </div>
            </div>

            <div class="card card-flush">
                <div class="card-header"><h3 class="card-title">Riwayat pembayaran</h3></div>
                <div class="card-body pt-0">
                    <table class="table align-middle table-row-dashed fs-6 gy-3">
                        <thead><tr class="text-muted fw-bold fs-7 text-uppercase"><th>Kode</th><th>Jenis</th><th>Paket</th><th>Nominal</th><th>Status</th><th>Tanggal</th></tr></thead>
                        <tbody>
                            @forelse ($team->payments as $p)
                                <tr>
                                    <td><a href="{{ route('saas.bayar.show', $p) }}" class="fw-bold">{{ $p->kode }}</a></td>
                                    <td>{{ $p->jenisLabel() }}</td>
                                    <td>{{ $p->plan?->nama ?? '-' }}</td>
                                    <td>{{ $p->nominalLabel() }}</td>
                                    <td><span class="badge badge-light-{{ $p->status->warna() }}">{{ $p->status->label() }}</span></td>
                                    <td class="fs-7">{{ $p->created_at->translatedFormat('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted">Belum ada pembayaran.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @foreach ($anggota as $u)
        <div class="modal fade" id="ubah-{{ $u->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('saas.tim.anggota.update', [$team->slug, $u->id]) }}" class="modal-content">
                    @csrf @method('PUT')
                    <div class="modal-header"><h3 class="modal-title">Ubah akun</h3>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label">Nama</label><input name="name" value="{{ $u->name }}" class="form-control" required /></div>
                        <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ $u->email }}" class="form-control" required /></div>
                        <div class="mb-3"><label class="form-label">No. WA</label><input name="no_wa" value="{{ $u->no_wa }}" class="form-control" /></div>
                        <div class="mb-3"><label class="form-label">Jabatan</label><input name="jabatan" value="{{ $u->jabatan }}" class="form-control" /></div>
                        <div class="mb-3"><label class="form-label">Peran</label>
                            <select name="peran" class="form-select">
                                @foreach (\App\Enums\Peran::cases() as $p)
                                    <option value="{{ $p->value }}" @selected($u->peran === $p)>{{ $p->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3"><label class="form-label">Password baru</label><input type="text" name="password" class="form-control" placeholder="Kosongkan bila tidak diganti" /></div>
                        <input type="hidden" name="is_active" value="0" />
                        <label class="form-check form-switch"><input class="form-check-input" type="checkbox" name="is_active" value="1" @checked($u->is_active) /><span class="form-check-label">Aktif</span></label>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    @endforeach
@endsection
