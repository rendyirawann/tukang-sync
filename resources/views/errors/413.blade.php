@extends('errors.layout')

@section('code', '413')
@section('title', 'Berkas Terlalu Besar')
@section('message', \App\Support\PesanGalat::untuk($exception ?? null, 'Ukuran file terlalu besar. Perkecil ukuran file lalu unggah kembali.'))
