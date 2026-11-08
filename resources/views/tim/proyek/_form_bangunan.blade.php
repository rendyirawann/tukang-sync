<form method="POST" action="{{ $aksi }}" class="row g-3 align-items-end">
    @csrf @if ($b) @method('PUT') @endif
    <div class="col-md-4"><label class="form-label fs-7">Nama bangunan</label><input name="nama" value="{{ $b?->nama }}" class="form-control form-control-sm" required maxlength="120" placeholder="Rumah Utama"></div>
    <div class="col-md-3"><label class="form-label fs-7">Jenis</label>
        <select name="jenis" class="form-select form-select-sm">@foreach ($jenisBangunan as $k => $v)<option value="{{ $k }}" @selected(($b?->jenis ?? 'rumah') === $k)>{{ $v }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label fs-7">Luas tanah m²</label><input type="number" step="0.01" min="0" name="luas_tanah" value="{{ $b?->luas_tanah }}" class="form-control form-control-sm"></div>
    <div class="col-md-2"><label class="form-label fs-7">Luas bangunan m²</label><input type="number" step="0.01" min="0" name="luas_bangunan" value="{{ $b?->luas_bangunan }}" class="form-control form-control-sm"></div>
    <div class="col-md-1"><button class="btn btn-sm btn-primary w-100">Simpan</button></div>
</form>
