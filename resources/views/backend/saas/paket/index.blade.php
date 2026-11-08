@extends('backend.layout.app')
@section('title', 'Paket Langganan')
@section('content')
    @include('backend.saas._header', [
        'judul' => 'Paket Langganan',
        'sub' => 'Paket',
        'aksi' => '<button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#paket-baru"><i class="ki-outline ki-plus fs-5"></i> Tambah paket</button>',
    ])

    <div class="card card-flush">
        <div class="card-body">
            <table class="table align-middle table-row-dashed fs-6 gy-4">
                <thead><tr class="text-muted fw-bold fs-7 text-uppercase"><th>Paket</th><th>Harga</th><th>Periode</th><th>Maks akun</th><th>Tim</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                    @forelse ($plans as $plan)
                        <tr>
                            <td><div class="fw-bold">{{ $plan->nama }}</div><div class="text-muted fs-7">{{ $plan->kode }} · {{ $plan->deskripsi }}</div></td>
                            <td class="fw-bold">{{ $plan->hargaLabel() }}</td>
                            <td>{{ $plan->periodeLabel() }}</td>
                            <td>{{ $plan->max_users }}</td>
                            <td>{{ $plan->teams_count }}</td>
                            <td><span class="badge badge-light-{{ $plan->aktif ? 'success' : 'secondary' }}">{{ $plan->aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                            <td class="text-end text-nowrap">
                                <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#paket-{{ $plan->id }}">Ubah</button>
                                <form method="POST" action="{{ route('saas.paket.destroy', $plan) }}" class="d-inline">@csrf @method('DELETE')
                                    <button class="btn btn-sm btn-light-danger" onclick="return confirm('Hapus paket ini?')">Hapus</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Belum ada paket.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @foreach ($plans->concat([null]) as $plan)
        <div class="modal fade" id="{{ $plan ? 'paket-' . $plan->id : 'paket-baru' }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form method="POST" action="{{ $plan ? route('saas.paket.update', $plan) : route('saas.paket.store') }}" class="modal-content">
                    @csrf @if ($plan) @method('PUT') @endif
                    <div class="modal-header"><h3 class="modal-title">{{ $plan ? 'Ubah paket' : 'Paket baru' }}</h3><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">@include('backend.saas.paket._form', ['plan' => $plan])</div>
                    <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary">Simpan</button></div>
                </form>
            </div>
        </div>
    @endforeach
@endsection
