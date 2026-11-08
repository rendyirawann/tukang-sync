@extends('tim.layout.app')
@section('title', 'Katalog Material')

@section('content')
@if ($boleh)
<div class="card shadow-sm mb-5"><div class="card-body">
    <div class="fw-bold mb-3">Tambah material / upah / alat</div>
    @include('tim.katalog._form_material', ['m' => null, 'aksi' => route('tim.material.store', $tim->slug)])
</div></div>
@endif

<div class="card shadow-sm">
    <div class="card-header align-items-center py-4">
        <form class="d-flex gap-2 flex-wrap" method="GET">
            <input name="q" value="{{ $cari }}" class="form-control form-control-sm w-200px" placeholder="Cari nama">
            <select name="kategori" class="form-select form-select-sm w-150px" onchange="this.form.submit()"><option value="">Semua</option>
                @foreach (\App\Models\Material::KATEGORI as $k => $v)<option value="{{ $k }}" @selected($kategori === $k)>{{ $v }}</option>@endforeach</select>
            <button class="btn btn-sm btn-light">Cari</button>
        </form>
        <div class="text-muted fs-7">Harga per daerah berbeda — sesuaikan dengan harga setempat.</div>
    </div>
    <div class="card-body pt-0 table-responsive">
        <table class="table table-row-dashed fs-7">
            <thead><tr class="text-muted fs-8 text-uppercase"><th>Kode</th><th>Nama</th><th>Kategori</th><th>Satuan</th><th class="text-end">Harga</th><th>Toko/merek</th><th></th></tr></thead>
            <tbody>
            @forelse ($material as $m)
                <tr class="{{ $m->aktif ? '' : 'opacity-50' }}">
                    <td class="text-muted">{{ $m->kode }}</td><td class="fw-semibold">{{ $m->nama }}</td>
                    <td><span class="badge badge-light">{{ $m->kategoriLabel() }}</span></td><td>{{ $m->satuan }}</td>
                    <td class="text-end fw-bold">Rp {{ number_format($m->harga, 0, ',', '.') }}</td>
                    <td class="text-muted">{{ collect([$m->merek, $m->toko])->filter()->implode(' · ') }}</td>
                    <td class="text-end text-nowrap">
                        @if ($boleh)
                            <button class="btn btn-sm btn-light py-1" data-bs-toggle="collapse" data-bs-target="#eM{{ $m->id }}">Ubah</button>
                            <form method="POST" action="{{ route('tim.material.destroy', [$tim->slug, $m]) }}" class="d-inline" data-confirm="Hapus material ini dari katalog?">@csrf @method('DELETE')<button class="btn btn-sm btn-light-danger py-1">Hapus</button></form>
                        @endif
                    </td>
                </tr>
                @if ($boleh)
                    <tr class="collapse" id="eM{{ $m->id }}"><td colspan="7">@include('tim.katalog._form_material', ['m' => $m, 'aksi' => route('tim.material.update', [$tim->slug, $m])])</td></tr>
                @endif
            @empty
                <tr><td colspan="7" class="text-center text-muted py-8">Katalog kosong.</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $material->links() }}
    </div>
</div>
@endsection
