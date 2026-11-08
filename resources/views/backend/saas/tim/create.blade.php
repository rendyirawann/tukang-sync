@extends('backend.layout.app')
@section('title', 'Daftarkan Tim')
@section('content')
    @include('backend.saas._header', ['judul' => 'Daftarkan Tim', 'sub' => 'Tim baru'])

    <form method="POST" action="{{ route('saas.tim.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row g-6">
            <div class="col-lg-7">
                <div class="card card-flush mb-6">
                    <div class="card-header"><h3 class="card-title">Data tim</h3></div>
                    <div class="card-body pt-0">
                        <div class="mb-4">
                            <label class="form-label required">Nama tim / usaha</label>
                            <input name="nama_tim" value="{{ old('nama_tim') }}" class="form-control" required />
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Kota</label>
                                <input name="kota" value="{{ old('kota') }}" class="form-control" />
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Alamat</label>
                                <input name="alamat" value="{{ old('alamat') }}" class="form-control" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-flush mb-6">
                    <div class="card-header"><h3 class="card-title">Akun penanggung jawab</h3></div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label required">Nama</label>
                                <input name="nama" value="{{ old('nama') }}" class="form-control" required />
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label required">Peran</label>
                                <select name="peran" class="form-select">
                                    @foreach (\App\Enums\Peran::pendaftar() as $p)
                                        <option value="{{ $p->value }}" @selected(old('peran') === $p->value)>{{ $p->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label required">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control" required />
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label required">No. WhatsApp</label>
                                <input name="no_wa" value="{{ old('no_wa') }}" class="form-control" required />
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label required">Password</label>
                                <input type="text" name="password" class="form-control" autocomplete="new-password" required />
                                <div class="form-text">Minimal 8 karakter, huruf dan angka. Sampaikan ke pelanggan.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card card-flush mb-6">
                    <div class="card-header"><h3 class="card-title">Paket</h3></div>
                    <div class="card-body pt-0">
                        @foreach ($plans as $plan)
                            <label class="d-flex align-items-center gap-4 border rounded p-4 mb-3" style="cursor:pointer">
                                <input type="radio" name="plan_id" value="{{ $plan->id }}" class="form-check-input"
                                    @checked(old('plan_id', $plans->first()->id) == $plan->id) />
                                <span class="flex-grow-1">
                                    <span class="fw-bold d-block">{{ $plan->nama }}</span>
                                    <span class="text-muted fs-7">{{ $plan->periodeLabel() }} · maks {{ $plan->max_users }} akun</span>
                                </span>
                                <span class="fw-bold text-primary">{{ $plan->hargaLabel() }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="card card-flush mb-6">
                    <div class="card-header"><h3 class="card-title">Pembayaran</h3></div>
                    <div class="card-body pt-0">
                        <label class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="langsung_aktif" value="1" @checked(old('langsung_aktif', true)) />
                            <span class="form-check-label">Pembayaran sudah diterima, langsung aktifkan</span>
                        </label>
                        <div class="mb-4">
                            <label class="form-label">Rekening tujuan</label>
                            <select name="bank_account_id" class="form-select">
                                <option value="">-</option>
                                @foreach ($rekening as $r)
                                    <option value="{{ $r->id }}" @selected(old('bank_account_id') == $r->id)>{{ $r->bank }} {{ $r->nomor }} a.n. {{ $r->atas_nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Catatan</label>
                            <input name="catatan" value="{{ old('catatan') }}" class="form-control" />
                        </div>
                        @include('backend.saas._bukti_input', ['id' => 'buat'])
                    </div>
                </div>

                <button class="btn btn-primary w-100">Simpan &amp; buat kode tim</button>
            </div>
        </div>
    </form>
@endsection
