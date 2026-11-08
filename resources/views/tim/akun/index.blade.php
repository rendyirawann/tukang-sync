@extends('tim.layout.app')
@section('title', 'Akun Saya')

@section('content')
<div class="row g-5" style="max-width:1000px">
    <div class="col-lg-6">
        <div class="card shadow-sm"><div class="card-body p-8">
            <h4 class="fw-bold mb-1">Profil</h4>
            <div class="text-muted fs-7 mb-5">{{ $user->email }} · {{ $user->roleLabel() }}</div>
            <form method="POST" action="{{ route('tim.akun.profil', $tim->slug) }}">
                @csrf @method('PUT')
                <div class="mb-4"><label class="form-label">Nama</label><input name="name" value="{{ old('name', $user->name) }}" class="form-control" required maxlength="120"></div>
                <div class="mb-4"><label class="form-label">No. WhatsApp</label><input name="no_wa" value="{{ old('no_wa', $user->no_wa) }}" class="form-control" maxlength="20"></div>
                <div class="mb-6"><label class="form-label">Jabatan</label><input name="jabatan" value="{{ old('jabatan', $user->jabatan) }}" class="form-control" maxlength="100"></div>
                <button class="btn btn-primary">Simpan profil</button>
            </form>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm"><div class="card-body p-8">
            <h4 class="fw-bold mb-5">Ganti password</h4>
            <form method="POST" action="{{ route('tim.akun.password', $tim->slug) }}">
                @csrf @method('PUT')
                <div class="mb-4"><label class="form-label">Password lama</label><input type="password" name="current_password" class="form-control" required autocomplete="current-password"></div>
                <div class="mb-4"><label class="form-label">Password baru</label><input type="password" name="password" class="form-control" required autocomplete="new-password"></div>
                <div class="mb-6"><label class="form-label">Ulangi password baru</label><input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password"></div>
                <button class="btn btn-primary">Ganti password</button>
                <div class="form-text mt-3">Login di aplikasi mobile perlu diulang setelah password diganti.</div>
            </form>
        </div></div>
    </div>
</div>
@endsection
