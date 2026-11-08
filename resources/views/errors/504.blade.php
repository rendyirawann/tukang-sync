@extends('errors.layout')

@section('code', '504')
@section('title', 'Waktu Tunggu Server Habis')
@section('message', \App\Support\PesanGalat::untuk($exception ?? null, 'Server terlalu lama merespons. Silakan coba beberapa saat lagi.'))
