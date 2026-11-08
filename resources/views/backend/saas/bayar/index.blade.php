@extends('backend.layout.app')
@section('title', 'Pembayaran')
@section('content')
    @include('backend.saas._header', ['judul' => 'Pembayaran', 'sub' => 'Konfirmasi transfer'])

    <div class="row g-4 mb-6">
        <div class="col-md-4"><div class="card card-flush"><div class="card-body py-5"><div class="fs-2 fw-bold text-warning">{{ $ringkasan['menunggu'] }}</div><div class="text-muted fs-7">Menunggu konfirmasi</div></div></div></div>
        <div class="col-md-4"><div class="card card-flush"><div class="card-body py-5"><div class="fs-2 fw-bold text-primary">Rp {{ number_format($ringkasan['bulan_ini'], 0, ',', '.') }}</div><div class="text-muted fs-7">Diterima bulan ini</div></div></div></div>
        <div class="col-md-4"><div class="card card-flush"><div class="card-body py-5"><div class="fs-2 fw-bold text-success">Rp {{ number_format($ringkasan['total'], 0, ',', '.') }}</div><div class="text-muted fs-7">Total diterima</div></div></div></div>
    </div>

    <div class="card card-flush">
        <div class="card-header py-5">
            <form method="GET" class="d-flex flex-wrap gap-3 w-100">
                <input type="text" name="q" value="{{ $cari }}" class="form-control form-control-sm w-250px" placeholder="Cari kode / tim" />
                <select name="status" class="form-select form-select-sm w-200px" onchange="this.form.submit()">
                    <option value="semua" @selected($status === 'semua')>Semua</option>
                    @foreach (\App\Enums\StatusPembayaran::cases() as $s)
                        <option value="{{ $s->value }}" @selected($status === $s->value)>{{ $s->label() }}</option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-light-primary">Cari</button>
            </form>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-4">
                    <thead><tr class="text-muted fw-bold fs-7 text-uppercase"><th>Kode</th><th>Tim</th><th>Jenis</th><th>Paket</th><th>Nominal</th><th>Status</th><th>Tanggal</th><th></th></tr></thead>
                    <tbody>
                        @forelse ($bayar as $p)
                            <tr>
                                <td class="fw-bold">{{ $p->kode }}</td>
                                <td>
                                    @if ($p->team)
                                        <span class="badge badge-light-dark font-monospace">{{ $p->team->slug }}</span> {{ $p->team->nama }}
                                    @else - @endif
                                </td>
                                <td>{{ $p->jenisLabel() }}</td>
                                <td>{{ $p->plan?->nama ?? '-' }}</td>
                                <td>{{ $p->nominalLabel() }}</td>
                                <td>
                                    <span class="badge badge-light-{{ $p->status->warna() }}">{{ $p->status->label() }}</span>
                                    @if ($p->bukti_path) <i class="ki-outline ki-paper-clip text-muted" title="Ada bukti"></i> @endif
                                </td>
                                <td class="fs-7">{{ $p->created_at->translatedFormat('d M Y H:i') }}</td>
                                <td class="text-end"><a href="{{ route('saas.bayar.show', $p) }}" class="btn btn-sm btn-light">{{ $p->isMenunggu() ? 'Proses' : 'Detail' }}</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted py-10">Tidak ada pembayaran.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $bayar->links() }}
        </div>
    </div>
@endsection
