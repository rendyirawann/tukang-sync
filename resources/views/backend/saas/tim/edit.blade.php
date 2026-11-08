@extends('backend.layout.app')
@section('title', 'Ubah Tim ' . $team->slug)
@section('content')
    @include('backend.saas._header', ['judul' => 'Ubah tim ' . $team->nama, 'sub' => $team->slug])

    <form method="POST" action="{{ route('saas.tim.update', $team->slug) }}" class="card card-flush">
        @csrf @method('PUT')
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label required">Nama tim</label>
                    <input name="nama" value="{{ old('nama', $team->nama) }}" class="form-control" required />
                </div>
                <div class="col-md-3 mb-4">
                    <label class="form-label">Kota</label>
                    <input name="kota" value="{{ old('kota', $team->kota) }}" class="form-control" />
                </div>
                <div class="col-md-3 mb-4">
                    <label class="form-label">No. WhatsApp</label>
                    <input name="no_wa" value="{{ old('no_wa', $team->no_wa) }}" class="form-control" />
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">Alamat</label>
                    <input name="alamat" value="{{ old('alamat', $team->alamat) }}" class="form-control" />
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $team->email) }}" class="form-control" />
                </div>
                <div class="col-md-4 mb-4">
                    <label class="form-label">Paket</label>
                    <select name="plan_id" class="form-select">
                        <option value="">-</option>
                        @foreach ($plans as $p)
                            <option value="{{ $p->id }}" @selected(old('plan_id', $team->plan_id) == $p->id)>{{ $p->nama }} ({{ $p->hargaLabel() }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-4">
                    <label class="form-label required">Maks akun</label>
                    <input type="number" min="1" max="50" name="max_users" value="{{ old('max_users', $team->max_users) }}" class="form-control" />
                </div>
                <div class="col-md-3 mb-4">
                    <label class="form-label">Aktif sampai</label>
                    <input type="date" name="aktif_sampai" value="{{ old('aktif_sampai', $team->aktif_sampai?->format('Y-m-d')) }}" class="form-control" />
                </div>
                <div class="col-md-3 mb-4 d-flex align-items-end">
                    <label class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="lifetime" value="1" @checked(old('lifetime', $team->lifetime)) />
                        <span class="form-check-label">Lifetime</span>
                    </label>
                </div>
                <div class="col-12 mb-4">
                    <label class="form-label">Catatan admin (internal)</label>
                    <textarea name="catatan_admin" rows="3" class="form-control">{{ old('catatan_admin', $team->catatan_admin) }}</textarea>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('saas.tim.show', $team->slug) }}" class="btn btn-light">Batal</a>
            <button class="btn btn-primary">Simpan</button>
        </div>
    </form>
@endsection
