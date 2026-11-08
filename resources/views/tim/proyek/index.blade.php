@extends('tim.layout.app')
@section('title', 'Proyek & RAB')

@section('content')
<div class="card shadow-sm">
    <div class="card-header align-items-center flex-wrap gap-3 py-4">
        <form class="d-flex gap-2 flex-wrap" method="GET">
            <input name="q" value="{{ $cari }}" class="form-control form-control-sm w-200px" placeholder="Cari nama, kode, lokasi">
            <select name="status" class="form-select form-select-sm w-150px" onchange="this.form.submit()">
                <option value="">Semua status</option>
                @foreach (\App\Enums\StatusKegiatan::cases() as $s)
                    <option value="{{ $s->value }}" @selected($status === $s->value)>{{ ucfirst($s->value) }}</option>
                @endforeach
            </select>
            <button class="btn btn-sm btn-light">Cari</button>
        </form>
        @if (\App\Support\Izin::kelolaKegiatan(auth()->user()))
            <a href="{{ route('tim.proyek.create', $tim->slug) }}" class="btn btn-sm btn-primary">+ Proyek baru</a>
        @endif
    </div>
    <div class="card-body pt-0 table-responsive">
        <table class="table table-row-dashed align-middle">
            <thead><tr class="text-muted fw-bold fs-7 text-uppercase"><th>Proyek</th><th>Lokasi</th><th>Status</th><th class="text-end">Pagu</th><th class="text-end">Total RAB</th></tr></thead>
            <tbody>
                @forelse ($proyek as $k)
                    <tr>
                        <td><a href="{{ route('tim.proyek.show', [$tim->slug, $k]) }}" class="fw-bold text-gray-800 text-hover-primary">{{ $k->nama }}</a>
                            <div class="text-muted fs-8">{{ $k->kode }}</div></td>
                        <td>{{ $k->lokasi ?: '-' }}</td>
                        <td><span class="badge badge-light-primary">{{ ucfirst($k->status->value ?? (string) $k->status) }}</span></td>
                        <td class="text-end">Rp {{ number_format((int) $k->pagu, 0, ',', '.') }}</td>
                        <td class="text-end fw-bold">Rp {{ number_format((int) $k->rab_total, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-10">Belum ada proyek.</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $proyek->links() }}
    </div>
</div>
@endsection
