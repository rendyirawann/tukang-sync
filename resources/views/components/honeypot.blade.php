{{--
    Perlindungan anti-bot untuk formulir publik. Pasang di dalam <form>:
        <x-honeypot />
    Server memeriksanya lewat App\Support\AntiSpam::lolos().
    - situs_web : kolom jebakan, tersembunyi dari manusia dan pembaca layar.
    - _dimuat   : waktu formulir dimuat (terenkripsi), untuk time-trap.
    - Turnstile : hanya tampil bila TURNSTILE_ENABLED=true dan key terisi.
--}}
<div style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden" aria-hidden="true">
    <label for="{{ \App\Support\AntiSpam::HONEYPOT }}">Jangan diisi</label>
    <input type="text" id="{{ \App\Support\AntiSpam::HONEYPOT }}" name="{{ \App\Support\AntiSpam::HONEYPOT }}" value="" tabindex="-1" autocomplete="off">
</div>
<input type="hidden" name="{{ \App\Support\AntiSpam::WAKTU }}" value="{{ \App\Support\AntiSpam::stempel() }}">
@if (\App\Support\AntiSpam::turnstileAktif())
    <div class="cf-turnstile my-4" data-sitekey="{{ config('security.turnstile.site_key') }}"></div>
    @once
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endonce
@endif
