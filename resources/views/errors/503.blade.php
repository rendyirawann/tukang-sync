@extends('errors.layout')

@section('code', '503')
@section('title', 'Sedang Dalam Pemeliharaan')
@section('message', \App\Support\PesanGalat::untuk($exception ?? null, 'Layanan sedang dalam pemeliharaan atau sibuk. Silakan coba beberapa saat lagi.'))
