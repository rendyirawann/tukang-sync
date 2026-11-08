{{-- Judul halaman modul Langganan. Variabel: $judul, $sub (opsional), slot aksi via $aksi (HTML). --}}
<div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-6">
    <div class="min-w-0">
        <h1 class="fs-2 fw-bold text-gray-900 mb-1">{{ $judul }}</h1>
        <div class="text-muted fs-7">
            <a href="{{ route('dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
            &middot; Langganan @isset($sub) &middot; {{ $sub }} @endisset
        </div>
    </div>
    @isset($aksi)
        <div class="d-flex flex-wrap gap-2">{!! $aksi !!}</div>
    @endisset
</div>

@if ($errors->any())
    <div class="alert alert-danger d-flex flex-column">
        <strong class="mb-1">Periksa kembali isian:</strong>
        <ul class="mb-0 ps-5">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif
