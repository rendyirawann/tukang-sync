@extends('tim.layout.app')
@section('title', $analisa->exists ? 'Ubah Analisa' : 'Analisa Baru')

@php
    $items = old('items', $analisa->exists ? $analisa->items->map(fn ($i) => ['material_id' => $i->material_id, 'koefisien' => $i->koefisien])->all() : [['material_id' => '', 'koefisien' => '']]);
@endphp

@section('content')
<form method="POST" action="{{ $analisa->exists ? route('tim.analisa.update', [$tim->slug, $analisa]) : route('tim.analisa.store', $tim->slug) }}">
    @csrf @if ($analisa->exists) @method('PUT') @endif
    <div class="card shadow-sm mb-5"><div class="card-body p-8"><div class="row g-4">
        <div class="col-md-2"><label class="form-label">Kode</label><input name="kode" value="{{ old('kode', $analisa->kode) }}" class="form-control" maxlength="30"></div>
        <div class="col-md-6"><label class="form-label required">Nama pekerjaan</label><input name="nama" value="{{ old('nama', $analisa->nama) }}" class="form-control" required maxlength="200"></div>
        <div class="col-md-2"><label class="form-label required">Kategori</label><select name="kategori" class="form-select">@foreach ($kategori as $k => $v)<option value="{{ $k }}" @selected(old('kategori', $analisa->kategori?->value) === $k)>{{ $v }}</option>@endforeach</select></div>
        <div class="col-md-2"><label class="form-label required">Per satuan</label><select name="satuan" class="form-select">@foreach ($satuan as $k => $v)<option value="{{ $k }}" @selected(old('satuan', $analisa->satuan) === $k)>{{ $v }}</option>@endforeach</select></div>
        <div class="col-12"><label class="form-label">Keterangan</label><input name="keterangan" value="{{ old('keterangan', $analisa->keterangan) }}" class="form-control" maxlength="255"></div>
    </div></div></div>

    <div class="card shadow-sm"><div class="card-header align-items-center"><h3 class="card-title fw-bold">Kebutuhan per 1 satuan</h3>
        <div class="fw-bolder fs-4" style="color:var(--tp-primary)">Rp <span id="totalAnalisa">0</span></div></div>
        <div class="card-body pt-0">
            <table class="table fs-7" id="tabelItem">
                <thead><tr class="text-muted fs-8 text-uppercase"><th>Material / upah</th><th style="width:150px">Koefisien</th><th class="text-end">Harga</th><th class="text-end">Subtotal</th><th></th></tr></thead>
                <tbody></tbody>
            </table>
            <button type="button" class="btn btn-sm btn-light" id="tambahItem">+ Baris</button>
        </div>
    </div>
    <div class="mt-5 d-flex gap-2"><button class="btn btn-primary">Simpan analisa</button><a href="{{ route('tim.analisa.index', $tim->slug) }}" class="btn btn-light">Batal</a></div>
</form>
@endsection

@push('scripts')
<script>
(function () {
    var material = @json($material), awal = @json(array_values($items)), tbody = document.querySelector('#tabelItem tbody'), n = 0;
    var rp = function (x) { return Math.round(x).toLocaleString('id-ID'); };
    function baris(it) {
        var i = n++, tr = document.createElement('tr');
        var opsi = '<option value="">— pilih —</option>' + material.map(function (m) {
            return '<option value="' + m.id + '" data-harga="' + m.harga + '" ' + (String(m.id) === String(it.material_id) ? 'selected' : '') + '>' + m.nama + ' (' + m.satuan + ')</option>';
        }).join('');
        tr.innerHTML = '<td><select name="items[' + i + '][material_id]" class="form-select form-select-sm" required>' + opsi + '</select></td>' +
            '<td><input type="number" step="0.0001" min="0" name="items[' + i + '][koefisien]" value="' + (it.koefisien || '') + '" class="form-control form-control-sm" required></td>' +
            '<td class="text-end h"></td><td class="text-end fw-bold s"></td><td><button type="button" class="btn btn-sm btn-icon btn-light-danger">×</button></td>';
        tr.querySelector('button').onclick = function () { tr.remove(); hitung(); };
        tr.querySelectorAll('select,input').forEach(function (e) { e.addEventListener('input', hitung); e.addEventListener('change', hitung); });
        tbody.appendChild(tr);
    }
    function hitung() {
        var tot = 0;
        tbody.querySelectorAll('tr').forEach(function (tr) {
            var o = tr.querySelector('select').selectedOptions[0], h = o && o.dataset.harga ? parseFloat(o.dataset.harga) : 0;
            var k = parseFloat(tr.querySelector('input').value) || 0;
            tr.querySelector('.h').textContent = rp(h); tr.querySelector('.s').textContent = rp(h * k); tot += h * k;
        });
        document.getElementById('totalAnalisa').textContent = rp(tot);
    }
    awal.forEach(baris); hitung();
    document.getElementById('tambahItem').onclick = function () { baris({}); };
})();
</script>
@endpush
