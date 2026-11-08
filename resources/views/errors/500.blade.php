@extends('errors.layout')

@section('code', '500')
@section('title', 'Terjadi Kesalahan')
@section('message', \App\Support\PesanGalat::untuk($exception ?? null, 'Terjadi kesalahan pada server. Tim kami akan memeriksanya; silakan coba beberapa saat lagi.'))
