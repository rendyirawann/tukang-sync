@extends('backend.layout.app')
@section('title', 'Rekening Pembayaran')
@section('content')
    @include('backend.saas._header', [
        'judul' => 'Rekening Pembayaran',
        'sub' => 'Rekening',
        'aksi' => '<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#rek-baru"><i class="ki-outline ki-plus fs-5"></i> Tambah rekening</button>',
    ])
    <div class="alert alert-light-info">Rekening aktif tampil di formulir pendaftaran dan halaman status pembayaran tim.</div>

    <div class="card card-flush">
        <div class="card-body">
            <table class="table align-middle table-row-dashed fs-6 gy-4">
                <thead><tr class="text-muted fw-bold fs-7 text-uppercase"><th>Bank</th><th>Nomor</th><th>Atas nama</th><th>Keterangan</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse ($rekening as $r)
                        <tr>
                            <td class="fw-bold">{{ $r->bank }}</td>
                            <td class="font-monospace">{{ $r->nomor }}</td>
                            <td>{{ $r->atas_nama }}</td>
                            <td class="text-muted fs-7">{{ $r->keterangan }}</td>
                            <td><span class="badge badge-light-{{ $r->aktif ? 'success' : 'secondary' }}">{{ $r->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td class="text-end text-nowrap">
                                <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#rek-{{ $r->id }}">Ubah</button>
                                <form method="POST" action="{{ route('saas.rekening.destroy', $r) }}" class="d-inline">@csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light-danger" onclick="return confirm('Hapus rekening ini?')">Hapus</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">Belum ada rekening.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($rekening->concat([null]) as $r)
        <div class="modal fade" id="{{ $r ? 'rek-' . $r->id : 'rek-baru' }}" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ $r ? route('saas.rekening.update', $r) : route('saas.rekening.store') }}" class="modal-content">
                    @csrf @if ($r) @method('PUT') @endif
                    <div class="modal-header"><h3 class="modal-title">{{ $r ? 'Ubah rekening' : 'Rekening baru' }}</h3><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-3"><label class="form-label required">Bank</label><input name="bank" value="{{ $r?->bank }}" class="form-control" placeholder="BCA / BRI / Mandiri / DANA" required /></div>
                        <div class="mb-3"><label class="form-label required">Nomor rekening</label><input name="nomor" value="{{ $r?->nomor }}" class="form-control" required /></div>
                        <div class="mb-3"><label class="form-label required">Atas nama</label><input name="atas_nama" value="{{ $r?->atas_nama }}" class="form-control" required /></div>
                        <div class="mb-3"><label class="form-label">Keterangan</label><input name="keterangan" value="{{ $r?->keterangan }}" class="form-control" /></div>
                        <div class="mb-3"><label class="form-label">Urutan</label><input type="number" min="0" name="urutan" value="{{ $r?->urutan ?? 0 }}" class="form-control" /></div>
                        <label class="form-check form-switch"><input class="form-check-input" type="checkbox" name="aktif" value="1" @checked($r?->aktif ?? true) /><span class="form-check-label">Aktif</span></label>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    @endforeach
@endsection
