@php $r = $ringkasan; $selisih = $r['pagu'] - $r['total_rab']; @endphp
<div class="row g-5 mb-6">
    @foreach ([
        ['Nilai kontrak / pagu', $rp($r['pagu']), null],
        ['Total RAB', $rp($r['total_rab']), $r['jumlah_item'].' item pekerjaan'],
        ['Selisih pagu − RAB', $rp($selisih), $selisih < 0 ? 'RAB melebihi pagu!' : 'margin'],
        ['Progres fisik', $r['progres'].'%', 'senilai '.$rp($r['nilai_progres'])],
        ['Realisasi belanja', $rp($r['realisasi_belanja']), $r['total_rab'] > 0 ? round($r['realisasi_belanja'] / $r['total_rab'] * 100, 1).'% dari RAB' : null],
    ] as [$j, $v, $s])
        <div class="col-6 col-xl">
            <div class="card shadow-sm h-100"><div class="card-body">
                <div class="text-muted fs-8">{{ $j }}</div>
                <div class="fs-3 fw-bolder {{ $j === 'Selisih pagu − RAB' && $selisih < 0 ? 'text-danger' : '' }}" style="{{ $j === 'Selisih pagu − RAB' && $selisih < 0 ? '' : 'color:var(--tp-primary)' }}">{{ $v }}</div>
                @if ($s)<div class="text-muted fs-8">{{ $s }}</div>@endif
            </div></div>
        </div>
    @endforeach
</div>

<div class="row g-5">
    <div class="col-xl-6">
        <div class="card shadow-sm"><div class="card-header"><h3 class="card-title fw-bold">Per kategori pekerjaan</h3></div>
            <div class="card-body pt-0 table-responsive"><table class="table table-row-dashed">
                <thead><tr class="text-muted fs-8 text-uppercase"><th>Kategori</th><th class="text-end">Item</th><th class="text-end">Total</th><th class="text-end">%</th></tr></thead>
                <tbody>
                @forelse ($r['per_kategori'] as $k)
                    <tr><td>{{ $k['label'] }}</td><td class="text-end">{{ $k['jumlah_item'] }}</td><td class="text-end fw-bold">{{ $rp($k['total']) }}</td><td class="text-end">{{ $k['persen'] }}%</td></tr>
                @empty
                    <tr><td colspan="4" class="text-muted text-center py-6">Belum ada item RAB.</td></tr>
                @endforelse
                </tbody>
            </table></div></div>
    </div>
    <div class="col-xl-6">
        <div class="card shadow-sm"><div class="card-header"><h3 class="card-title fw-bold">Per lokasi</h3></div>
            <div class="card-body pt-0 table-responsive"><table class="table table-row-dashed">
                <thead><tr class="text-muted fs-8 text-uppercase"><th>Lokasi</th><th class="text-end">Total</th><th style="width:30%">Progres</th></tr></thead>
                <tbody>
                @forelse ($r['per_lokasi'] as $l)
                    <tr><td>{{ $l['lokasi'] }}</td><td class="text-end fw-bold">{{ $rp($l['total']) }}</td>
                        <td><div class="d-flex align-items-center gap-2"><div class="progress progress-thin flex-grow-1"><div class="progress-bar" style="width:{{ $l['progres'] }}%;background:var(--tp-gold)"></div></div><span class="fs-8">{{ $l['progres'] }}%</span></div></td></tr>
                @empty
                    <tr><td colspan="3" class="text-muted text-center py-6">Belum ada item RAB.</td></tr>
                @endforelse
                </tbody>
            </table></div></div>
    </div>
</div>
