@php
    $isiProgres = \App\Support\Izin::isiProgres($user);
    $grup = $pekerjaan->groupBy(fn ($p) => ($p->bangunan_id ?? 0).'-'.($p->lantai_id ?? 0).'-'.($p->ruangan_id ?? 0));
    $node = fn ($b, $l, $r) => $grup->get(($b ?? 0).'-'.($l ?? 0).'-'.($r ?? 0), collect());
    $tambah = fn (array $q) => route('tim.proyek.pekerjaan.create', [$tim->slug, $proyek] + $q);
@endphp

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div class="fs-5">Total RAB: <span class="fw-bolder" style="color:var(--tp-primary)">{{ $rp($ringkasan['total_rab']) }}</span> · Progres {{ $ringkasan['progres'] }}%</div>
    @if ($kelolaRab)
        <div class="d-flex gap-2">
            <a href="{{ $tambah([]) }}" class="btn btn-sm btn-light">+ Item umum</a>
            <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#fBangunanBaru">+ Bangunan</button>
        </div>
    @endif
</div>

@if ($kelolaRab)
<div class="collapse mb-5" id="fBangunanBaru"><div class="card card-body shadow-sm">
    @include('tim.proyek._form_bangunan', ['b' => null, 'aksi' => route('tim.proyek.bangunan.store', [$tim->slug, $proyek])])
</div></div>
@endif

@if ($node(null, null, null)->isNotEmpty())
    <div class="card shadow-sm mb-5"><div class="card-body py-4">
        <div class="fw-bold mb-2">Pekerjaan umum (tanpa lokasi)</div>
        @include('tim.proyek._pekerjaan_rows', ['daftar' => $node(null, null, null)])
    </div></div>
@endif

@forelse ($proyek->bangunan as $b)
    <div class="card shadow-sm mb-5">
        <div class="card-header align-items-center py-3" style="border-left:4px solid var(--tp-gold)">
            <div><h3 class="card-title fw-bold mb-0">🏠 {{ $b->nama }}</h3>
                <div class="text-muted fs-8">{{ $b->jenisLabel() }}@if($b->luas_bangunan) · LB {{ $b->luas_bangunan }} m²@endif @if($b->luas_tanah) · LT {{ $b->luas_tanah }} m²@endif</div></div>
            @if ($kelolaRab)
                <div class="d-flex gap-1 flex-wrap">
                    <a href="{{ $tambah(['bangunan_id' => $b->id]) }}" class="btn btn-sm btn-light">+ Item</a>
                    <button class="btn btn-sm btn-light" data-bs-toggle="collapse" data-bs-target="#fLantai{{ $b->id }}">+ Lantai</button>
                    <button class="btn btn-sm btn-light" data-bs-toggle="collapse" data-bs-target="#fEditB{{ $b->id }}">Ubah</button>
                    <form method="POST" action="{{ route('tim.proyek.bangunan.destroy', [$tim->slug, $proyek, $b]) }}" data-confirm="Hapus bangunan, lantai, ruangan, dan item RAB di dalamnya?">@csrf @method('DELETE')<button class="btn btn-sm btn-light-danger">Hapus</button></form>
                </div>
            @endif
        </div>
        <div class="card-body py-4">
            @if ($kelolaRab)
                <div class="collapse mb-4" id="fEditB{{ $b->id }}">@include('tim.proyek._form_bangunan', ['b' => $b, 'aksi' => route('tim.proyek.bangunan.update', [$tim->slug, $proyek, $b])])</div>
                <div class="collapse mb-4" id="fLantai{{ $b->id }}">@include('tim.proyek._form_lantai', ['l' => null, 'aksi' => route('tim.proyek.lantai.store', [$tim->slug, $proyek, $b]), 'nomor' => $b->lantai->count() + 1])</div>
            @endif

            @include('tim.proyek._pekerjaan_rows', ['daftar' => $node($b->id, null, null)])

            @foreach ($b->lantai as $l)
                <div class="border rounded p-4 mb-3 ms-md-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                        <div class="fw-bold">▤ {{ $l->nama }} <span class="text-muted fs-8 fw-normal">@if($l->luas) {{ $l->luas }} m²@endif @if($l->tinggi) · tinggi {{ $l->tinggi }} m @endif</span></div>
                        @if ($kelolaRab)
                            <div class="d-flex gap-1 flex-wrap">
                                <a href="{{ $tambah(['bangunan_id' => $b->id, 'lantai_id' => $l->id]) }}" class="btn btn-xs btn-sm btn-light py-1">+ Item</a>
                                <button class="btn btn-sm btn-light py-1" data-bs-toggle="collapse" data-bs-target="#fRuang{{ $l->id }}">+ Ruangan</button>
                                <button class="btn btn-sm btn-light py-1" data-bs-toggle="collapse" data-bs-target="#fEditL{{ $l->id }}">Ubah</button>
                                <form method="POST" action="{{ route('tim.proyek.lantai.destroy', [$tim->slug, $proyek, $l]) }}" data-confirm="Hapus lantai beserta ruangan & item RAB-nya?">@csrf @method('DELETE')<button class="btn btn-sm btn-light-danger py-1">Hapus</button></form>
                            </div>
                        @endif
                    </div>
                    @if ($kelolaRab)
                        <div class="collapse mb-3" id="fEditL{{ $l->id }}">@include('tim.proyek._form_lantai', ['l' => $l, 'aksi' => route('tim.proyek.lantai.update', [$tim->slug, $proyek, $l]), 'nomor' => $l->nomor])</div>
                        <div class="collapse mb-3" id="fRuang{{ $l->id }}">@include('tim.proyek._form_ruangan', ['r' => null, 'aksi' => route('tim.proyek.ruangan.store', [$tim->slug, $proyek, $l])])</div>
                    @endif

                    @include('tim.proyek._pekerjaan_rows', ['daftar' => $node($b->id, $l->id, null)])

                    @foreach ($l->ruangan as $r)
                        <div class="bg-light rounded p-3 mb-2 ms-md-4">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="fw-semibold">◻ {{ $r->nama }}
                                    <span class="text-muted fs-8 fw-normal">@if($r->luasLantai()) lantai {{ $r->luasLantai() }} m²@endif @if($r->luasDinding()) · dinding {{ $r->luasDinding() }} m²@endif</span></div>
                                @if ($kelolaRab)
                                    <div class="d-flex gap-1">
                                        <a href="{{ $tambah(['bangunan_id' => $b->id, 'lantai_id' => $l->id, 'ruangan_id' => $r->id]) }}" class="btn btn-sm btn-light py-1">+ Item</a>
                                        <button class="btn btn-sm btn-light py-1" data-bs-toggle="collapse" data-bs-target="#fEditR{{ $r->id }}">Ubah</button>
                                        <form method="POST" action="{{ route('tim.proyek.ruangan.destroy', [$tim->slug, $proyek, $r]) }}" data-confirm="Hapus ruangan beserta item RAB-nya?">@csrf @method('DELETE')<button class="btn btn-sm btn-light-danger py-1">Hapus</button></form>
                                    </div>
                                @endif
                            </div>
                            @if ($kelolaRab)
                                <div class="collapse mt-3" id="fEditR{{ $r->id }}">@include('tim.proyek._form_ruangan', ['r' => $r, 'aksi' => route('tim.proyek.ruangan.update', [$tim->slug, $proyek, $r])])</div>
                            @endif
                            @include('tim.proyek._pekerjaan_rows', ['daftar' => $node($b->id, $l->id, $r->id)])
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@empty
    <div class="card shadow-sm"><div class="card-body text-center py-12 text-muted">
        Belum ada bangunan. Tambahkan bangunan (mis. "Rumah Utama"), lalu lantai dan ruangannya, kemudian item pekerjaan seperti pondasi, kolom, dinding, pintu, dan jendela.
    </div></div>
@endforelse
