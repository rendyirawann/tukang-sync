@extends('errors.layout')

@section('code', '502')
@section('title', 'Gateway Bermasalah')
@section('message', \App\Support\PesanGalat::untuk($exception ?? null, 'Server sedang tidak dapat dihubungi. Silakan coba beberapa saat lagi.'))
