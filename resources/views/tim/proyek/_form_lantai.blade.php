<form method="POST" action="{{ $aksi }}" class="row g-3 align-items-end">
    @csrf @if ($l) @method('PUT') @endif
    <div class="col-md-4"><label class="form-label fs-7">Nama lantai</label><input name="nama" value="{{ $l?->nama ?? 'Lantai '.$nomor }}" class="form-control form-control-sm" required maxlength="80"></div>
    <div class="col-md-2"><label class="form-label fs-7">Nomor</label><input type="number" name="nomor" value="{{ $nomor }}" class="form-control form-control-sm" required></div>
    <div class="col-md-2"><label class="form-label fs-7">Luas m²</label><input type="number" step="0.01" min="0" name="luas" value="{{ $l?->luas }}" class="form-control form-control-sm"></div>
    <div class="col-md-2"><label class="form-label fs-7">Tinggi m</label><input type="number" step="0.01" min="0" name="tinggi" value="{{ $l?->tinggi }}" class="form-control form-control-sm"></div>
    <div class="col-md-2"><button class="btn btn-sm btn-primary w-100">Simpan</button></div>
</form>
