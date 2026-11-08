@extends('tim.layout.app')
@section('title', $proyek->nama)

@php
    $rp = fn ($n) => 'Rp '.number_format((int) $n, 0, ',', '.');
    $kelolaRab = \App\Support\Izin::kelolaRab($user);
    $tabs = ['ringkasan' => 'Ringkasan', 'rab' => 'Struktur & RAB', 'material' => 'Kebutuhan Material', 'log' => 'Log Harian', 'belanja' => 'Belanja'];
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-5">
    <div>
        <div class="text-muted fs-7">{{ $proyek->kode }} · {{ $proyek->lokasi ?: 'Lokasi belum diisi' }}</div>
        <h2 class="fw-bolder mb-0">{{ $proyek->nama }}</h2>
    </div>
    @if (\App\Support\Izin::kelolaKegiatan($user))
        <div class="d-flex gap-2">
            <a href="{{ route('tim.proyek.edit', [$tim->slug, $proyek]) }}" class="btn btn-sm btn-light">Ubah proyek</a>
            <form method="POST" action="{{ route('tim.proyek.destroy', [$tim->slug, $proyek]) }}" data-confirm="Hapus proyek ini beserta seluruh RAB-nya?">
                @csrf @method('DELETE')<button class="btn btn-sm btn-light-danger">Hapus</button>
            </form>
        </div>
    @endif
</div>

<ul class="nav nav-tabs nav-line-tabs fs-6 fw-semibold mb-6 flex-nowrap overflow-auto">
    @foreach ($tabs as $k => $label)
        <li class="nav-item"><a class="nav-link text-nowrap {{ $tab === $k ? 'active' : '' }}" href="{{ route('tim.proyek.show', [$tim->slug, $proyek, 'tab' => $k]) }}">{{ $label }}</a></li>
    @endforeach
</ul>

@includeWhen(array_key_exists($tab, $tabs), 'tim.proyek.tab_'.$tab)
@endsection
