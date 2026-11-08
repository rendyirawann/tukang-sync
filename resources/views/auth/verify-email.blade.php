@extends('auth.app')

@section('title', 'Verifikasi Email')

@section('content')
    <div class="auth-card">
        <div class="auth-card__brand">
            <img src="{{ $brand['logo_url'] }}" alt="{{ config('app.name') }}" width="56" height="56" />
        </div>

        <h1 class="auth-card__title">Verifikasi email Anda</h1>
        <p class="auth-card__subtitle">Kami sudah mengirim tautan verifikasi ke email Anda. Belum menerima? Kirim ulang di bawah.</p>

        @if (session('status') === 'verification-link-sent')
            <div class="auth-alert" role="status"
                style="background: rgba(34,197,94,.14); border-color: rgba(74,222,128,.38); color:#bbf7d0;">
                <span>Tautan verifikasi baru sudah dikirim.</span>
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="auth-submit">Kirim ulang tautan verifikasi</button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="btn btn-link p-0" style="color: #a5b4fc;">Keluar</button>
        </form>
    </div>
@endsection
