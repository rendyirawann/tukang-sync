@extends('tim.layout.polos')
@section('title', 'Daftar Tim')

@section('content')
<div class="container py-10" style="max-width:1000px">
    <h1 class="fw-bold mb-2">Daftarkan tim Anda</h1>
    <p class="text-muted mb-8">Setelah mengisi formulir, Anda akan diarahkan ke WhatsApp admin untuk konfirmasi pembayaran. Akun aktif setelah pembayaran dikonfirmasi.</p>

    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('daftar.store') }}">
        @csrf
        <div style="position:absolute;left:-9999px" aria-hidden="true">
            <label>Situs web <input type="text" name="situs_web" tabindex="-1" autocomplete="off"></label>
        </div>
        <div class="row g-8">
            <div class="col-lg-7">
                <div class="card shadow-sm mb-6"><div class="card-body p-8">
                    <h4 class="fw-bold mb-5">Data tim</h4>
                    <div class="mb-4"><label class="form-label required">Nama tim / usaha</label>
                        <input name="nama_tim" value="{{ old('nama_tim') }}" class="form-control" required maxlength="120" placeholder="Mis. CV Bangun Jaya"></div>
                    <div class="row g-4">
                        <div class="col-md-6"><label class="form-label">Kota</label><input name="kota" value="{{ old('kota') }}" class="form-control" maxlength="80"></div>
                        <div class="col-md-6"><label class="form-label">Alamat</label><input name="alamat" value="{{ old('alamat') }}" class="form-control" maxlength="255"></div>
                    </div>
                </div></div>

                <div class="card shadow-sm"><div class="card-body p-8">
                    <h4 class="fw-bold mb-5">Akun penanggung jawab</h4>
                    <div class="mb-4"><label class="form-label required">Peran Anda</label>
                        <div class="d-flex gap-6">
                            @foreach ($peran as $p)
                                <label class="form-check form-check-custom">
                                    <input class="form-check-input" type="radio" name="peran" value="{{ $p->value }}" {{ old('peran', 'owner') === $p->value ? 'checked' : '' }}>
                                    <span class="form-check-label">{{ $p->label() }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="mb-4"><label class="form-label required">Nama lengkap</label><input name="nama" value="{{ old('nama') }}" class="form-control" required maxlength="120"></div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6"><label class="form-label required">Email</label><input type="email" name="email" value="{{ old('email') }}" class="form-control" required></div>
                        <div class="col-md-6"><label class="form-label required">No. WhatsApp</label><input name="no_wa" value="{{ old('no_wa') }}" class="form-control" required placeholder="08xxxxxxxxxx"></div>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6"><label class="form-label required">Password</label><input type="password" name="password" class="form-control" required autocomplete="new-password"></div>
                        <div class="col-md-6"><label class="form-label required">Ulangi password</label><input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password"></div>
                    </div>
                    <div class="form-text">Minimal 8 karakter, berisi huruf dan angka.</div>
                </div></div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm mb-6"><div class="card-body p-8">
                    <h4 class="fw-bold mb-5">Paket</h4>
                    @foreach ($plans as $plan)
                        <label class="d-flex align-items-center border rounded p-4 mb-3 gap-3" style="cursor:pointer">
                            <input class="form-check-input" type="radio" name="plan_id" value="{{ $plan->id }}" {{ (int) old('plan_id', $pilih?->id) === $plan->id ? 'checked' : '' }}>
                            <span class="flex-grow-1"><span class="fw-bold d-block">{{ $plan->nama }}</span><span class="text-muted fs-7">{{ $plan->periodeLabel() }}</span></span>
                            <span class="fw-bolder" style="color:var(--tp-primary)">{{ $plan->hargaLabel() }}</span>
                        </label>
                    @endforeach
                </div></div>

                <div class="card shadow-sm mb-6"><div class="card-body p-8">
                    <h4 class="fw-bold mb-4">Transfer ke rekening</h4>
                    @forelse ($rekening as $r)
                        <div class="border rounded p-4 mb-3">
                            <div class="fw-bold">{{ $r->bank }}</div>
                            <div class="fs-4 fw-bolder font-monospace">{{ $r->nomor }}</div>
                            <div class="text-muted fs-7">a.n. {{ $r->atas_nama }}</div>
                        </div>
                    @empty
                        <p class="text-muted">Rekening akan diinformasikan admin via WhatsApp.</p>
                    @endforelse
                    <p class="text-muted fs-7 mb-0">Kirim bukti transfer ke WhatsApp admin setelah mendaftar, sebutkan kode tim Anda.</p>
                </div></div>

                <button class="btn btn-gold btn-lg w-100">Daftar &amp; konfirmasi via WhatsApp</button>
            </div>
        </div>
    </form>
</div>
@endsection
