<form method="POST" action="{{ $aksi }}" class="row g-2 align-items-end">
    @csrf @if ($m) @method('PUT') @endif
    <div class="col-md-1"><label class="form-label fs-8">Kode</label><input name="kode" value="{{ $m?->kode }}" class="form-control form-control-sm" maxlength="30"></div>
    <div class="col-md-3"><label class="form-label fs-8">Nama</label><input name="nama" value="{{ $m?->nama }}" class="form-control form-control-sm" required maxlength="150"></div>
    <div class="col-md-2"><label class="form-label fs-8">Kategori</label><select name="kategori" class="form-select form-select-sm">@foreach (\App\Models\Material::KATEGORI as $k => $v)<option value="{{ $k }}" @selected(($m?->kategori ?? 'material') === $k)>{{ $v }}</option>@endforeach</select></div>
    <div class="col-md-1"><label class="form-label fs-8">Satuan</label><input name="satuan" value="{{ $m?->satuan }}" class="form-control form-control-sm" required maxlength="30" placeholder="zak"></div>
    <div class="col-md-2"><label class="form-label fs-8">Harga (Rp)</label><input type="number" step="1" min="0" name="harga" value="{{ $m ? (int) $m->harga : '' }}" class="form-control form-control-sm" required></div>
    <div class="col-md-1"><label class="form-label fs-8">Merek</label><input name="merek" value="{{ $m?->merek }}" class="form-control form-control-sm" maxlength="80"></div>
    <div class="col-md-1"><label class="form-check mb-2"><input type="hidden" name="aktif" value="0"><input class="form-check-input" type="checkbox" name="aktif" value="1" @checked($m?->aktif ?? true)> <span class="form-check-label fs-8">Aktif</span></label></div>
    <div class="col-md-1"><button class="btn btn-sm btn-primary w-100">Simpan</button></div>
</form>
