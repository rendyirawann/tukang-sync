<?php

use App\Http\Middleware\CatatAktivitas;
use App\Http\Middleware\TimApi;
use App\Http\Middleware\TimWeb;
use App\Http\Middleware\WajibPlatformAdmin;
use App\Support\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'forbid-banned-user' => \Cog\Laravel\Ban\Http\Middleware\ForbidBannedUser::class,

            // SaaS
            'platform' => WajibPlatformAdmin::class,
            'tim' => TimWeb::class,
            'tim.api' => TimApi::class,
        ]);

        // Runs on every request, including assets served through PHP:
        // header hardening first, then the URL-signature guard.
        $middleware->prepend([
            \App\Http\Middleware\ResetTimAktif::class,
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\BlockSuspiciousRequests::class,
        ]);

        $middleware->web(append: [
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \App\Http\Middleware\SanitizeInput::class,
            \App\Http\Middleware\CheckMaintenanceMode::class,
            'throttle:web',
        ]);

        $middleware->api(prepend: [
            \App\Http\Middleware\SanitizeInput::class,
        ]);

        $middleware->appendToGroup('api', 'throttle:api');

        // Setiap endpoint API yang mengubah data otomatis tercatat di jejak
        // aktivitas tim, termasuk endpoint yang ditambahkan nanti.
        $middleware->appendToGroup('api', CatatAktivitas::class);

        $middleware->validateCsrfTokens(except: []);

        /*
         * Tamu diarahkan ke halaman login yang sesuai dengan tempatnya:
         * rute tim -> /{slug}/login, rute admin -> /admin/login, API -> 401.
         */
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('api/*')) {
                return null;
            }

            $tim = $request->route('tim');

            if ($tim !== null) {
                return route('tim.login', is_string($tim) ? strtoupper($tim) : $tim->slug);
            }

            return route('login');
        });

        // Pengguna yang sudah login dan membuka halaman tamu dikembalikan ke
        // dasbornya sendiri: anggota tim ke dasbor tim, superadmin ke /admin.
        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();

            if ($user?->team_id !== null && $user->team) {
                return route('tim.dashboard', $user->team->slug);
            }

            return route('dashboard');
        });

        if ($proxies = env('TRUSTED_PROXIES')) {
            $middleware->trustProxies(at: $proxies === '*' ? '*' : explode(',', $proxies));
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        /*
         * Envelope error API yang seragam { success, message, code, errors? }
         * dibawa dari o-api, supaya aplikasi mobile cukup punya satu jalur
         * parsing. Halaman web tetap memakai halaman galat starter.
         */
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $isApi = fn (Request $request): bool => $request->is('api/*');

        $exceptions->render(function (ValidationException $e, Request $request) use ($isApi) {
            if (! $isApi($request)) {
                return null;
            }

            return ApiResponse::error(
                $e->validator->errors()->first() ?: 'Data yang dikirim tidak valid.',
                422,
                $e->errors(),
                'VALIDATION_ERROR',
            );
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($isApi) {
            if (! $isApi($request)) {
                return null;
            }

            return ApiResponse::error('Sesi Anda sudah berakhir. Silakan login kembali.', 401, code: 'UNAUTHENTICATED');
        });

        $exceptions->render(function (AuthorizationException $e, Request $request) use ($isApi) {
            if (! $isApi($request)) {
                return null;
            }

            return ApiResponse::error('Anda tidak punya hak akses untuk tindakan ini.', 403, code: 'FORBIDDEN');
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) use ($isApi) {
            if (! $isApi($request)) {
                return null;
            }

            return ApiResponse::error('Data tidak ditemukan.', 404, code: 'NOT_FOUND');
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($isApi) {
            if (! $isApi($request)) {
                return null;
            }

            $isModel = $e->getPrevious() instanceof ModelNotFoundException;

            return ApiResponse::error(
                $isModel ? 'Data tidak ditemukan atau sudah dihapus.' : 'Endpoint tidak ditemukan.',
                404,
                code: $isModel ? 'MODEL_NOT_FOUND' : 'NOT_FOUND',
            );
        });

        $exceptions->render(function (PostTooLargeException $e, Request $request) use ($isApi) {
            if (! $isApi($request)) {
                return null;
            }

            return ApiResponse::error('Berkas terlalu besar. Ukuran maksimal 8 MB.', 413, code: 'PAYLOAD_TOO_LARGE');
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) use ($isApi) {
            if (! $isApi($request)) {
                return null;
            }

            return ApiResponse::error('Metode HTTP tidak diizinkan untuk endpoint ini.', 405, code: 'METHOD_NOT_ALLOWED');
        });

        $exceptions->render(function (TooManyRequestsHttpException $e, Request $request) use ($isApi) {
            if (! $isApi($request)) {
                return null;
            }

            $detik = (int) ($e->getHeaders()['Retry-After'] ?? 0);

            return ApiResponse::error(
                $detik > 0
                    ? "Terlalu banyak percobaan. Coba lagi dalam {$detik} detik."
                    : 'Terlalu banyak permintaan. Silakan tunggu sebentar.',
                429,
                code: 'TOO_MANY_REQUESTS',
            )->withHeaders($e->getHeaders());
        });

        $exceptions->render(function (Throwable $e, Request $request) use ($isApi) {
            if (! $isApi($request) || $e instanceof HttpExceptionInterface || config('app.debug')) {
                return null;
            }

            report($e);

            return ApiResponse::error('Terjadi kesalahan pada server. Silakan coba lagi.', 500, code: 'SERVER_ERROR');
        });
    })->create();
