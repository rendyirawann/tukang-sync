{{--
    Layout halaman galat yang MANDIRI.

    Sengaja tanpa @vite, tanpa query database, tanpa auth()/session, tanpa
    partial lain: halaman galat sering tampil justru saat bagian-bagian itu
    yang rusak (DB mati, sesi rusak, view lain error). CSS dan logo inline
    supaya tetap tampil walau aset tidak termuat.
--}}
@php
    $appName = config('app.name', 'Tukangsync');
    $home = url('/');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#1E3A5F">
    <title>@yield('code') — @yield('title') | {{ $appName }}</title>
    <style>
        *{box-sizing:border-box}
        html,body{margin:0;height:100%}
        body{font-family:'Plus Jakarta Sans','Segoe UI',system-ui,-apple-system,Arial,sans-serif;
            background:linear-gradient(135deg,#0D1830,#1E3A5F 55%,#16243C);color:#e6ecf5;
            display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px}
        .kartu{width:100%;max-width:520px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);
            border-radius:20px;padding:36px 28px;text-align:center;backdrop-filter:blur(6px)}
        .merek{display:inline-flex;align-items:center;gap:10px;font-weight:800;font-size:20px;color:#fff;text-decoration:none;margin-bottom:24px}
        .merek b{color:#C9A44C}
        .kode{font-size:clamp(64px,16vw,96px);font-weight:800;line-height:1;margin:0 0 8px;color:#C9A44C;letter-spacing:-.04em}
        h1{font-size:22px;margin:0 0 10px;color:#fff}
        p{margin:0 0 24px;line-height:1.6;color:#c8d3e6}
        .aksi{display:flex;gap:10px;justify-content:center;flex-wrap:wrap}
        .btn{display:inline-block;padding:11px 20px;border-radius:10px;font-weight:700;text-decoration:none;font-size:15px;border:0;cursor:pointer}
        .btn-emas{background:#C9A44C;color:#0F1D33}
        .btn-garis{background:transparent;color:#fff;border:1px solid rgba(255,255,255,.35)}
        footer{margin-top:24px;font-size:13px;color:#8fa1bd}
    </style>
</head>
<body>
    <main class="kartu">
        <a href="{{ $home }}" class="merek" aria-label="{{ $appName }}">
            <svg width="40" height="40" viewBox="0 0 100 100" aria-hidden="true">
                <defs><linearGradient id="eg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#0D1830"/><stop offset="1" stop-color="#2B5288"/></linearGradient></defs>
                <rect width="100" height="100" rx="22" fill="url(#eg)" stroke="rgba(255,255,255,.25)"/>
                <path d="M19 39 A33 33 0 0 1 79 33" fill="none" stroke="#C9A44C" stroke-width="6.5" stroke-linecap="round"/>
                <path d="M81 61 A33 33 0 0 1 21 67" fill="none" stroke="#C9A44C" stroke-width="6.5" stroke-linecap="round"/>
                <polygon points="86,28 80,44 72,30" fill="#C9A44C"/><polygon points="14,72 20,56 28,70" fill="#C9A44C"/>
                <path d="M31 56 A19 19 0 0 1 69 56 Z" fill="#fff"/><rect x="23" y="56" width="54" height="6" rx="2" fill="#fff"/>
                <rect x="46.5" y="37.5" width="7" height="18.5" fill="#C9A44C"/>
            </svg>
            <span>Tukang<b>sync</b></span>
        </a>
        <div class="kode">@yield('code')</div>
        <h1>@yield('title')</h1>
        <p>@yield('message')</p>
        <div class="aksi">
            @hasSection('actions')
                @yield('actions')
            @else
                <a href="javascript:history.back()" class="btn btn-garis">Kembali</a>
                <a href="{{ $home }}" class="btn btn-emas">Beranda</a>
            @endif
        </div>
    </main>
    <footer>&copy; {{ now()->year }} {{ $appName }}. All rights reserved.</footer>
</body>
</html>
