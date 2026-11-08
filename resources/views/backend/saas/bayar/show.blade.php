@extends('backend.layout.app')
@section('title', 'Pembayaran ' . $bayar->kode)
@section('content')
    @php($tim = $bayar->team)
    @include('backend.saas._header', ['judul' => 'Pembayaran ' . $bayar->kode, 'sub' => 'Pembayaran'])

    <div class="row g-6">
        <div class="col-xl-5">
            <div class="card card-flush mb-6">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <span class="fs-1 fw-bold">{{ $bayar->nominalLabel() }}</span>
                        <span class="badge badge-light-{{ $bayar->status->warna() }} fs-6">{{ $bayar->status->label() }}</span>
                    </div>
                    <table class="table table-row-dashed fs-7 mb-0">
                        <tr><td class="text-muted">Jenis</td><td>{{ $bayar->jenisLabel() }}</td></tr>
                        <tr><td class="text-muted">Paket</td><td>{{ $bayar->plan?->nama ?? '-' }} {{ $bayar->plan ? '(' . $bayar->plan->periodeLabel() . ')' : '' }}</td></tr>
                        <tr><td class="text-muted">Tim</td><td>
                            @if ($tim)
                                <a href="{{ route('saas.tim.show', $tim->slug) }}" class="fw-bold">{{ $tim->nama }}</a>
                                <span class="badge badge-light-dark font-monospace">{{ $tim->slug }}</span>
                            @else - @endif
                        </td></tr>
                        <tr><td class="text-muted">Penanggung jawab</td><td>
                            @if ($pemilik)
                                {{ $pemilik->name }} ({{ $pemilik->peran?->label() }})<br>
                                <span class="text-muted">{{ $pemilik->email }}</span>
                                @php($wa = preg_replace('/\D+/', '', (string) ($pemilik->no_wa ?: $tim?->no_wa)))
                                @if ($wa)
                                    <br><a href="https://wa.me/{{ str_starts_with($wa, '0') ? '62' . substr($wa, 1) : $wa }}" target="_blank" rel="noopener" class="btn btn-sm btn-light-success mt-2">
                                        <i class="ki-outline ki-whatsapp fs-5"></i> Chat WhatsApp</a>
                                @endif
                            @else - @endif
                        </td></tr>
                        <tr><td class="text-muted">Rekening</td><td>{{ $bayar->bankAccount ? $bayar->bankAccount->bank . ' ' . $bayar->bankAccount->nomor : '-' }}</td></tr>
                        <tr><td class="text-muted">Dibuat</td><td>{{ $bayar->created_at->translatedFormat('d M Y H:i') }}</td></tr>
                        @if ($bayar->catatan)<tr><td class="text-muted">Catatan</td><td>{{ $bayar->catatan }}</td></tr>@endif
                        @if ($bayar->dikonfirmasi_pada)
                            <tr><td class="text-muted">Diproses</td><td>{{ $bayar->dikonfirmasi_pada->translatedFormat('d M Y H:i') }} oleh {{ $bayar->konfirmator?->name ?? '-' }}</td></tr>
                        @endif
                        @if ($bayar->periode_mulai)
                            <tr><td class="text-muted">Periode</td><td>{{ $bayar->periode_mulai->translatedFormat('d M Y') }} – {{ $bayar->periode_selesai?->translatedFormat('d M Y') ?? 'selamanya' }}</td></tr>
                        @endif
                        @if ($bayar->alasan_tolak)<tr><td class="text-muted">Alasan tolak</td><td class="text-danger">{{ $bayar->alasan_tolak }}</td></tr>@endif
                    </table>
                </div>
            </div>

            @if ($bayar->isMenunggu())
                <form method="POST" action="{{ route('saas.bayar.terima', $bayar) }}" enctype="multipart/form-data" class="card card-flush mb-6 border border-success">
                    @csrf
                    <div class="card-header"><h3 class="card-title text-success">Terima (ACC) pembayaran</h3></div>
                    <div class="card-body pt-0">
                        <p class="text-muted fs-7">Tim akan aktif, masa langganan berjalan, dan owner bisa login di <b>{{ $tim ? route('tim.login', $tim->slug) : '' }}</b>.</p>
                        <div class="mb-4">
                            <label class="form-label">Rekening tujuan</label>
                            <select name="bank_account_id" class="form-select">
                                <option value="">-</option>
                                @foreach ($rekening as $r)
                                    <option value="{{ $r->id }}" @selected($bayar->bank_account_id == $r->id)>{{ $r->bank }} {{ $r->nomor }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4"><label class="form-label">Catatan</label><input name="catatan" class="form-control" /></div>
                        @unless ($bayar->punyaBukti())
                            @include('backend.saas._bukti_input', ['id' => 'acc'])
                        @endunless
                    </div>
                    <div class="card-footer"><button class="btn btn-success w-100" onclick="return confirm('Terima pembayaran dan aktifkan tim?')">Terima &amp; aktifkan</button></div>
                </form>

                <form method="POST" action="{{ route('saas.bayar.tolak', $bayar) }}" class="card card-flush">
                    @csrf
                    <div class="card-body">
                        <label class="form-label">Tolak pembayaran</label>
                        <input name="alasan" class="form-control mb-3" placeholder="Alasan, mis. nominal tidak sesuai" required />
                        <button class="btn btn-light-danger w-100" onclick="return confirm('Tolak pembayaran ini?')">Tolak</button>
                    </div>
                </form>
            @endif
        </div>

        <div class="col-xl-7">
            <div class="card card-flush">
                <div class="card-header"><h3 class="card-title">Bukti transfer</h3></div>
                <div class="card-body pt-0">
                    @if ($bayar->punyaBukti())
                        @if ($bayar->buktiPdf())
                            <embed src="{{ route('saas.bayar.bukti', $bayar) }}" type="application/pdf" class="w-100 rounded border" style="height: 600px" />
                        @else
                            <a href="{{ route('saas.bayar.bukti', $bayar) }}" target="_blank"><img src="{{ route('saas.bayar.bukti', $bayar) }}" alt="Bukti transfer" class="mw-100 rounded border" /></a>
                        @endif
                        <div class="mt-3"><a href="{{ route('saas.bayar.bukti', $bayar) }}" target="_blank" class="btn btn-sm btn-light">Buka di tab baru</a></div>
                    @else
                        <div class="text-muted mb-4">Belum ada bukti transfer.</div>
                    @endif

                    <form method="POST" action="{{ route('saas.bayar.unggah', $bayar) }}" enctype="multipart/form-data" class="border-top pt-5 mt-5">
                        @csrf
                        @include('backend.saas._bukti_input', ['id' => 'unggah'])
                        <button class="btn btn-sm btn-primary mt-4">{{ $bayar->punyaBukti() ? 'Ganti bukti' : 'Simpan bukti' }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
