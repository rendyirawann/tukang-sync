@extends('tim.layout.app')
@section('title', 'Anggota Tim')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
    <div class="fs-5">Kuota akun: <span class="fw-bold">{{ $anggota->count() }} dari {{ $tim->max_users }} akun</span> (termasuk owner)</div>
    @if ($boleh && $anggota->count() < $tim->max_users)
        <button class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#fAnggota">+ Tambah anggota</button>
    @endif
</div>

@if ($boleh)
<div class="collapse mb-5" id="fAnggota"><div class="card card-body shadow-sm">
    @include('tim.anggota._form', ['u' => null, 'aksi' => route('tim.anggota.store', $tim->slug)])
</div></div>
@endif

<div class="row g-5">
    @foreach ($anggota as $a)
        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm h-100 {{ $a->is_active ? '' : 'opacity-50' }}"><div class="card-body">
                <div class="d-flex gap-3 align-items-center mb-3">
                    <span class="symbol symbol-45px symbol-circle"><span class="symbol-label fw-bold text-white" style="background:var(--tp-primary)">{{ $a->initials }}</span></span>
                    <div class="min-w-0"><div class="fw-bold text-truncate">{{ $a->name }} @if ($a->id === auth()->id())<span class="text-muted fs-8">(Anda)</span>@endif</div>
                        <div class="text-muted fs-8 text-truncate">{{ $a->email }}</div></div>
                </div>
                <span class="badge {{ $a->isPengelola() ? 'text-gold' : 'badge-light-primary' }}" @if ($a->isPengelola()) style="background:rgba(201,164,76,.15)" @endif>{{ $a->peran?->label() }}</span>
                @unless ($a->is_active)<span class="badge badge-light-danger">Nonaktif</span>@endunless
                <div class="text-muted fs-8 mt-2">{{ $a->peran?->keterangan() }}</div>
                @if ($boleh)
                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-sm btn-light" data-bs-toggle="collapse" data-bs-target="#eA{{ $loop->index }}">Ubah</button>
                        @if ($a->id !== auth()->id())
                            <form method="POST" action="{{ route('tim.anggota.destroy', [$tim->slug, $a]) }}" data-confirm="Hapus akun {{ $a->name }}?">@csrf @method('DELETE')<button class="btn btn-sm btn-light-danger">Hapus</button></form>
                        @endif
                    </div>
                    <div class="collapse mt-4" id="eA{{ $loop->index }}">@include('tim.anggota._form', ['u' => $a, 'aksi' => route('tim.anggota.update', [$tim->slug, $a])])</div>
                @endif
            </div></div>
        </div>
    @endforeach
</div>
@endsection
