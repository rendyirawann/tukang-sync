@php $bolehBelanja = \App\Support\Izin::kelolaBahanBaku($user); @endphp
@if ($bolehBelanja)
<div class="card shadow-sm mb-5"><div class="card-body">
    <form method="POST" action="{{ route('tim.proyek.belanja.store', [$tim->slug, $proyek]) }}" class="row g-3 align-items-end">
        @csrf
        <div class="col-md-3"><label class="form-label fs-7">Material katalog (opsional)</label>
            <select name="material_id" id="pilihMaterial" class="form-select form-select-sm"><option value="">— ketik manual —</option>
                @foreach ($materials as $m)<option value="{{ $m->id }}" data-satuan="{{ $m->satuan }}" data-harga="{{ (int) $m->harga }}">{{ $m->nama }}</option>@endforeach
            </select></div>
        <div class="col-md-3"><label class="form-label fs-7">Nama barang</label><input name="nama" id="belanjaNama" class="form-control form-control-sm" maxlength="150"></div>
        <div class="col-md-1"><label class="form-label fs-7">Qty</label><input type="number" step="0.001" min="0" name="qty" class="form-control form-control-sm" required></div>
        <div class="col-md-1"><label class="form-label fs-7">Satuan</label><input name="satuan" id="belanjaSatuan" class="form-control form-control-sm" maxlength="30"></div>
        <div class="col-md-2"><label class="form-label fs-7">Harga satuan</label><input type="number" min="0" name="harga_satuan" id="belanjaHarga" class="form-control form-control-sm" required></div>
        <div class="col-md-2"><label class="form-label fs-7">Tanggal</label><input type="date" name="tanggal_beli" value="{{ now()->toDateString() }}" class="form-control form-control-sm"></div>
        <div class="col-md-3"><label class="form-label fs-7">Untuk item RAB (opsional)</label>
            <select name="pekerjaan_id" class="form-select form-select-sm"><option value="">—</option>@foreach ($pekerjaan as $p)<option value="{{ $p->id }}">{{ $p->nama }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label fs-7">Toko</label><input name="toko" class="form-control form-control-sm" maxlength="120"></div>
        <div class="col-md-4"><label class="form-label fs-7">Keterangan</label><input name="keterangan" class="form-control form-control-sm"></div>
        <div class="col-md-2"><button class="btn btn-sm btn-primary w-100">Catat belanja</button></div>
    </form>
</div></div>
@endif

<div class="card shadow-sm"><div class="card-body table-responsive">
    <table class="table table-row-dashed fs-7">
        <thead><tr class="text-muted fs-8 text-uppercase"><th>Tanggal</th><th>Barang</th><th class="text-end">Qty</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th><th>Toko</th><th></th></tr></thead>
        <tbody>
        @forelse ($belanja as $b)
            <tr>
                <td>{{ $b->tanggal_beli?->format('d/m/Y') }}</td>
                <td class="fw-semibold">{{ $b->nama }}</td>
                <td class="text-end">{{ rtrim(rtrim(number_format((float) $b->qty, 3, ',', '.'), '0'), ',') }} {{ $b->satuan }}</td>
                <td class="text-end">{{ $rp($b->harga_satuan) }}</td>
                <td class="text-end fw-bold">{{ $rp($b->subtotal) }}</td>
                <td>{{ $b->toko }}</td>
                <td class="text-end">@if ($bolehBelanja)<form method="POST" action="{{ route('tim.proyek.belanja.destroy', [$tim->slug, $proyek, $b]) }}" data-confirm="Hapus belanja ini?">@csrf @method('DELETE')<button class="btn btn-sm btn-light-danger py-1">Hapus</button></form>@endif</td>
            </tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted py-8">Belum ada belanja tercatat.</td></tr>
        @endforelse
        </tbody>
    </table>
    {{ $belanja?->links() }}
</div></div>

@push('scripts')
<script>
    (function () {
        var s = document.getElementById('pilihMaterial'); if (!s) return;
        s.addEventListener('change', function () {
            var o = s.selectedOptions[0]; if (!o || !o.value) return;
            document.getElementById('belanjaNama').value = o.text;
            document.getElementById('belanjaSatuan').value = o.dataset.satuan;
            document.getElementById('belanjaHarga').value = o.dataset.harga;
        });
    })();
</script>
@endpush
