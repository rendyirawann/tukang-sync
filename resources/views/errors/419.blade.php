@extends('errors.layout')

@section('code', '419')
@section('title', 'Sesi Kedaluwarsa')
@section('message', \App\Support\PesanGalat::untuk($exception ?? null, 'Sesi Anda telah kedaluwarsa. Silakan muat ulang halaman dan coba lagi.'))

@section('actions')
    <a href="javascript:location.reload()" class="btn btn-emas">Muat ulang</a>
    <a href="{{ url('/') }}" class="btn btn-garis">Beranda</a>
@endsection
