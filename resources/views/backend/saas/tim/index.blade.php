@extends('backend.layout.app')
@section('title', 'Tim Pelanggan')
@section('content')
    @include('backend.saas._header', [
        'judul' => 'Tim Pelanggan',
        'sub' => 'Tim',
        'aksi' => '<a href="' . route('saas.tim.create') . '" class="btn btn-sm btn-primary"><i class="ki-outline ki-plus fs-5"></i> Daftarkan tim</a>',
    ])

    <div class="row g-4 mb-6">
        @foreach ([['Total', $ringkasan['total'], 'primary', ''], ['Aktif', $ringkasan['aktif'], 'success', 'aktif'], ['Menunggu bayar', $ringkasan['menunggu'], 'warning', 'menunggu'], ['Masa habis', $ringkasan['habis'], 'danger', 'habis']] as [$l, $n, $w, $s])
            <div class="col-6 col-lg-3">
                <a href="{{ route('saas.tim.index', $s ? ['status' => $s] : []) }}" class="card card-flush h-100">
                    <div class="card-body py-5">
                        <div class="fs-2 fw-bold text-{{ $w }}">{{ $n }}</div>
                        <div class="text-muted fs-7">{{ $l }}</div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card card-flush">
        <div class="card-header py-5">
            <form method="GET" class="d-flex flex-wrap gap-3 w-100">
                <input type="text" name="q" value="{{ $cari }}" class="form-control form-control-sm w-250px"
                    placeholder="Cari nama, kode, email, WA" />
                <select name="status" class="form-select form-select-sm w-200px" onchange="this.form.submit()">
                    @foreach ($filter as $v => $l)
                        <option value="{{ $v }}" @selected($status === $v)>{{ $l }}</option>
                    @endforeach
                </select>
                <button class="btn btn-sm btn-light-primary">Cari</button>
            </form>
        </div>
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table class="table align-middle table-row-dashed fs-6 gy-4">
                    <thead>
                        <tr class="text-start text-muted fw-bold fs-7 text-uppercase gs-0">
                            <th>Kode</th><th>Tim</th><th>Paket</th><th>Status</th><th>Masa aktif</th><th>Akun</th><th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-700">
                        @forelse ($tim as $t)
                            <tr>
                                <td><span class="badge badge-light-dark fs-6 font-monospace">{{ $t->slug }}</span></td>
                                <td>
                                    <a href="{{ route('saas.tim.show', $t->slug) }}" class="text-gray-900 fw-bold text-hover-primary">{{ $t->nama }}</a>
                                    <div class="text-muted fs-7">{{ $t->kota }} {{ $t->no_wa ? '· ' . $t->no_wa : '' }}</div>
                                </td>
                                <td>{{ $t->plan?->nama ?? '-' }}</td>
                                <td>
                                    <span class="badge badge-light-{{ $t->status->warna() }}">{{ $t->status->label() }}</span>
                                    @if ($t->hanyaBaca())
                                        <span class="badge badge-light-danger">Masa habis</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $t->masaBerlakuLabel() }}
                                    @if (! is_null($t->sisaHari()) && $t->isAktif())
                                        <div class="text-muted fs-8">sisa {{ $t->sisaHari() }} hari</div>
                                    @endif
                                </td>
                                <td>{{ $t->users_count }}/{{ $t->max_users }}</td>
                                <td class="text-end">
                                    <a href="{{ route('saas.tim.show', $t->slug) }}" class="btn btn-sm btn-light">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-10">Belum ada tim.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $tim->links() }}
        </div>
    </div>
@endsection
