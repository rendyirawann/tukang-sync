<base href="{{ url('/') }}/" />
@include('partials.theme-boot')
@include('partials.meta')
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family={{ str_replace(' ', '+', $brand['font']) }}:wght@300;400;500;600;700;800&display=swap" />
<link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
<style>
    :root {
        --tp-primary: #1E3A5F;
        --tp-dark: #0F1D33;
        --tp-light: #4A7BC4;
        --tp-gold: #C9A44C;
        --bs-primary: #1E3A5F;
        --bs-primary-rgb: 30, 58, 95;
        --bs-body-font-family: '{{ $brand['font'] }}', system-ui, sans-serif;
    }
    body, h1, h2, h3, h4, h5, h6 { font-family: '{{ $brand['font'] }}', system-ui, -apple-system, 'Segoe UI', sans-serif; }
    .btn.btn-primary { background: var(--tp-primary) !important; border-color: var(--tp-primary) !important; color: #fff !important; }
    .btn.btn-primary:hover { background: var(--tp-light) !important; }
    .btn.btn-gold { background: var(--tp-gold); color: #0F1D33; font-weight: 600; }
    .btn.btn-gold:hover { background: #d8b55e; color: #0F1D33; }
    .text-gold { color: var(--tp-gold) !important; }
    .bg-navy { background: linear-gradient(135deg, #0D1830, #2B5288 60%, #16243C); }
    .kode-badge { font-family: ui-monospace, Menlo, Consolas, monospace; letter-spacing: .12em; background: rgba(201,164,76,.15); color: var(--tp-gold); border: 1px solid rgba(201,164,76,.45); border-radius: 6px; padding: 2px 8px; font-weight: 700; }
    .progress-thin { height: 6px; }
    .table td, .table th { vertical-align: middle; }
</style>
