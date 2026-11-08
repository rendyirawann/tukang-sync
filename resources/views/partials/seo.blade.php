{{--
    Blok <head> bersama: SEO, sosial, favicon, CSRF.

    Nilai berasal dari App\Support\Brand (pengaturan admin) dengan bawaan dari
    config/seo.php. Override per halaman:
      @section('title', 'Daftar Tim')            -> "Daftar Tim | Tukangsync"
      @section('meta_description', '...')
      @section('og_image', asset('...'))
      @section('robots', 'noindex, nofollow')
      @section('json_ld') ... @endsection         -> JSON-LD tambahan
--}}
@php
    $pageTitle = trim($__env->yieldContent('title'));
    $fullTitle = $pageTitle !== ''
        ? $pageTitle . ' | ' . $brand['name']
        : $brand['name'] . ' — ' . $brand['tagline'];
    $metaDescription = \Illuminate\Support\Str::limit(
        trim($__env->yieldContent('meta_description')) ?: $brand['description'],
        160,
        '…'
    );
    $ogImage = trim($__env->yieldContent('og_image')) ?: $brand['og_image_url'];

    // Canonical tanpa query string (utm_*, fbclid, dll. tidak membuat duplikat).
    $canonical = url()->current();

    // Hanya halaman publik yang terdaftar di config/seo.php, dan hanya di
    // production, yang boleh diindeks. Sisanya noindex.
    $indexable = app()->isProduction()
        && in_array(optional(request()->route())->getName(), config('seo.indexable_routes', []), true);
    $robots = trim($__env->yieldContent('robots')) ?: ($indexable ? 'index, follow' : 'noindex, nofollow');
@endphp

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
<meta name="theme-color" content="{{ $brand['theme_color'] }}" />

<title>{{ $fullTitle }}</title>

<meta name="description" content="{{ $metaDescription }}" />
<meta name="keywords" content="{{ $brand['keywords'] }}" />
<meta name="author" content="{{ $brand['author'] }}" />
<meta name="application-name" content="{{ $brand['name'] }}" />
<meta name="apple-mobile-web-app-title" content="{{ $brand['short_name'] }}" />
<meta name="robots" content="{{ $robots }}" />
<meta name="referrer" content="strict-origin-when-cross-origin" />
<link rel="canonical" href="{{ $canonical }}" />

@if ($brand['google_verification'])
    <meta name="google-site-verification" content="{{ $brand['google_verification'] }}" />
@endif

{{-- Open Graph --}}
<meta property="og:type" content="website" />
<meta property="og:locale" content="id_ID" />
<meta property="og:site_name" content="{{ $brand['name'] }}" />
<meta property="og:title" content="{{ $fullTitle }}" />
<meta property="og:description" content="{{ $metaDescription }}" />
<meta property="og:url" content="{{ $canonical }}" />
<meta property="og:image" content="{{ $ogImage }}" />
<meta property="og:image:width" content="1200" />
<meta property="og:image:height" content="630" />
<meta property="og:image:alt" content="{{ $brand['name'] }}" />

{{-- Twitter / X --}}
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $fullTitle }}" />
<meta name="twitter:description" content="{{ $metaDescription }}" />
<meta name="twitter:image" content="{{ $ogImage }}" />
@if ($brand['twitter'])
    <meta name="twitter:site" content="{{ \Illuminate\Support\Str::start($brand['twitter'], '@') }}" />
@endif

{{-- Favicon --}}
<link rel="icon" href="{{ $brand['favicon_url'] }}" sizes="any" />
<link rel="icon" type="image/svg+xml" href="{{ $brand['favicon_svg_url'] }}" />
<link rel="icon" type="image/png" sizes="32x32" href="{{ $brand['favicon_png_url'] }}" />
<link rel="apple-touch-icon" sizes="180x180" href="{{ $brand['apple_icon_url'] }}" />
<link rel="manifest" href="{{ $brand['manifest_url'] }}" />

{{-- Dibaca skrip penanganan sesi kedaluwarsa (419) dan AJAX. --}}
<meta name="csrf-token" content="{{ csrf_token() }}" />

@hasSection('json_ld')
    @yield('json_ld')
@endif
