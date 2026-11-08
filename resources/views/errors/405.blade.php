@extends('errors.layout')

@section('code', '405')
@section('title', 'Metode Tidak Diizinkan')
@section('message', \App\Support\PesanGalat::untuk($exception ?? null, 'Cara permintaan ini tidak didukung untuk alamat tersebut. Muat ulang halaman lalu coba lagi.'))
