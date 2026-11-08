@extends('tim.layout.app')
@section('title', 'Dasbor')

@php $rp = fn ($n) => 'Rp '.number_format((int) $n, 0, ',', '.'); @endphp

@section('content')
<div class="row g-5 mb-6">
    @foreach ([
        ['Jumlah proyek', $jumlahProyek, ($perStatus['berjalan'] ?? 0).' berjalan · '.($perStatus['selesai'] ?? 0).' selesai'],
        ['Total RAB', $rp($totalRab), 'semua proyek'],
        ['Progres rata-rata', $progres.'%', 'tertimbang nilai RAB'],
        ['Belanja bulan ini', $rp($belanjaBulanIni), now()->translatedFormat('F Y')],
    ] as [$judul, $nilai, $sub])
        <div class="col-6 col-xl-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="text-muted fs-7 mb-1">{{ $judul }}</div>
                    <div class="fs-2 fw-bolder" style="color:var(--tp-primary)">{{ $nilai }}</div>
                    <div class="text-muted fs-8">{{ $sub }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-5">
    <div class="col-xl-8">
        <div class="card shadow-sm h-100">
            <div class="card-header align-items-center">
                <h3 class="card-title fw-bold">Proyek terbaru</h3>
                <a href="{{ route('tim.proyek.index', $tim->slug) }}" class="btn btn-sm btn-light">Semua proyek</a>
            </div>
            <div class="card-body pt-2">
                @forelse ($proyek as $k)
                    <a href="{{ route('tim.proyek.show', [$tim->slug, $k]) }}" class="d-block py-4 border-bottom text-gray-800">
                        <div class="d-flex justify-content-between gap-3">
                            <div class="min-w-0"><div class="fw-bold text-truncate">{{ $k->nama }}</div>
                                <div class="text-muted fs-8">{{ $k->lokasi ?: '-' }} · {{ ucfirst($k->status->value ?? (string) $k->status) }}</div></div>
                            <div class="text-end"><div class="fw-bold">{{ $rp($k->rab_total) }}</div><div class="text-muted fs-8">{{ $k->rab_progres }}%</div></div>
                        </div>
                        <div class="progress progress-thin mt-2"><div class="progress-bar" style="width:{{ $k->rab_progres }}%;background:var(--tp-gold)"></div></div>
                    </a>
                @empty
                    <div class="text-center py-10 text-muted">Belum ada proyek.
                        @if (\App\Support\Izin::kelolaKegiatan(auth()->user()))
                            <div class="mt-3"><a href="{{ route('tim.proyek.create', $tim->slug) }}" class="btn btn-primary btn-sm">Buat proyek pertama</a></div>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="card shadow-sm mb-5 bg-navy text-white">
            <div class="card-body">
                <div class="fs-7" style="color:#c8d3e6">Langganan</div>
                <div class="fs-3 fw-bold">{{ $tim->plan?->nama ?? '-' }}</div>
                <div class="mb-3 text-gold fw-semibold">{{ $tim->lifetime ? 'Lifetime' : 'Berlaku sampai '.$tim->masaBerlakuLabel() }}</div>
                <a href="{{ route('tim.langganan.index', $tim->slug) }}" class="btn btn-sm btn-gold">Kelola langganan</a>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header"><h3 class="card-title fw-bold">Log harian terbaru</h3></div>
            <div class="card-body pt-2">
                @forelse ($log as $l)
                    <div class="py-3 border-bottom">
                        <div class="d-flex justify-content-between fs-8 text-muted"><span>{{ $l->tanggal->translatedFormat('d M Y') }}</span><span>{{ $l->pembuat?->name }}</span></div>
                        <div class="fw-semibold fs-7">{{ $l->kegiatan?->nama }}</div>
                        <div class="fs-7">{{ \Illuminate\Support\Str::limit($l->uraian, 90) }}</div>
                    </div>
                @empty
                    <div class="text-muted py-6 text-center">Belum ada catatan lapangan.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
