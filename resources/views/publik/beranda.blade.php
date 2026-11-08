{{--
    Beranda publik Tukangsync.

    Halaman mandiri (tidak memakai layout Metronic) dengan Tailwind v4 yang
    dikompilasi ke public/assets/css/beranda.css. Setelah mengubah kelas di
    berkas ini jalankan: npm run build:beranda

    Data pratinjau dan kalkulator hanyalah CONTOH tampilan; angka kalkulator
    memakai koefisien analisa bawaan (App\Services\Saas\KatalogAwal) dan
    harga contoh katalog, bukan harga daerah pelanggan.
--}}
@section('meta_description', config('app.description'))

@section('json_ld')
@php
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'url' => url('/'),
                'logo' => asset('assets/media/branding/android-chrome-512x512.png'),
            ],
            [
                '@type' => 'WebSite',
                'name' => config('app.name'),
                'url' => url('/'),
                'inLanguage' => 'id-ID',
                'description' => config('app.description'),
            ],
            [
                '@type' => 'WebApplication',
                'name' => config('app.name'),
                'url' => url('/'),
                'applicationCategory' => 'BusinessApplication',
                'operatingSystem' => 'Web, Android',
                'description' => config('app.description'),
                'offers' => $plans->map(fn ($p) => [
                    '@type' => 'Offer',
                    'name' => $p->nama,
                    'price' => (string) $p->harga,
                    'priceCurrency' => 'IDR',
                ])->values()->all(),
            ],
        ],
    ];
@endphp
<script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endsection
@php
    $logo = asset('assets/media/branding/logo-icon.svg').'?v='.@filemtime(public_path('assets/media/branding/logo-icon.svg'));
    $css = asset('assets/css/beranda.css').'?v='.@filemtime(public_path('assets/css/beranda.css'));
    $unggulan = $plans->firstWhere('durasi_bulan', null);
    $centang = '<svg class="mt-0.5 size-4 shrink-0 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.meta')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="{{ $css }}">
</head>
<body class="overflow-x-hidden bg-white font-sans text-slate-900 selection:bg-navy-600 selection:text-white">

<a href="#utama" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:shadow">Lewati ke konten</a>

{{-- ============ NAVBAR ============ --}}
<header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/85 backdrop-blur-md">
    <div class="mx-auto flex h-16 max-w-full items-center justify-between gap-4 px-5 sm:px-8">
        <a href="{{ route('beranda') }}" class="flex items-center gap-2.5">
            <img src="{{ $logo }}" alt="{{ config('app.name') }}" width="34" height="34" class="size-[34px] rounded-lg">
            <span class="text-lg font-extrabold tracking-tight text-navy-900">Tukang<span class="text-gold-600">sync</span></span>
        </a>

        <nav class="hidden items-center gap-8 text-sm font-medium text-slate-600 md:flex" aria-label="Navigasi utama">
            <a href="#fitur" class="transition-colors hover:text-navy-900">Fitur</a>
            <a href="#kalkulator" class="transition-colors hover:text-navy-900">Simulasi RAB</a>
            <a href="#harga" class="transition-colors hover:text-navy-900">Harga</a>
        </nav>

        <div class="flex items-center gap-2">
            <a href="#masuk" class="hidden rounded-lg px-4 py-2 text-sm font-semibold text-slate-700 transition-colors hover:bg-slate-100 hover:text-navy-900 sm:inline-flex">Masuk</a>
            <a href="{{ route('daftar', ['paket' => $unggulan?->kode]) }}" class="inline-flex rounded-lg bg-navy-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-navy-700">Daftar</a>
            <button type="button" id="tsMenuBtn" class="inline-flex size-9 items-center justify-center rounded-lg text-slate-600 hover:bg-slate-100 md:hidden" aria-label="Buka menu" aria-expanded="false" aria-controls="tsMenu">
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
            </button>
        </div>
    </div>
    <nav id="tsMenu" class="hidden border-t border-slate-200 bg-white px-5 py-3 md:hidden" aria-label="Navigasi seluler">
        <a href="#fitur" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Fitur</a>
        <a href="#kalkulator" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Simulasi RAB</a>
        <a href="#harga" class="block rounded-lg px-3 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">Harga</a>
        <a href="#masuk" class="block rounded-lg px-3 py-2.5 text-sm font-semibold text-navy-700 hover:bg-slate-50">Masuk dengan kode tim</a>
    </nav>
</header>

<main id="utama">

{{-- ============ HERO ============ --}}
<section class="relative overflow-hidden border-b border-slate-200">
    <div class="pointer-events-none absolute inset-0 -z-10 bg-[radial-gradient(60rem_30rem_at_85%_-10%,var(--color-navy-50),transparent),radial-gradient(40rem_24rem_at_0%_110%,var(--color-gold-50),transparent)]" aria-hidden="true"></div>

    <div class="mx-auto grid max-w-full grid-cols-1 items-center gap-12 px-5 py-14 sm:px-8 lg:grid-cols-12 lg:gap-10 lg:py-20">

        {{-- Kiri: proposisi nilai + akses tim --}}
        <div class="min-w-0 space-y-8 lg:col-span-5">
            <span class="inline-flex items-center gap-2 rounded-full border border-gold-100 bg-gold-50 px-3 py-1.5 text-xs font-semibold text-gold-700">
                <span class="size-1.5 rounded-full bg-gold-500"></span>
                Untuk kontraktor, mandor & pemilik proyek
            </span>

            <div class="space-y-5">
                <h1 class="text-[2rem] font-extrabold leading-[1.12] tracking-tight text-navy-900 sm:text-5xl">
                    Catat proyek, susun RAB, dan pantau biaya konstruksi
                    <span class="relative text-navy-600 sm:whitespace-nowrap">
                        <span class="relative z-10">dari HP maupun web.</span>
                        <span class="absolute inset-x-0 bottom-1 -z-0 h-3 rounded bg-gold-100" aria-hidden="true"></span>
                    </span>
                </h1>
                <p class="text-base leading-relaxed text-slate-600 sm:text-lg">
                    Hitung kebutuhan material per bangunan, lantai, dan ruangan secara otomatis. Pantau progres lapangan, belanja, dan bagi hasil dalam satu tim.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('daftar', ['paket' => $unggulan?->kode]) }}" class="inline-flex items-center gap-2 rounded-xl bg-navy-600 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-navy-600/20 transition hover:-translate-y-0.5 hover:bg-navy-700">
                    Daftar sekarang
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                <a href="#harga" class="inline-flex rounded-xl border border-slate-300 bg-white px-6 py-3.5 text-sm font-semibold text-navy-900 transition hover:border-slate-400 hover:bg-slate-50">Lihat paket</a>
            </div>

            {{-- Masuk dengan kode tim --}}
            <div id="masuk" class="scroll-mt-24 space-y-3 rounded-2xl border border-slate-200 bg-white/80 p-5 shadow-sm backdrop-blur">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-bold text-navy-900">Sudah punya tim?</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Masukkan kode tim 4 karakter yang Anda terima saat mendaftar.</p>
                    </div>
                    <span class="shrink-0 rounded-md border border-navy-100 bg-navy-50 px-2.5 py-0.5 text-[11px] font-semibold text-navy-600">Web & Android</span>
                </div>

                <form id="tsKodeForm" class="flex gap-2" novalidate>
                    <label for="tsKode" class="sr-only">Kode tim</label>
                    <input id="tsKode" type="text" inputmode="text" autocomplete="off" maxlength="6" placeholder="Mis. K7QX" required
                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-bold uppercase tracking-[0.2em] text-navy-900 placeholder:font-normal placeholder:tracking-normal placeholder:text-slate-400 focus:border-navy-600 focus:outline-none focus:ring-2 focus:ring-navy-600/15">
                    <button type="submit" class="shrink-0 rounded-xl bg-navy-900 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-navy-700">Masuk</button>
                </form>
                <p id="tsKodeGalat" class="hidden text-xs font-medium text-red-600" role="alert">Kode tim berisi 4–6 huruf atau angka.</p>
            </div>
        </div>

        {{-- Kanan: pratinjau aplikasi --}}
        <div class="min-w-0 lg:col-span-7">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl shadow-navy-900/10 ring-1 ring-slate-900/5">

                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-slate-50 px-5 py-3.5">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="flex items-center gap-1.5" aria-hidden="true">
                            <span class="size-2.5 rounded-full bg-slate-300"></span><span class="size-2.5 rounded-full bg-slate-300"></span><span class="size-2.5 rounded-full bg-slate-300"></span>
                        </div>
                        <span class="h-4 w-px bg-slate-200"></span>
                        <span class="rounded-md bg-navy-600 px-2 py-0.5 text-xs font-bold tabular-nums text-white">#DEMO</span>
                        <span id="uiProyek" class="truncate text-xs font-bold text-navy-900 sm:text-sm">Rumah Tinggal 2 Lantai</span>
                    </div>
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200/70 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                        <span class="size-1.5 animate-pulse rounded-full bg-emerald-500"></span> Tersinkron
                    </span>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-3">
                    <div class="flex max-w-full items-center gap-1 overflow-x-auto rounded-xl bg-slate-100 p-1" role="tablist" aria-label="Contoh proyek">
                        <button type="button" class="ts-proyek rounded-lg px-3 py-1.5 text-xs font-semibold transition-all" data-proyek="rumah">Rumah 2 Lantai</button>
                        <button type="button" class="ts-proyek rounded-lg px-3 py-1.5 text-xs font-semibold transition-all" data-proyek="ruko">Ruko 3 Lantai</button>
                        <button type="button" class="ts-proyek rounded-lg px-3 py-1.5 text-xs font-semibold transition-all" data-proyek="renovasi">Renovasi</button>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-medium text-slate-500">
                        Progres fisik
                        <span class="h-1.5 w-20 overflow-hidden rounded-full bg-slate-200"><span id="uiBar" class="block h-full rounded-full bg-gold-500 transition-all duration-500" style="width:44%"></span></span>
                        <strong id="uiProgres" class="tabular-nums text-navy-900">44%</strong>
                    </div>
                </div>

                <div class="grid grid-cols-3 divide-x divide-slate-200 border-b border-slate-200 bg-slate-50/50">
                    <div class="p-4"><div class="text-[11px] font-medium text-slate-500 sm:text-xs">Total RAB</div><div id="uiRab" class="mt-0.5 text-sm font-extrabold tabular-nums text-navy-900 sm:text-lg">Rp 96.540.808</div></div>
                    <div class="p-4"><div class="text-[11px] font-medium text-slate-500 sm:text-xs">Realisasi belanja</div><div id="uiReal" class="mt-0.5 text-sm font-extrabold tabular-nums text-navy-600 sm:text-lg">Rp 41.250.000</div></div>
                    <div class="p-4"><div class="text-[11px] font-medium text-slate-500 sm:text-xs">Pagu proyek</div><div id="uiPagu" class="mt-0.5 text-sm font-extrabold tabular-nums text-navy-900 sm:text-lg">Rp 450.000.000</div></div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[560px] border-collapse text-left">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/80 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                                <th class="px-5 py-3">Lokasi & pekerjaan</th>
                                <th class="px-4 py-3">Volume</th>
                                <th class="px-4 py-3">Kebutuhan otomatis</th>
                                <th class="px-5 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody id="uiTabel" class="divide-y divide-slate-100 text-xs sm:text-sm"></tbody>
                    </table>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-200 bg-slate-50 px-5 py-3 text-xs text-slate-600">
                    <div class="flex items-center gap-2"><span class="size-2 rounded-full bg-gold-500"></span><span id="uiLog">Mandor (Android): kolom Lantai 1 selesai dicor 100%</span></div>
                    <span class="font-semibold text-navy-900">Contoh tampilan</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ FITUR ============ --}}
<section id="fitur" class="scroll-mt-16 border-b border-slate-200 bg-slate-50/70 py-20">
    <div class="mx-auto max-w-full px-5 sm:px-8">
        <div class="mb-14 max-w-2xl">
            <p class="mb-2 text-xs font-bold uppercase tracking-wider text-gold-600">Fitur lengkap</p>
            <h2 class="text-2xl font-extrabold tracking-tight text-navy-900 sm:text-4xl">Semua yang dibutuhkan untuk mencatat proyek</h2>
            <p class="mt-3 text-base text-slate-600">Mudah dipakai di lapangan lewat HP Android, tetap rinci saat dibuka di komputer kantor.</p>
        </div>

        @php
            $fitur = [
                ['RAB per bangunan, lantai & ruangan', 'Susun anggaran rinci: pondasi, kolom, dinding, pintu, jendela, atap, sampai titik lampu.', 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'rab'],
                ['Kebutuhan material otomatis', 'Pilih analisa harga satuan, volume dihitung dari dimensi — semen, bata, dan besi keluar sendiri.', 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'material'],
                ['Progres & log harian', 'Mandor melaporkan progres dan catatan lapangan setiap hari langsung dari HP.', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'progres'],
                ['Arus kas & bagi hasil', 'Catat belanja, upah, dan termin. Bandingkan rencana dengan realisasi.', 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'kas'],
                ['Aplikasi Android + web', 'Satu akun untuk aplikasi HP dan panel web tim. Data tersinkron otomatis.', 'M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z', 'sync'],
                ['Hingga 4 akun per tim', 'Owner, kontraktor, mandor, anggota, atau viewer untuk klien.', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'tim'],
            ];
        @endphp

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($fitur as [$judul, $isi, $ikon, $jenis])
                <article class="ts-reveal group flex flex-col justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-navy-200 hover:shadow-lg hover:shadow-navy-900/5">
                    <div>
                        <div class="mb-5 flex size-11 items-center justify-center rounded-xl bg-navy-600 text-gold-300 shadow-md shadow-navy-600/20">
                            <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $ikon }}"/></svg>
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-navy-900">{{ $judul }}</h3>
                        <p class="text-sm leading-relaxed text-slate-600">{{ $isi }}</p>
                    </div>

                    <div class="mt-6 border-t border-slate-100 pt-4 text-xs">
                        @switch($jenis)
                            @case('rab')
                                <div class="space-y-2 tabular-nums">
                                    <div class="flex items-center justify-between rounded-lg border border-slate-200/70 bg-slate-50 px-3 py-2"><span class="font-semibold text-navy-900">Lantai 1 · Ruang Tamu</span><span class="font-bold text-navy-600">Rp 15.576.250</span></div>
                                    <div class="flex items-center justify-between rounded-lg bg-slate-50/60 px-3 py-2 text-slate-600"><span>Pondasi batu kali</span><span class="font-semibold text-navy-900">Rp 24.250.752</span></div>
                                </div>
                                @break
                            @case('material')
                                <div class="space-y-2 rounded-lg border border-slate-200/70 bg-slate-50 p-3">
                                    <div class="flex justify-between text-slate-500"><span>Dinding bata <strong class="text-navy-900">10 × 3,5 m (35 m²)</strong></span><span class="font-semibold text-gold-700">Otomatis</span></div>
                                    <div class="grid grid-cols-3 gap-2 pt-1 text-center tabular-nums">
                                        <div class="rounded border border-slate-200/80 bg-white p-2"><div class="text-[10px] text-slate-500">Semen</div><div class="font-bold text-navy-900">8,1 zak</div></div>
                                        <div class="rounded border border-slate-200/80 bg-white p-2"><div class="text-[10px] text-slate-500">Bata merah</div><div class="font-bold text-navy-900">2.450 bh</div></div>
                                        <div class="rounded border border-slate-200/80 bg-white p-2"><div class="text-[10px] text-slate-500">Pasir</div><div class="font-bold text-navy-900">1,5 m³</div></div>
                                    </div>
                                </div>
                                @break
                            @case('progres')
                                <div class="space-y-2 rounded-lg border border-slate-200/70 bg-slate-50 p-3">
                                    <div class="flex items-center justify-between"><span class="font-semibold text-navy-900">Pemasangan rangka atap</span><span class="font-bold tabular-nums text-navy-600">75%</span></div>
                                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200"><div class="h-full w-3/4 rounded-full bg-gold-500"></div></div>
                                    <div class="text-[11px] text-slate-500">Dilaporkan Mandor Agus · hari ini 16.20</div>
                                </div>
                                @break
                            @case('kas')
                                <div class="grid grid-cols-2 gap-2 tabular-nums">
                                    <div class="rounded-lg border border-slate-200/70 bg-slate-50 p-3"><div class="text-[11px] text-slate-500">Termin masuk</div><div class="mt-0.5 font-bold text-navy-900">Rp 250.000.000</div></div>
                                    <div class="rounded-lg border border-slate-200/70 bg-slate-50 p-3"><div class="text-[11px] text-slate-500">Realisasi belanja</div><div class="mt-0.5 font-bold text-navy-600">Rp 184.300.000</div></div>
                                </div>
                                @break
                            @case('sync')
                                <div class="flex items-center justify-between rounded-lg border border-slate-200/70 bg-slate-50 px-3 py-2.5">
                                    <div class="flex items-center gap-2"><span class="size-2 rounded-full bg-emerald-500"></span><span class="font-semibold text-navy-900">HP Android & panel web</span></div>
                                    <span class="font-medium text-slate-500">1 akun</span>
                                </div>
                                @break
                            @default
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach (['Owner', 'Kontraktor', 'Mandor', 'Anggota'] as $p)
                                        <span class="rounded-md bg-slate-100 px-2.5 py-1 font-semibold text-navy-900">{{ $p }}</span>
                                    @endforeach
                                    <span class="rounded-md bg-gold-50 px-2.5 py-1 font-semibold text-gold-700">Viewer klien</span>
                                </div>
                        @endswitch
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ KALKULATOR ============ --}}
<section id="kalkulator" class="scroll-mt-16 border-b border-slate-200 bg-white py-20">
    <div class="mx-auto grid max-w-full grid-cols-1 items-center gap-10 px-5 sm:px-8 lg:grid-cols-12">
        <div class="min-w-0 space-y-4 lg:col-span-5">
            <p class="text-xs font-bold uppercase tracking-wider text-gold-600">Simulasi perhitungan otomatis</p>
            <h2 class="text-2xl font-extrabold tracking-tight text-navy-900 sm:text-4xl">Dari ukuran langsung jadi daftar belanja material.</h2>
            <p class="text-base leading-relaxed text-slate-600">
                Tidak perlu menghitung koefisien manual di lembar kerja. Pilih jenis pekerjaan, geser ukurannya, dan lihat volume, material, serta perkiraan biayanya.
            </p>
            <ul class="flex flex-col gap-2.5 pt-2 text-sm text-slate-700">
                <li class="flex items-start gap-2.5">{!! $centang !!}<span>Koefisien mengikuti analisa harga satuan (AHSP) yang lazim</span></li>
                <li class="flex items-start gap-2.5">{!! $centang !!}<span>Harga material & upah bisa disesuaikan per tim</span></li>
                <li class="flex items-start gap-2.5">{!! $centang !!}<span>Ekspor PDF siap dikirim ke pemilik proyek</span></li>
            </ul>
        </div>

        <div class="min-w-0 lg:col-span-7">
            <div class="ts-reveal space-y-6 rounded-2xl border border-slate-200 bg-slate-50/80 p-6 sm:p-8">
                <div class="flex flex-wrap gap-2" role="tablist" aria-label="Jenis pekerjaan">
                    <button type="button" class="ts-hitung rounded-xl px-4 py-2 text-xs font-semibold transition-colors sm:text-sm" data-jenis="dinding">Dinding bata ½ bata</button>
                    <button type="button" class="ts-hitung rounded-xl px-4 py-2 text-xs font-semibold transition-colors sm:text-sm" data-jenis="beton">Plat beton bertulang</button>
                    <button type="button" class="ts-hitung rounded-xl px-4 py-2 text-xs font-semibold transition-colors sm:text-sm" data-jenis="keramik">Lantai keramik 40×40</button>
                </div>

                <div class="grid grid-cols-1 gap-6 rounded-xl border border-slate-200 bg-white p-5 sm:grid-cols-2">
                    <div>
                        <div class="mb-2 flex justify-between text-xs font-semibold"><label for="tsS1" id="tsL1" class="text-slate-500">Panjang dinding</label><span id="tsV1" class="font-bold tabular-nums text-navy-900">12,0 m</span></div>
                        <input type="range" id="tsS1" class="ts-range" min="1" max="30" step="0.5" value="12">
                    </div>
                    <div>
                        <div class="mb-2 flex justify-between text-xs font-semibold"><label for="tsS2" id="tsL2" class="text-slate-500">Tinggi dinding</label><span id="tsV2" class="font-bold tabular-nums text-navy-900">3,5 m</span></div>
                        <input type="range" id="tsS2" class="ts-range" min="1" max="12" step="0.5" value="3.5">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 tabular-nums sm:grid-cols-4" aria-live="polite">
                    <div class="rounded-xl border border-slate-200 bg-white p-4"><div id="tsO1L" class="text-xs text-slate-500">Luas</div><div id="tsO1" class="mt-1 text-lg font-extrabold text-navy-900">–</div></div>
                    <div class="rounded-xl border border-slate-200 bg-white p-4"><div id="tsO2L" class="text-xs text-slate-500">Semen</div><div id="tsO2" class="mt-1 text-lg font-extrabold text-navy-900">–</div></div>
                    <div class="rounded-xl border border-slate-200 bg-white p-4"><div id="tsO3L" class="text-xs text-slate-500">Bata</div><div id="tsO3" class="mt-1 text-lg font-extrabold text-navy-900">–</div></div>
                    <div class="rounded-xl bg-gradient-to-br from-navy-600 to-navy-900 p-4 text-white shadow-lg shadow-navy-900/20"><div class="text-xs text-navy-100">Estimasi RAB</div><div id="tsTotal" class="mt-1 whitespace-nowrap text-base font-extrabold text-gold-300 sm:text-lg">–</div></div>
                </div>
                <p class="text-[11px] text-slate-500">Perkiraan memakai harga contoh termasuk upah. Di aplikasi, harga mengikuti katalog tim Anda.</p>
            </div>
        </div>
    </div>
</section>

{{-- ============ HARGA ============ --}}
<section id="harga" class="scroll-mt-16 border-b border-slate-200 bg-slate-50/70 py-20">
    <div class="mx-auto max-w-full px-5 sm:px-8">
        <div class="mx-auto mb-12 max-w-xl text-center">
            <p class="mb-2 text-xs font-bold uppercase tracking-wider text-gold-600">Harga</p>
            <h2 class="text-2xl font-extrabold tracking-tight text-navy-900 sm:text-4xl">Pilih paket</h2>
            <p class="mt-2 text-base text-slate-600">Satu harga transparan untuk satu tim. Tanpa biaya tambahan per proyek.</p>
        </div>

        <div class="mx-auto grid max-w-4xl grid-cols-1 items-stretch gap-6 md:grid-cols-2">
            @forelse ($plans as $plan)
                @php $utama = $unggulan && $plan->is($unggulan); @endphp
                <div @class([
                    'ts-reveal relative flex flex-col justify-between rounded-2xl bg-white p-8',
                    'border-2 border-navy-600 shadow-xl shadow-navy-900/10' => $utama,
                    'border border-slate-200 shadow-sm' => ! $utama,
                ])>
                    <div>
                        <div class="mb-2 flex items-center justify-between gap-2">
                            <h3 class="text-lg font-bold text-navy-900">{{ $plan->nama }}</h3>
                            @if ($utama)
                                <span class="rounded-full border border-gold-300 bg-gold-50 px-2.5 py-1 text-xs font-bold text-gold-700">Paling hemat</span>
                            @endif
                        </div>
                        <div class="mt-3 text-4xl font-extrabold tracking-tight tabular-nums text-navy-900">{{ $plan->hargaLabel() }}</div>
                        <p class="mt-1 text-xs text-slate-500">{{ $plan->periodeLabel() }}</p>
                        @if ($plan->deskripsi)
                            <p class="mt-4 text-sm text-slate-600">{{ $plan->deskripsi }}</p>
                        @endif

                        <ul class="mt-6 space-y-3.5 text-sm text-slate-700">
                            @foreach (collect($plan->fitur ?? [])->push('Maksimal '.$plan->max_users.' akun tim')->unique() as $item)
                                <li class="flex items-start gap-3">{!! $centang !!}<span>{{ $item }}</span></li>
                            @endforeach
                        </ul>
                    </div>

                    <a href="{{ route('daftar', ['paket' => $plan->kode]) }}" @class([
                        'mt-8 block w-full rounded-xl px-5 py-3.5 text-center text-sm font-semibold transition-colors',
                        'bg-navy-600 text-white shadow-md shadow-navy-600/20 hover:bg-navy-700' => $utama,
                        'bg-navy-900 text-white hover:bg-navy-700' => ! $utama,
                    ])>Daftar paket {{ $plan->nama }}</a>
                </div>
            @empty
                <p class="text-center text-slate-500 md:col-span-2">Paket belum tersedia. Silakan hubungi admin.</p>
            @endforelse
        </div>

        <p class="mt-8 text-center text-xs text-slate-500">Pembayaran lewat transfer bank, dikonfirmasi admin melalui WhatsApp. Setelah aktif, tim Anda langsung bisa masuk.</p>
    </div>
</section>

{{-- ============ CTA ============ --}}
<section class="bg-gradient-to-br from-navy-900 via-navy-600 to-navy-800 py-16 text-white">
    <div class="mx-auto flex max-w-full flex-col items-start justify-between gap-6 px-5 sm:px-8 md:flex-row md:items-center">
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight sm:text-3xl">Mulai catat proyek pertama Anda hari ini.</h2>
            <p class="mt-2 text-navy-100">Daftar dalam dua menit, dapatkan kode tim, dan ajak mandor Anda bergabung.</p>
        </div>
        <a href="{{ route('daftar', ['paket' => $unggulan?->kode]) }}" class="shrink-0 rounded-xl bg-gold-500 px-6 py-3.5 text-sm font-bold text-navy-900 shadow-lg shadow-black/20 transition hover:-translate-y-0.5 hover:bg-gold-300">Daftar sekarang</a>
    </div>
</section>

</main>

{{-- ============ FOOTER ============ --}}
<footer class="bg-white py-10">
    <div class="mx-auto flex max-w-full flex-col items-center justify-between gap-4 px-5 text-xs text-slate-500 sm:flex-row sm:px-8">
        <div class="flex items-center gap-2">
            <img src="{{ $logo }}" alt="" width="20" height="20" class="size-5 rounded" loading="lazy">
            <span>&copy; {{ now()->year }} {{ config('app.name') }}. All rights reserved.</span>
        </div>
        <nav class="flex items-center gap-6 font-medium" aria-label="Tautan bawah">
            <a href="#fitur" class="hover:text-navy-900">Fitur</a>
            <a href="#kalkulator" class="hover:text-navy-900">Simulasi RAB</a>
            <a href="#harga" class="hover:text-navy-900">Harga</a>
            <a href="#masuk" class="font-semibold text-navy-600 hover:underline">Masuk</a>
        </nav>
    </div>
</footer>

<script>
(function () {
    'use strict';

    var $ = function (id) { return document.getElementById(id); };
    var rp = function (n) { return 'Rp ' + Math.round(n).toLocaleString('id-ID'); };
    var angka = function (n, d) { return n.toLocaleString('id-ID', { minimumFractionDigits: d, maximumFractionDigits: d }); };

    // ---- Menu seluler ----
    var menuBtn = $('tsMenuBtn'), menu = $('tsMenu');
    menuBtn.addEventListener('click', function () {
        var buka = menu.classList.toggle('hidden') === false;
        menuBtn.setAttribute('aria-expanded', buka ? 'true' : 'false');
    });
    menu.querySelectorAll('a').forEach(function (a) { a.addEventListener('click', function () { menu.classList.add('hidden'); }); });

    // ---- Masuk dengan kode tim -> /{KODE}/login ----
    $('tsKodeForm').addEventListener('submit', function (e) {
        e.preventDefault();
        var kode = $('tsKode').value.trim().toUpperCase();
        if (!/^[A-Z0-9]{4,6}$/.test(kode)) {
            $('tsKodeGalat').classList.remove('hidden');
            $('tsKode').focus();
            return;
        }
        window.location.href = @json(url('/')) + '/' + encodeURIComponent(kode) + '/login';
    });
    $('tsKode').addEventListener('input', function () { $('tsKodeGalat').classList.add('hidden'); });

    // ---- Pratinjau aplikasi (data contoh) ----
    var proyek = {
        rumah: {
            nama: 'Rumah Tinggal 2 Lantai', progres: 44, rab: 96540808, real: 41250000, pagu: 450000000,
            log: 'Mandor (Android): kolom Lantai 1 selesai dicor 100%',
            baris: [
                ['Umum · Pondasi batu kali keliling', '23,04 m³', '76 zak semen · 27,6 m³ batu kali', 24250752],
                ['Lantai 1 · Kolom K1 20×20', '1,68 m³', '13 zak semen · 252 kg besi', 7844172],
                ['Lantai 1 · Dinding ruang tamu', '52,50 m²', '3.675 bh bata · 13 zak semen', 7693350],
                ['Lantai 1 · Keramik ruang tamu', '14,00 m²', '16 dus keramik · 3 zak semen', 3882900],
                ['Lantai 2 · Atap baja ringan + genteng', '80,00 m²', '140 lbr genteng metal', 19260000]
            ]
        },
        ruko: {
            nama: 'Ruko 3 Lantai', progres: 82, rab: 790000000, real: 615000000, pagu: 850000000,
            log: 'Mandor (Android): kusen aluminium Lantai 2 terpasang',
            baris: [
                ['Umum · Pondasi footplat & sloof', '32,00 m³', '237 zak semen · 4.800 kg besi', 149400000],
                ['Lantai 1 · Dinding area ritel', '210,00 m²', '14.700 bh bata · 48 zak semen', 30770000],
                ['Lantai 2 · Plat lantai beton', '24,00 m³', '178 zak semen · 3.600 kg besi', 112060000],
                ['Lantai 3 · Instalasi listrik', '42 titik', '420 m kabel NYM · 42 saklar', 9576000]
            ]
        },
        renovasi: {
            nama: 'Renovasi Dapur & Kamar Mandi', progres: 35, rab: 38500000, real: 12800000, pagu: 45000000,
            log: 'Mandor (Android): bongkar keramik lama selesai',
            baris: [
                ['Lantai 1 · Keramik dapur baru', '9,00 m²', '10 dus keramik · 2 zak semen', 2496150],
                ['Lantai 1 · Dinding kamar mandi', '18,00 m²', '1.260 bh bata · 5 zak semen', 2637720],
                ['Lantai 1 · Pengecatan ulang', '64,00 m²', '17 kg cat tembok · 7 kg plamir', 3200000]
            ]
        }
    };

    function sel(teks, kelas) { var td = document.createElement('td'); td.className = kelas; td.textContent = teks; return td; }

    function tampilkanProyek(kunci) {
        var p = proyek[kunci];
        $('uiProyek').textContent = p.nama;
        $('uiProgres').textContent = p.progres + '%';
        $('uiBar').style.width = p.progres + '%';
        $('uiRab').textContent = rp(p.rab);
        $('uiReal').textContent = rp(p.real);
        $('uiPagu').textContent = rp(p.pagu);
        $('uiLog').textContent = p.log;

        var tbody = $('uiTabel');
        tbody.textContent = '';
        p.baris.forEach(function (b) {
            var tr = document.createElement('tr');
            tr.className = 'transition-colors hover:bg-slate-50/80';
            tr.appendChild(sel(b[0], 'px-5 py-3.5 font-semibold text-navy-900'));
            tr.appendChild(sel(b[1], 'whitespace-nowrap px-4 py-3.5 tabular-nums text-slate-600'));
            var td = document.createElement('td');
            td.className = 'px-4 py-3.5';
            var chip = document.createElement('span');
            chip.className = 'inline-flex min-w-40 rounded-md bg-slate-100 px-2.5 py-0.5 text-xs font-medium tabular-nums text-slate-700';
            chip.textContent = b[2];
            td.appendChild(chip);
            tr.appendChild(td);
            tr.appendChild(sel(rp(b[3]), 'whitespace-nowrap px-5 py-3.5 text-right font-bold tabular-nums text-navy-900'));
            tbody.appendChild(tr);
        });

        document.querySelectorAll('.ts-proyek').forEach(function (b) {
            var aktif = b.dataset.proyek === kunci;
            b.setAttribute('aria-selected', aktif ? 'true' : 'false');
            b.classList.toggle('bg-white', aktif);
            b.classList.toggle('text-navy-900', aktif);
            b.classList.toggle('shadow-sm', aktif);
            b.classList.toggle('text-slate-600', !aktif);
        });
    }
    document.querySelectorAll('.ts-proyek').forEach(function (b) {
        b.addEventListener('click', function () { tampilkanProyek(b.dataset.proyek); });
    });
    tampilkanProyek('rumah');

    // ---- Kalkulator ----
    // Koefisien sama dengan analisa bawaan aplikasi (KatalogAwal):
    //   dinding ½ bata 1PC:4PP per m²: 70 bh bata, 0,23 zak semen, 0,043 m³ pasir
    //   beton bertulang K-225 per m³: 7,42 zak semen, 150 kg besi
    //   keramik 40×40 per m²: 1,1 dus keramik, 0,21 zak semen
    // Harga satuan = total analisa dengan harga contoh katalog (termasuk upah).
    var jenis = {
        dinding: { l1: 'Panjang dinding', l2: 'Tinggi dinding', max2: 12, hitung: function (a, b) {
            var v = a * b;
            return [['Luas dinding', angka(v, 1) + ' m²'], ['Semen 50 kg', angka(Math.ceil(v * 0.23), 0) + ' zak'], ['Bata merah', angka(Math.ceil(v * 70), 0) + ' bh'], v * 146540];
        }},
        beton: { l1: 'Panjang plat', l2: 'Lebar plat', max2: 12, hitung: function (a, b) {
            var v = a * b * 0.12;
            return [['Volume (tebal 12 cm)', angka(v, 2) + ' m³'], ['Semen 50 kg', angka(Math.ceil(v * 7.42), 0) + ' zak'], ['Besi beton', angka(Math.ceil(v * 150), 0) + ' kg'], v * 4669150];
        }},
        keramik: { l1: 'Panjang ruangan', l2: 'Lebar ruangan', max2: 12, hitung: function (a, b) {
            var v = a * b;
            return [['Luas lantai', angka(v, 1) + ' m²'], ['Semen 50 kg', angka(Math.ceil(v * 0.21), 0) + ' zak'], ['Keramik 40×40', angka(Math.ceil(v * 1.1), 0) + ' dus'], v * 277350];
        }}
    };
    var aktifJenis = 'dinding';

    function hitung() {
        var j = jenis[aktifJenis];
        var a = parseFloat($('tsS1').value), b = parseFloat($('tsS2').value);
        $('tsL1').textContent = j.l1; $('tsL2').textContent = j.l2;
        $('tsV1').textContent = angka(a, 1) + ' m'; $('tsV2').textContent = angka(b, 1) + ' m';
        var h = j.hitung(a, b);
        $('tsO1L').textContent = h[0][0]; $('tsO1').textContent = h[0][1];
        $('tsO2L').textContent = h[1][0]; $('tsO2').textContent = h[1][1];
        $('tsO3L').textContent = h[2][0]; $('tsO3').textContent = h[2][1];
        $('tsTotal').textContent = rp(h[3]);
    }

    document.querySelectorAll('.ts-hitung').forEach(function (b) {
        b.addEventListener('click', function () {
            aktifJenis = b.dataset.jenis;
            document.querySelectorAll('.ts-hitung').forEach(function (x) {
                var on = x === b;
                x.setAttribute('aria-selected', on ? 'true' : 'false');
                x.classList.toggle('bg-navy-600', on);
                x.classList.toggle('text-white', on);
                x.classList.toggle('bg-white', !on);
                x.classList.toggle('border', !on);
                x.classList.toggle('border-slate-200', !on);
                x.classList.toggle('text-slate-700', !on);
            });
            hitung();
        });
    });
    $('tsS1').addEventListener('input', hitung);
    $('tsS2').addEventListener('input', hitung);
    document.querySelector('.ts-hitung[data-jenis="dinding"]').click();

    // ---- Animasi muncul saat digulir ----
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entri) {
            entri.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('is-visible'); io.unobserve(e.target); } });
        }, { threshold: 0.12 });
        document.querySelectorAll('.ts-reveal').forEach(function (el) { io.observe(el); });
    } else {
        document.querySelectorAll('.ts-reveal').forEach(function (el) { el.classList.add('is-visible'); });
    }
})();
</script>
</body>
</html>
