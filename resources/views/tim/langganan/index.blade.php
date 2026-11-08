@extends('tim.layout.app')
@section('title', 'Langganan')

@section('content')
<div class="row g-5">
    <div class="col-xl-5">
        <div class="card shadow-sm bg-navy text-white mb-5"><div class="card-body p-8">
            <div style="color:#c8d3e6">Paket saat ini</div>
            <div class="fs-2hx fw-bolder">{{ $tim->plan?->nama ?? '-' }}</div>
            <div class="fs-5 text-gold fw-semibold mb-2">{{ $tim->lifetime ? 'Lifetime — tanpa masa berakhir' : 'Berlaku sampai '.$tim->masaBerlakuLabel() }}</div>
            @if (! $tim->lifetime && $tim->sisaHari() !== null)<div style="color:#c8d3e6">Sisa {{ $tim->sisaHari() }} hari</div>@endif
            <div class="mt-4" style="color:#c8d3e6">Kode tim <span class="kode-badge">{{ $tim->slug }}</span> · maks. {{ $tim->max_users }} akun</div>
        </div></div>

        @if ($boleh)
        <div class="card shadow-sm"><div class="card-body p-8">
            <h4 class="fw-bold mb-4">{{ $tim->lifetime ? 'Ganti paket' : 'Perpanjang / ganti paket' }}</h4>
            <form method="POST" action="{{ route('tim.langganan.store', $tim->slug) }}">
                @csrf
                @foreach ($plans as $p)
                    <label class="d-flex align-items-center border rounded p-4 mb-3 gap-3" style="cursor:pointer">
                        <input class="form-check-input" type="radio" name="plan_id" value="{{ $p->id }}" @checked($loop->first) required>
                        <span class="flex-grow-1"><span class="fw-bold d-block">{{ $p->nama }}</span><span class="text-muted fs-7">{{ $p->periodeLabel() }}</span></span>
                        <span class="fw-bolder" style="color:var(--tp-primary)">{{ $p->hargaLabel() }}</span>
                    </label>
                @endforeach
                @if ($rekening->isNotEmpty())
                    <div class="text-muted fs-7 mb-3">Transfer ke: {{ $rekening->map(fn ($r) => $r->bank.' '.$r->nomor.' a.n. '.$r->atas_nama)->implode(' · ') }}</div>
                @endif
                <button class="btn btn-success w-100">Buat tagihan & konfirmasi via WhatsApp</button>
            </form>
        </div></div>
        @else
            <div class="alert alert-info">Hanya owner/kontraktor yang dapat memperpanjang langganan.</div>
        @endif
    </div>
    <div class="col-xl-7">
        <div class="card shadow-sm"><div class="card-header"><h3 class="card-title fw-bold">Riwayat pembayaran</h3></div>
            <div class="card-body pt-0 table-responsive">
                <table class="table table-row-dashed fs-7">
                    <thead><tr class="text-muted fs-8 text-uppercase"><th>Invoice</th><th>Jenis</th><th>Paket</th><th class="text-end">Nominal</th><th>Status</th><th>Periode</th></tr></thead>
                    <tbody>
                    @forelse ($riwayat as $b)
                        <tr><td class="font-monospace">{{ $b->kode }}</td><td>{{ $b->jenisLabel() }}</td><td>{{ $b->plan?->nama }}</td>
                            <td class="text-end fw-bold">{{ $b->nominalLabel() }}</td>
                            <td><span class="badge badge-light-{{ $b->status->warna() }}">{{ $b->status->label() }}</span></td>
                            <td class="text-muted">{{ $b->periode_mulai?->format('d/m/Y') }}{{ $b->periode_selesai ? ' – '.$b->periode_selesai->format('d/m/Y') : ($b->periode_mulai ? ' – selamanya' : '') }}</td></tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-8">Belum ada pembayaran.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div></div>
    </div>
</div>
@endsection
