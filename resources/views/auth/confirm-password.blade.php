@extends('auth.app')

@section('title', 'Konfirmasi Kata Sandi')

@section('content')
    <div class="auth-card">
        <div class="auth-card__brand">
            <img src="{{ $brand['logo_url'] }}" alt="{{ config('app.name') }}" width="56" height="56" />
        </div>

        <h1 class="auth-card__title">Konfirmasi kata sandi</h1>
        <p class="auth-card__subtitle">Area ini sensitif. Masukkan kata sandi Anda sekali lagi untuk melanjutkan.</p>

        @if ($errors->any())
            <div class="auth-alert" role="alert">
                <i class="ki-outline ki-shield-cross fs-4 mt-1" aria-hidden="true"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="auth-field">
                <label class="auth-label" for="confirm-password">Kata sandi</label>
                <div class="auth-input-wrap">
                    <input type="password" name="password" id="confirm-password" class="auth-input"
                        autocomplete="current-password" required
                        @if ($errors->has('password')) aria-invalid="true" @endif />
                </div>
            </div>

            <button type="submit" class="auth-submit">Konfirmasi</button>
        </form>
    </div>
@endsection
