@extends('errors.layout')

@section('code', '401')
@section('title', 'Perlu Masuk')
@section('message', \App\Support\PesanGalat::untuk($exception ?? null, 'Anda perlu masuk terlebih dahulu untuk membuka halaman ini.'))
