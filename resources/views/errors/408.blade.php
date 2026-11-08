@extends('errors.layout')

@section('code', '408')
@section('title', 'Waktu Permintaan Habis')
@section('message', \App\Support\PesanGalat::untuk($exception ?? null, 'Server menunggu terlalu lama. Periksa koneksi Anda lalu coba lagi.'))
