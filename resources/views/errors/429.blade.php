@extends('errors.layout')

@section('code', '429')
@section('title', 'Terlalu Banyak Permintaan')
@section('message', (($d = \App\Support\PesanGalat::retryAfter($exception ?? null)) ? "Terlalu banyak permintaan. Coba lagi dalam {$d} detik." : 'Terlalu banyak permintaan. Silakan tunggu sebentar lalu coba lagi.'))
