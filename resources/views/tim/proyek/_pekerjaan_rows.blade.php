{{-- $daftar: koleksi Pekerjaan; $isiProgres: bool --}}
@if ($daftar->isNotEmpty())
<div class="table-responsive">
<table class="table table-sm table-row-dashed mb-2 fs-7">
    <tbody>
    @foreach ($daftar as $p)
        <tr>
            <td><a href="{{ route('tim.proyek.pekerjaan.show', [$tim->slug, $proyek, $p]) }}" class="fw-semibold text-gray-800 text-hover-primary">{{ $p->nama }}</a>
                <div class="text-muted fs-8">{{ $p->komponen?->label() }} · {{ rtrim(rtrim(number_format($p->volume, 3, ',', '.'), '0'), ',') }} {{ $p->satuanSimbol() }}</div></td>
            <td class="text-end fw-bold text-nowrap">{{ $rp($p->total) }}</td>
            <td style="width:170px">
                @if ($isiProgres)
                    <form method="POST" action="{{ route('tim.proyek.pekerjaan.progres', [$tim->slug, $proyek, $p]) }}" class="d-flex gap-1 align-items-center">
                        @csrf @method('PATCH')
                        <input type="number" name="progres" min="0" max="100" value="{{ $p->progres }}" class="form-control form-control-sm w-70px">
                        <span>%</span><button class="btn btn-sm btn-icon btn-light" title="Simpan progres">✓</button>
                    </form>
                @else
                    <div class="progress progress-thin"><div class="progress-bar" style="width:{{ $p->progres }}%;background:var(--tp-gold)"></div></div>
                    <span class="fs-8">{{ $p->progres }}%</span>
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
</div>
@endif
