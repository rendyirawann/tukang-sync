@extends('errors.layout')

@section('code', '404')
@section('title', 'Halaman Tidak Ditemukan')
@section('message', \App\Support\PesanGalat::untuk($exception ?? null, 'Halaman yang Anda cari tidak ada, sudah dipindahkan, atau alamatnya salah ketik.'))
