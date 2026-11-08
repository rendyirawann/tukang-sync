<!DOCTYPE html>
<html lang="id">
<head>
    @include('tim.layout.head')
    <style>
        body { background: #f4f6fa; }
        .pub-nav { background: var(--tp-dark); }
        .pub-nav a { color: #e9eef7; }
        .hero { background: linear-gradient(135deg, #0D1830, #2B5288 60%, #16243C); color: #fff; }
        .card-plan.populer { border: 2px solid var(--tp-gold); }
    </style>
    @stack('stylesheets')
</head>
<body class="app-blank">
    <nav class="pub-nav py-3">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="{{ route('beranda') }}" class="d-flex align-items-center gap-2 text-white fw-bold fs-4">
                <img src="{{ $brand['logo_url'] }}" alt="" width="32" height="32"> Tukangsync
            </a>
            <div class="d-flex gap-2">
                <a href="{{ route('beranda') }}#masuk" class="btn btn-sm btn-outline btn-outline-light text-white">Masuk</a>
                <a href="{{ route('daftar') }}" class="btn btn-sm btn-gold">Daftar</a>
            </div>
        </div>
    </nav>
    @yield('content')
    <footer class="text-center text-muted py-8 fs-7">&copy; {{ date('Y') }} Tukangsync</footer>
    @include('tim.layout.scripts')
</body>
</html>
