@extends('errors.layout')

@section('code', '400')
@section('title', 'Permintaan Tidak Valid')
@section('message', \App\Support\PesanGalat::untuk($exception ?? null, 'Permintaan yang dikirim tidak dapat diproses. Periksa kembali isian Anda lalu coba lagi.'))
