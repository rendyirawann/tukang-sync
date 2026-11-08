@extends('tim.layout.app')
@section('title', 'Analisa Harga Satuan')

@section('content')
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-5">
    <p class="text-muted mb-0" style="max-width:700px">Analisa (AHSP) adalah resep kebutuhan untuk 1 satuan pekerjaan, mis. 1 m² dinding bata = 70 bata + 0,23 zak semen + upah. Harga satuan dihitung dari harga katalog terkini.</p>
    @if ($boleh)<a href="{{ route('tim.analisa.create', $tim->slug) }}" class="btn btn-sm btn-primary">+ Analisa baru</a>@endif
</div>
@forelse ($analisa->groupBy(fn ($a) => $a->kategori?->label()) as $kat => $daftar)
    <div class="card shadow-sm mb-5">
        <div class="card-header"><h3 class="card-title fw-bold">{{ $kat }}</h3></div>
        <div class="card-body pt-0">
            @foreach ($daftar as $a)
                <div class="border-bottom py-4">
                    <div class="d-flex justify-content-between flex-wrap gap-2">
                        <div><span class="text-muted fs-8">{{ $a->kode }}</span> <span class="fw-bold">{{ $a->nama }}</span></div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bolder" style="color:var(--tp-primary)">Rp {{ number_format($a->hargaSatuan(), 0, ',', '.') }} / {{ $a->satuan }}</span>
                            @if ($boleh)
                                <a href="{{ route('tim.analisa.edit', [$tim->slug, $a]) }}" class="btn btn-sm btn-light py-1">Ubah</a>
                                <form method="POST" action="{{ route('tim.analisa.destroy', [$tim->slug, $a]) }}" data-confirm="Hapus analisa ini? Item RAB yang memakainya tetap menyimpan rinciannya.">@csrf @method('DELETE')<button class="btn btn-sm btn-light-danger py-1">Hapus</button></form>
                            @endif
                        </div>
                    </div>
                    <div class="text-muted fs-8 mt-1">
                        {{ $a->items->map(fn ($i) => rtrim(rtrim(number_format($i->koefisien, 4, ',', '.'), '0'), ',').' '.$i->material?->satuan.' '.$i->material?->nama)->implode(' · ') }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@empty
    <div class="card"><div class="card-body text-center text-muted py-10">Belum ada analisa.</div></div>
@endforelse
@endsection
