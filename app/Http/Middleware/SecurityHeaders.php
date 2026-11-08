<?php

namespace App\Http\Middleware;

use App\Support\AntiSpam;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Header keamanan browser untuk SETIAP respons (HTML, JSON, halaman galat).
 *
 * Kebijakannya sengaja longgar supaya tema Metronic (script/style inline,
 * plugin yang memakai eval), Google Fonts, CDN, Swagger UI, dan websocket
 * Reverb tetap jalan, tetapi tetap menutup clickjacking, MIME sniffing,
 * kebocoran referrer, object/plugin, dan pembajakan form lintas origin.
 * Keamanan utama tetap di server: validasi, auth, CSRF, otorisasi.
 *
 * Sengaja TIDAK memasang COEP/COOP/CORP ketat: header itu sering memblokir
 * gambar CDN dan popup login sosial.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        // Hanya mematikan fitur yang jelas tidak dipakai versi web. Kamera
        // dan lokasi dibiarkan: unggah foto struk dari HP memakai kamera.
        $response->headers->set(
            'Permissions-Policy',
            'payment=(), usb=(), midi=(), magnetometer=(), gyroscope=(), accelerometer=(), interest-cohort=()'
        );

        // Sidik jari server. header_remove() untuk X-Powered-By yang
        // dipasang PHP sendiri; matikan juga expose_php=Off di php.ini.
        if (! headers_sent()) {
            header_remove('X-Powered-By');
        }
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        if (config('security.hsts', false) && $request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        if (config('security.csp.enabled', true) && $this->isHtml($response)) {
            $header = config('security.csp.report_only', false)
                ? 'Content-Security-Policy-Report-Only'
                : 'Content-Security-Policy';

            $response->headers->set($header, $this->policy());
        }

        // Halaman yang memuat data akun tidak boleh disimpan cache browser/proxy.
        if ($request->user() !== null && $this->isHtml($response)) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
        }

        return $response;
    }

    private function isHtml(Response $response): bool
    {
        return str_contains((string) $response->headers->get('Content-Type', 'text/html'), 'text/html');
    }

    /**
     * Menyusun CSP dari daftar host yang memang dipakai project.
     *
     * Hasil pemindaian Blade/JS: fonts.googleapis.com, fonts.gstatic.com,
     * cdn.jsdelivr.net (+ cdnjs/unpkg yang dipakai plugin tema). wa.me hanya
     * tautan navigasi, tidak perlu masuk CSP.
     */
    private function policy(): string
    {
        $extra = array_filter(array_map('trim', (array) config('security.csp.extra_hosts', [])));
        $cdn = 'https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://unpkg.com';
        $connect = ["'self'", 'https:'];
        $frame = ["'self'"];
        $script = ["'self'", "'unsafe-inline'", "'unsafe-eval'", 'https://fonts.googleapis.com', $cdn];

        // Reverb: host & port dari .env, ws:// lokal dan wss:// produksi.
        $reverbHost = (string) config('broadcasting.connections.reverb.options.host', '');
        if ($reverbHost !== '') {
            $port = (string) config('broadcasting.connections.reverb.options.port', '');
            $hostPort = $reverbHost.($port !== '' ? ':'.$port : '');
            $connect[] = 'ws://'.$hostPort;
            $connect[] = 'wss://'.$hostPort;
        }

        // Server Vite saat pengembangan.
        if (app()->environment('local')) {
            foreach (['localhost:5173', '127.0.0.1:5173'] as $vite) {
                $script[] = 'http://'.$vite;
                $connect[] = 'http://'.$vite;
                $connect[] = 'ws://'.$vite;
            }
        }

        if (AntiSpam::turnstileAktif()) {
            $script[] = 'https://challenges.cloudflare.com';
            $frame[] = 'https://challenges.cloudflare.com';
        }

        $extraList = $extra === [] ? '' : ' '.implode(' ', $extra);

        $directives = [
            "default-src 'self'",
            implode(' ', $script).$extraList,
            "style-src 'self' 'unsafe-inline' https:",
            "img-src 'self' data: blob: https:",
            "font-src 'self' data: https:",
            'connect-src '.implode(' ', array_unique($connect)).$extraList,
            "media-src 'self' data: blob: https:",
            "worker-src 'self' blob:",
            'frame-src '.implode(' ', $frame).$extraList,
            "frame-ancestors 'self'",
            "form-action 'self'",
            "base-uri 'self'",
            "object-src 'none'",
        ];

        $directives[1] = 'script-src '.substr($directives[1], 0);

        if (config('security.csp.upgrade_insecure', false)) {
            $directives[] = 'upgrade-insecure-requests';
        }

        return implode('; ', $directives);
    }
}
