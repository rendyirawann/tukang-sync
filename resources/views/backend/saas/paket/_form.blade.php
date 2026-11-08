@php($p = $plan ?? null)
<div class="row g-3">
    <div class="col-md-4"><label class="form-label required">Kode</label><input name="kode" value="{{ $p?->kode }}" class="form-control" required /></div>
    <div class="col-md-8"><label class="form-label required">Nama</label><input name="nama" value="{{ $p?->nama }}" class="form-control" required /></div>
    <div class="col-md-4"><label class="form-label required">Harga (Rp)</label><input type="number" min="0" name="harga" value="{{ $p?->harga }}" class="form-control" required /></div>
    <div class="col-md-4"><label class="form-label required">Jenis</label>
        <select name="jenis" class="form-select">
            <option value="berkala" @selected($p && ! $p->isLifetime())>Berkala</option>
            <option value="lifetime" @selected($p?->isLifetime())>Lifetime</option>
        </select>
    </div>
    <div class="col-md-4"><label class="form-label">Durasi (bulan)</label><input type="number" min="1" name="durasi_bulan" value="{{ $p?->durasi_bulan ?? 1 }}" class="form-control" /></div>
    <div class="col-md-4"><label class="form-label required">Maks akun</label><input type="number" min="1" name="max_users" value="{{ $p?->max_users ?? 4 }}" class="form-control" required /></div>
    <div class="col-md-4"><label class="form-label">Urutan</label><input type="number" min="0" name="urutan" value="{{ $p?->urutan ?? 0 }}" class="form-control" /></div>
    <div class="col-md-4 d-flex align-items-end"><label class="form-check form-switch"><input class="form-check-input" type="checkbox" name="aktif" value="1" @checked($p?->aktif ?? true) /><span class="form-check-label">Aktif</span></label></div>
    <div class="col-12"><label class="form-label">Deskripsi</label><input name="deskripsi" value="{{ $p?->deskripsi }}" class="form-control" /></div>
    <div class="col-12"><label class="form-label">Fitur (satu per baris)</label><textarea name="fitur" rows="4" class="form-control">{{ implode("\n", $p?->fitur ?? []) }}</textarea></div>
</div>
