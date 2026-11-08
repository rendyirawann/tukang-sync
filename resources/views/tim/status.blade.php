@extends('tim.layout.polos')
@section('title', 'Status Tim '.$tim->slug)

@section('content')
<div class="container py-10" style="max-width:760px">
    <div class="card shadow-sm mb-6">
        <div class="card-body p-10 text-center">
            <div class="text-muted mb-2">Kode tim Anda</div>
            <div class="fw-bolder font-monospace mb-3" style="font-size:3.5rem;letter-spacing:.25em;color:var(--tp-primary)">{{ $tim->slug }}</div>
            <div class="fw-semibold fs-4 mb-3">{{ $tim->nama }}</div>
            <span class="badge badge-light-{{ $tim->status->warna() }} fs-6">{{ $tim->status->label() }}</span>
            <p class="text-muted mt-4 mb-0">Simpan kode ini. Halaman login tim Anda: <a href="{{ route('tim.login', $tim->slug) }}">{{ route('tim.login', $tim->slug) }}</a></p>
        </div>
    </div>

    @if ($tim->isAktif() && ! $bayar)
        <div class="card shadow-sm"><div class="card-body p-8 text-center">
            <h3 class="fw-bold">Tim sudah aktif 🎉</h3>
            <p class="text-muted">Masa berlaku: {{ $tim->masaBerlakuLabel() }}</p>
            <a href="{{ route('tim.login', $tim->slug) }}" class="btn btn-primary btn-lg">Masuk ke panel tim</a>
        </div></div>
    @elseif ($tim->status->value === 'ditangguhkan')
        <div class="alert alert-danger">Akses tim ini sedang ditangguhkan. Silakan hubungi admin.</div>
    @endif

    @if ($bayar)
        <div class="card shadow-sm mb-6"><div class="card-body p-8">
            <h4 class="fw-bold mb-5">Tagihan {{ $bayar->jenisLabel() }}</h4>
            <table class="table table-row-dashed mb-0">
                <tr><td class="text-muted">Invoice</td><td class="fw-bold font-monospace">{{ $bayar->kode }}</td></tr>
                <tr><td class="text-muted">Paket</td><td class="fw-bold">{{ $bayar->plan?->nama }}</td></tr>
                <tr><td class="text-muted">Nominal</td><td class="fw-bolder fs-3" style="color:var(--tp-primary)">{{ $bayar->nominalLabel() }}</td></tr>
            </table>
        </div></div>

        <div class="card shadow-sm mb-6"><div class="card-body p-8">
            <h4 class="fw-bold mb-5">Transfer ke salah satu rekening</h4>
            @forelse ($rekening as $r)
                <div class="d-flex justify-content-between align-items-center border rounded p-4 mb-3">
                    <div><div class="fw-bold">{{ $r->bank }}</div>
                        <div class="fs-4 fw-bolder font-monospace">{{ $r->nomor }}</div>
                        <div class="text-muted fs-7">a.n. {{ $r->atas_nama }}</div></div>
                    <button type="button" class="btn btn-sm btn-light" data-copy="{{ $r->nomor }}">Salin</button>
                </div>
            @empty
                <p class="text-muted">Nomor rekening akan diberikan admin lewat WhatsApp.</p>
            @endforelse
        </div></div>

        @if ($linkWa)
            <div class="card shadow-sm"><div class="card-body p-8 text-center">
                <p class="mb-5">Setelah transfer, kirim bukti ke WhatsApp admin{{ $kontak ? ' ('.$kontak->nama.')' : '' }}. Akun aktif setelah admin mengonfirmasi.</p>
                <a href="{{ $linkWa }}" id="tombolWa" target="_blank" rel="noopener" class="btn btn-lg btn-success">Konfirmasi via WhatsApp</a>
                @if ($baru)<div class="text-muted fs-7 mt-3" id="infoWa">Membuka WhatsApp otomatis…</div>@endif
            </div></div>
        @endif
    @endif
</div>
@endsection

@if ($baru && $linkWa)
@push('scripts')
<script>
    setTimeout(function () { window.location.href = @json($linkWa); }, 1500);
</script>
@endpush
@endif
