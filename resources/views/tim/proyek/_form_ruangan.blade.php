<form method="POST" action="{{ $aksi }}" class="row g-3 align-items-end">
    @csrf @if ($r) @method('PUT') @endif
    <div class="col-md-4"><label class="form-label fs-7">Nama ruangan</label><input name="nama" value="{{ $r?->nama }}" class="form-control form-control-sm" required maxlength="120" placeholder="Kamar Tidur 1"></div>
    <div class="col-md-2"><label class="form-label fs-7">Panjang m</label><input type="number" step="0.01" min="0" name="panjang" value="{{ $r?->panjang }}" class="form-control form-control-sm"></div>
    <div class="col-md-2"><label class="form-label fs-7">Lebar m</label><input type="number" step="0.01" min="0" name="lebar" value="{{ $r?->lebar }}" class="form-control form-control-sm"></div>
    <div class="col-md-2"><label class="form-label fs-7">Tinggi m</label><input type="number" step="0.01" min="0" name="tinggi" value="{{ $r?->tinggi }}" class="form-control form-control-sm"></div>
    <div class="col-md-2"><button class="btn btn-sm btn-primary w-100">Simpan</button></div>
</form>
