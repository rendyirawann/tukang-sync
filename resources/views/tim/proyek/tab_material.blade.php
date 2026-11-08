@php
    $grup = $rekap->groupBy('kategori');
    $angka = fn ($n) => rtrim(rtrim(number_format((float) $n, 3, ',', '.'), '0'), ',');
@endphp
<div class="card shadow-sm">
    <div class="card-header align-items-center"><h3 class="card-title fw-bold">Daftar kebutuhan material & upah</h3>
        <div class="text-muted fs-7">Realisasi dihitung dari belanja yang ditautkan ke material katalog.</div></div>
    <div class="card-body pt-0 table-responsive">
        <table class="table table-row-dashed fs-7">
            <thead><tr class="text-muted fs-8 text-uppercase"><th>Nama</th><th class="text-end">Kebutuhan</th><th>Satuan</th><th class="text-end">Biaya RAB</th><th class="text-end">Qty dibeli</th><th class="text-end">Biaya realisasi</th><th class="text-end">Sisa anggaran</th></tr></thead>
            <tbody>
            @forelse ($grup as $kategori => $baris)
                <tr class="bg-light"><td colspan="7" class="fw-bold text-uppercase fs-8 ps-3">{{ \App\Models\Material::KATEGORI[$kategori] ?? ucfirst((string) $kategori) }} — {{ $rp($baris->sum('subtotal')) }}</td></tr>
                @foreach ($baris as $m)
                    <tr>
                        <td>{{ $m['nama'] }}</td>
                        <td class="text-end fw-bold">{{ $angka($m['qty']) }}</td>
                        <td>{{ $m['satuan'] }}</td>
                        <td class="text-end">{{ $rp($m['subtotal']) }}</td>
                        <td class="text-end">{{ $angka($m['qty_realisasi']) }}</td>
                        <td class="text-end">{{ $rp($m['biaya_realisasi']) }}</td>
                        <td class="text-end {{ $m['selisih'] < 0 ? 'text-danger fw-bold' : '' }}">{{ $rp($m['selisih']) }}</td>
                    </tr>
                @endforeach
            @empty
                <tr><td colspan="7" class="text-center text-muted py-8">Belum ada kebutuhan. Pasang analisa pada item RAB agar kebutuhan material terhitung otomatis.</td></tr>
            @endforelse
            </tbody>
            @if ($rekap->isNotEmpty())
                <tfoot><tr class="fw-bolder"><td colspan="3">TOTAL</td><td class="text-end">{{ $rp($rekap->sum('subtotal')) }}</td><td></td><td class="text-end">{{ $rp($rekap->sum('biaya_realisasi')) }}</td><td class="text-end">{{ $rp($rekap->sum('selisih')) }}</td></tr></tfoot>
            @endif
        </table>
    </div>
</div>
