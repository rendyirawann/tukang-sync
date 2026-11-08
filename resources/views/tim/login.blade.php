@extends('tim.layout.polos')
@section('title', 'Masuk · '.$tim->nama)

@section('content')
<div class="bg-navy py-15" style="min-height:80vh">
    <div class="container" style="max-width:440px">
        <div class="card shadow-lg">
            <div class="card-body p-10">
                <div class="text-center mb-8">
                    <span class="kode-badge fs-5">{{ $tim->slug }}</span>
                    <h2 class="fw-bold mt-4 mb-1">{{ $tim->nama }}</h2>
                    <div class="text-muted">Masuk ke panel tim</div>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger py-3">{{ $errors->first('email') ?: $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('tim.login.store', $tim->slug) }}">
                    @csrf
                    <div class="mb-5"><label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg" required autofocus autocomplete="username"></div>
                    <div class="mb-5"><label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg" required autocomplete="current-password"></div>
                    <label class="form-check mb-6"><input class="form-check-input" type="checkbox" name="remember" value="1"> <span class="form-check-label">Ingat saya</span></label>
                    <button class="btn btn-primary btn-lg w-100">Masuk</button>
                </form>
                <div class="text-center mt-6 fs-7"><a href="{{ route('tim.status', $tim->slug) }}">Status pendaftaran & pembayaran</a></div>
            </div>
        </div>
    </div>
</div>
@endsection
