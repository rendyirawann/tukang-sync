@php
    $u = auth()->user();
    $menu = [
        ['tim.dashboard', 'tim.dashboard', 'ki-element-11', 'Dasbor'],
        ['tim.proyek.index', 'tim.proyek.*', 'ki-abstract-26', 'Proyek & RAB'],
        ['tim.material.index', 'tim.material.*', 'ki-cube-2', 'Katalog Material'],
        ['tim.analisa.index', 'tim.analisa.*', 'ki-calculator', 'Analisa Harga'],
        ['tim.anggota.index', 'tim.anggota.*', 'ki-people', 'Anggota Tim'],
        ['tim.langganan.index', 'tim.langganan.*', 'ki-wallet', 'Langganan'],
        ['tim.akun.index', 'tim.akun.*', 'ki-user', 'Akun Saya'],
    ];
    $sisa = $tim->sisaHari();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    @include('tim.layout.head')
    <style>
        body { background: #f3f5f9; }
        [data-bs-theme="dark"] body { background: #0b1220; }
        .tp-side { position: fixed; inset: 0 auto 0 0; width: 250px; background: linear-gradient(180deg, #0F1D33, #16243C); color: #c8d3e6; z-index: 105; display: flex; flex-direction: column; transition: transform .25s; }
        .tp-side .brand { padding: 20px; border-bottom: 1px solid rgba(255,255,255,.07); }
        .tp-side a.nav-i { display: flex; align-items: center; gap: 12px; padding: 11px 20px; color: #c8d3e6; font-weight: 500; border-left: 3px solid transparent; }
        .tp-side a.nav-i i { color: #7f93b5; font-size: 1.35rem; }
        .tp-side a.nav-i:hover { background: rgba(255,255,255,.04); color: #fff; }
        .tp-side a.nav-i.active { background: rgba(201,164,76,.10); color: #fff; border-left-color: var(--tp-gold); }
        .tp-side a.nav-i.active i { color: var(--tp-gold); }
        .tp-main { margin-left: 250px; min-height: 100vh; display: flex; flex-direction: column; }
        .tp-top { background: var(--bs-body-bg); border-bottom: 1px solid var(--bs-gray-200); position: sticky; top: 0; z-index: 100; }
        .tp-overlay { display: none; }
        @media (max-width: 991px) {
            .tp-side { transform: translateX(-100%); }
            .tp-side.open { transform: none; }
            .tp-main { margin-left: 0; }
            .tp-overlay.open { display: block; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 104; }
        }
    </style>
    @stack('stylesheets')
</head>
<body class="app-blank">
    <aside class="tp-side" id="tpSide">
        <div class="brand">
            <div class="d-flex align-items-center gap-2 mb-2">
                <img src="{{ $brand['logo_url'] }}" alt="" width="30" height="30">
                <span class="fw-bold text-white fs-5">Transaksi Pekerjaan</span>
            </div>
            <div class="text-white fw-semibold text-truncate">{{ $tim->nama }}</div>
            <span class="kode-badge fs-8">{{ $tim->slug }}</span>
        </div>
        <nav class="py-3 flex-grow-1 overflow-auto">
            @foreach ($menu as [$route, $pola, $ikon, $label])
                <a href="{{ route($route, $tim->slug) }}" class="nav-i {{ request()->routeIs($pola) ? 'active' : '' }}">
                    <i class="ki-duotone {{ $ikon }}"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span></i>
                    {{ $label }}
                </a>
            @endforeach
        </nav>
        <form method="POST" action="{{ route('tim.logout', $tim->slug) }}" class="p-4">
            @csrf
            <button class="btn btn-sm w-100 btn-outline btn-outline-secondary text-white">Keluar</button>
        </form>
    </aside>
    <div class="tp-overlay" id="tpOverlay"></div>

    <div class="tp-main">
        <header class="tp-top">
            <div class="d-flex align-items-center justify-content-between px-4 px-lg-8 py-3 gap-3">
                <div class="d-flex align-items-center gap-3 min-w-0">
                    <button class="btn btn-icon btn-sm d-lg-none" id="tpToggle" aria-label="Menu">
                        <i class="ki-duotone ki-abstract-14 fs-2"><span class="path1"></span><span class="path2"></span></i>
                    </button>
                    <div class="min-w-0">
                        <div class="fw-bold fs-5 text-truncate">@yield('title', 'Dasbor')</div>
                        <div class="text-muted fs-8 text-truncate">{{ $tim->nama }} · <span class="kode-badge fs-9">{{ $tim->slug }}</span></div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    @if ($tim->hanyaBaca())
                        <a href="{{ route('tim.langganan.index', $tim->slug) }}" class="badge badge-light-danger fs-8 py-2">Masa habis – hanya baca</a>
                    @elseif ($tim->lifetime)
                        <span class="badge badge-light-success fs-8 py-2">Lifetime</span>
                    @else
                        <a href="{{ route('tim.langganan.index', $tim->slug) }}" class="badge {{ $sisa !== null && $sisa <= 7 ? 'badge-light-warning' : 'badge-light-primary' }} fs-8 py-2">Sisa {{ $sisa }} hari</a>
                    @endif
                    <div class="text-end d-none d-sm-block">
                        <div class="fw-semibold fs-7">{{ $u->name }}</div>
                        <div class="text-muted fs-8">{{ $u->roleLabel() }}</div>
                    </div>
                    <a href="{{ route('tim.akun.index', $tim->slug) }}" class="symbol symbol-35px symbol-circle">
                        <span class="symbol-label fw-bold text-white" style="background:var(--tp-primary)">{{ $u->initials }}</span>
                    </a>
                </div>
            </div>
        </header>
        <main class="px-4 px-lg-8 py-6 flex-grow-1">
            @if ($tim->hanyaBaca())
                <div class="alert alert-danger d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span>Masa langganan berakhir {{ $tim->masaBerlakuLabel() }}. Data tetap bisa dilihat, tetapi tidak bisa ditambah atau diubah.</span>
                    <a href="{{ route('tim.langganan.index', $tim->slug) }}" class="btn btn-sm btn-danger">Perpanjang</a>
                </div>
            @endif
            @yield('content')
        </main>
    </div>

    @include('tim.layout.scripts')
    <script>
        (function () {
            var s = document.getElementById('tpSide'), o = document.getElementById('tpOverlay'), t = document.getElementById('tpToggle');
            function tog() { s.classList.toggle('open'); o.classList.toggle('open'); }
            t && t.addEventListener('click', tog); o.addEventListener('click', tog);
        })();
    </script>
</body>
</html>
