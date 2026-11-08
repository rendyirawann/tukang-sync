@extends('tim.layout.app')
@section('title', $proyek->exists ? 'Ubah Proyek' : 'Proyek Baru')

@section('content')
<div class="card shadow-sm" style="max-width:900px">
    <div class="card-body p-8">
        <form method="POST" action="{{ $proyek->exists ? route('tim.proyek.update', [$tim->slug, $proyek]) : route('tim.proyek.store', $tim->slug) }}">
            @csrf
            @if ($proyek->exists) @method('PUT') @endif
            <div class="row g-5">
                <div class="col-md-3"><label class="form-label">Kode</label><input name="kode" value="{{ old('kode', $proyek->kode) }}" class="form-control" maxlength="40" placeholder="PRJ-001"></div>
                <div class="col-md-9"><label class="form-label required">Nama proyek</label><input name="nama" value="{{ old('nama', $proyek->nama) }}" class="form-control" required maxlength="150"></div>
                <div class="col-md-6"><label class="form-label">Lokasi</label><input name="lokasi" value="{{ old('lokasi', $proyek->lokasi) }}" class="form-control" maxlength="150"></div>
                <div class="col-md-6"><label class="form-label">Sumber dana / klien</label><input name="sumber_dana" value="{{ old('sumber_dana', $proyek->sumber_dana) }}" class="form-control" maxlength="100"></div>
                <div class="col-md-4"><label class="form-label">Tanggal mulai</label><input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $proyek->tanggal_mulai?->format('Y-m-d')) }}" class="form-control"></div>
                <div class="col-md-4"><label class="form-label">Tanggal selesai</label><input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai', $proyek->tanggal_selesai?->format('Y-m-d')) }}" class="form-control"></div>
                <div class="col-md-4"><label class="form-label required">Status</label>
                    <select name="status" class="form-select">
                        @foreach (\App\Enums\StatusKegiatan::cases() as $s)
                            <option value="{{ $s->value }}" @selected(old('status', $proyek->status?->value ?? $proyek->status) === $s->value)>{{ ucfirst($s->value) }}</option>
                        @endforeach
                    </select></div>
                <div class="col-md-6"><label class="form-label">Nilai kontrak / pagu (Rp)</label><input type="number" min="0" step="1" name="pagu" value="{{ old('pagu', (int) $proyek->pagu) }}" class="form-control"></div>
                <div class="col-12"><label class="form-label">Keterangan</label><textarea name="keterangan" rows="3" class="form-control">{{ old('keterangan', $proyek->keterangan) }}</textarea></div>
            </div>
            <div class="d-flex gap-2 mt-8">
                <button class="btn btn-primary">Simpan</button>
                <a href="{{ $proyek->exists ? route('tim.proyek.show', [$tim->slug, $proyek]) : route('tim.proyek.index', $tim->slug) }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
