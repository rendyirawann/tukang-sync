@extends('errors.layout')

@section('code', '403')
@section('title', 'Akses Ditolak')
@section('message', \App\Support\PesanGalat::untuk($exception ?? null, 'Anda tidak memiliki akses ke halaman ini.'))
