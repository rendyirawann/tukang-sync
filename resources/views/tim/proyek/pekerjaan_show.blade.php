@extends('tim.layout.app')
@section('title', $p->nama)

@php
    $rp = fn ($n) => 'Rp '.number_format((float) $n, 0, ',', '.');
    $angka = fn ($n) => rtrim(rtrim(number_format((float) $n, 4, ',', '.'), '0'), ',');
    $kelolaRab = \App\Support\Izin::kelolaRab($user);
@endphp

@section('content')
<div class="mb-4"><a href="{{ route('tim.proyek.show', [$tim->slug, $proyek, 'tab' => 'rab']) }}">← {{ $proyek->nama }}</a></div>
<div class="row g-5">
    <div class="col-xl-4">
        <div class="card shadow-sm mb-5"><div class="card-body p-8">
            <div class="text-muted fs-8">{{ $p->lokasiLabel() }}</div>
            <h3 class="fw-bolder">{{ $p->nama }}</h3>
            <div class="mb-4"><span class="badge badge-light-primary">{{ $p->komponen?->label() }}</span> <span class="badge badge-light">{{ $p->kategori?->label() }}</span></div>
            <table class="table table-sm mb-4">
                <tr><td class="text-muted">Jumlah</td><td class="text-end">{{ $angka($p->jumlah) }}</td></tr>
                @foreach (['panjang', 'lebar', 'tinggi'] as $d) @if ($p->{$d})<tr><td class="text-muted">{{ ucfirst($d) }}</td><td class="text-end">{{ $angka($p->{$d}) }} m</td></tr>@endif @endforeach
                <tr><td class="text-muted">Volume</td><td class="text-end fw-bold">{{ $angka($p->volume) }} {{ $p->satuanSimbol() }} {{ $p->volume_manual ? '(manual)' : '' }}</td></tr>
                <tr><td class="text-muted">Harga satuan</td><td class="text-end">{{ $rp($p->harga_satuan) }} / {{ $p->satuanSimbol() }}</td></tr>
                <tr><td class="text-muted">Analisa</td><td class="text-end">{{ $p->analisa?->nama ?? 'Borongan' }}</td></tr>
            </table>
            <div class="fs-2 fw-bolder" style="color:var(--tp-primary)">{{ $rp($p->total) }}</div>
            <div class="text-muted fs-8 mb-4">Progres {{ $p->progres }}% · senilai {{ $rp($p->nilaiProgres()) }}</div>

            @if (\App\Support\Izin::isiProgres($user))
                <form method="POST" action="{{ route('tim.proyek.pekerjaan.progres', [$tim->slug, $proyek, $p]) }}" class="d-flex gap-2 mb-4">
                    @csrf @method('PATCH')
                    <input type="range" min="0" max="100" step="5" name="progres" value="{{ $p->progres }}" class="form-range" oninput="this.nextElementSibling.textContent=this.value+'%'"><span class="fw-bold w-50px">{{ $p->progres }}%</span>
                    <button class="btn btn-sm btn-gold">Simpan</button>
                </form>
            @endif
            @if ($kelolaRab)
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('tim.proyek.pekerjaan.edit', [$tim->slug, $proyek, $p]) }}" class="btn btn-sm btn-primary">Ubah</a>
                    <form method="POST" action="{{ route('tim.proyek.pekerjaan.destroy', [$tim->slug, $proyek, $p]) }}" data-confirm="Hapus item RAB ini?">@csrf @method('DELETE')<button class="btn btn-sm btn-light-danger">Hapus</button></form>
                </div>
            @endif
        </div></div>
    </div>
    <div class="col-xl-8">
        <div class="card shadow-sm"><div class="card-header align-items-center">
            <h3 class="card-title fw-bold">Kebutuhan material & upah</h3>
            @if ($kelolaRab && $p->kebutuhan->isNotEmpty())
                <form method="POST" action="{{ route('tim.proyek.pekerjaan.segarkan', [$tim->slug, $proyek, $p]) }}">@csrf<button class="btn btn-sm btn-light">Segarkan harga dari katalog</button></form>
            @endif
        </div>
        <div class="card-body pt-0 table-responsive">
            <table class="table table-row-dashed fs-7">
                <thead><tr class="text-muted fs-8 text-uppercase"><th>Nama</th><th class="text-end">Koef.</th><th class="text-end">Qty</th><th>Satuan</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th></tr></thead>
                <tbody>
                @forelse ($p->kebutuhan as $k)
                    <tr><td>{{ $k->nama }} <span class="badge badge-light fs-9">{{ \App\Models\Material::KATEGORI[$k->kategori] ?? $k->kategori }}</span></td>
                        <td class="text-end text-muted">{{ $k->koefisien !== null ? $angka($k->koefisien) : '-' }}</td>
                        <td class="text-end fw-bold">{{ $angka($k->qty) }}</td><td>{{ $k->satuan }}</td>
                        <td class="text-end">{{ $rp($k->harga_satuan) }}</td><td class="text-end fw-bold">{{ $rp($k->subtotal) }}</td></tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-8">Pekerjaan borongan tanpa rincian. Pilih analisa di form ubah untuk menghitung kebutuhan otomatis.</td></tr>
                @endforelse
                </tbody>
                @if ($p->kebutuhan->isNotEmpty())<tfoot><tr class="fw-bolder"><td colspan="5">TOTAL</td><td class="text-end">{{ $rp($p->kebutuhan->sum('subtotal')) }}</td></tr></tfoot>@endif
            </table>
        </div></div>
    </div>
</div>
@endsection
