@extends('errors.layout')

@section('code', '422')
@section('title', 'Data Tidak Valid')
@section('message', \App\Support\PesanGalat::untuk($exception ?? null, 'Data yang dikirim belum sesuai. Periksa kembali isian Anda.'))
