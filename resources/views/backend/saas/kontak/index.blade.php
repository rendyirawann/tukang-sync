@extends('backend.layout.app')
@section('title', 'Kontak WhatsApp')
@section('content')
    @include('backend.saas._header', [
        'judul' => 'Kontak WhatsApp',
        'sub' => 'Kontak',
        'aksi' => '<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#kontak-baru"><i class="ki-outline ki-plus fs-5"></i> Tambah kontak</button>',
    ])
    <div class="alert alert-light-info">Kontak <b>utama</b> menjadi tujuan tombol "Konfirmasi via WhatsApp" setelah pelanggan mendaftar.</div>

    <div class="card card-flush">
        <div class="card-body">
            <table class="table align-middle table-row-dashed fs-6 gy-4">
                <thead><tr class="text-muted fw-bold fs-7 text-uppercase"><th>Nama</th><th>No. WhatsApp</th><th>Keterangan</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse ($kontak as $k)
                        <tr>
                            <td class="fw-bold">{{ $k->nama }} @if ($k->utama) <span class="badge badge-light-primary">Utama</span> @endif</td>
                            <td><a href="{{ $k->linkWa() }}" target="_blank" rel="noopener">{{ $k->no_wa }}</a></td>
                            <td class="text-muted fs-7">{{ $k->keterangan }}</td>
                            <td><span class="badge badge-light-{{ $k->aktif ? 'success' : 'secondary' }}">{{ $k->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td class="text-end text-nowrap">
                                <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#kontak-{{ $k->id }}">Ubah</button>
                                <form method="POST" action="{{ route('saas.kontak.destroy', $k) }}" class="d-inline">@csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light-danger" onclick="return confirm('Hapus kontak ini?')">Hapus</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">Belum ada kontak.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($kontak->concat([null]) as $k)
        <div class="modal fade" id="{{ $k ? 'kontak-' . $k->id : 'kontak-baru' }}" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ $k ? route('saas.kontak.update', $k) : route('saas.kontak.store') }}" class="modal-content">
                    @csrf @if ($k) @method('PUT') @endif
                    <div class="modal-header"><h3 class="modal-title">{{ $k ? 'Ubah kontak' : 'Kontak baru' }}</h3><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label required">Nama</label><input name="nama" value="{{ $k?->nama }}" class="form-control" required /></div>
                        <div class="mb-3"><label class="form-label required">No. WhatsApp</label><input name="no_wa" value="{{ $k?->no_wa }}" class="form-control" placeholder="08xxxxxxxxxx" required /></div>
                        <div class="mb-3"><label class="form-label">Keterangan</label><input name="keterangan" value="{{ $k?->keterangan }}" class="form-control" /></div>
                        <div class="mb-3"><label class="form-label">Urutan</label><input type="number" min="0" name="urutan" value="{{ $k?->urutan ?? 0 }}" class="form-control" /></div>
                        <label class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="aktif" value="1" @checked($k?->aktif ?? true) /><span class="form-check-label">Aktif</span></label>
                        <label class="form-check form-switch"><input class="form-check-input" type="checkbox" name="utama" value="1" @checked($k?->utama) /><span class="form-check-label">Kontak utama</span></label>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    @endforeach
@endsection
