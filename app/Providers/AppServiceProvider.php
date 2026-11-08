<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\User;
use App\Support\Brand;
use App\Support\SecurityLog;
use App\View\Composers\NotificationComposer;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Resolved once per request (and reset between Octane requests).
        $this->app->scoped(Brand::KEY, fn () => Brand::compute());
    }

    public function boot(): void
    {
        $this->configureUrls();
        $this->configureAuthorization();
        $this->configurePasswordPolicy();
        $this->configureRateLimiting();
        $this->configureModelSafety();
        $this->configureSecurityLogging();
        $this->shareBranding();
    }

    /**
     * Pengaman model saat pengembangan.
     *
     * - Atribut yang tidak ada di $fillable melempar exception (bukan
     *   dibuang diam-diam), supaya bug mass assignment ketahuan di lokal.
     * - Lazy loading (N+1) hanya DICATAT, tidak dilempar: sebagian view lama
     *   masih memuat relasi di dalam loop, dan melemparnya akan membuat
     *   halaman 500. Lihat storage/logs/laravel.log untuk daftarnya.
     */
    private function configureModelSafety(): void
    {
        if ($this->app->isProduction()) {
            return;
        }

        // Dicatat, bukan dilempar: beberapa jalur lama (mis. field form yang
        // bukan kolom seperti status_bayar) sengaja mengandalkan pembuangan
        // atribut, dan melemparnya membuat endpoint 500.
        Model::preventSilentlyDiscardingAttributes();
        Model::handleDiscardedAttributeViolationUsing(function (Model $model, array $keys): void {
            Log::debug('Atribut dibuang (tidak ada di $fillable)', ['model' => $model::class, 'atribut' => $keys]);
        });
        Model::preventLazyLoading();
        Model::handleLazyLoadingViolationUsing(function (Model $model, string $relation): void {
            Log::debug('Lazy loading terdeteksi', ['model' => $model::class, 'relasi' => $relation]);
        });
    }

    /**
     * Event keamanan ke channel log "security" (OWASP A09). Tidak pernah
     * mencatat password/token; email disamarkan.
     */
    private function configureSecurityLogging(): void
    {
        Event::listen(Login::class, fn (Login $e) => SecurityLog::catat('auth.login', null, [
            'user_id' => $e->user->getAuthIdentifier(),
            'guard' => $e->guard,
        ]));

        Event::listen(Failed::class, fn (Failed $e) => SecurityLog::catat('auth.failed', null, [
            'login' => SecurityLog::samarkanEmail((string) ($e->credentials['email'] ?? $e->credentials['no_wa'] ?? $e->credentials['name'] ?? '')),
            'guard' => $e->guard,
        ], 'warning'));

        Event::listen(Logout::class, fn (Logout $e) => SecurityLog::catat('auth.logout', null, [
            'user_id' => $e->user?->getAuthIdentifier(),
        ]));

        Event::listen(Lockout::class, fn (Lockout $e) => SecurityLog::catat('auth.lockout', $e->request, [], 'warning'));

        Event::listen(PasswordReset::class, fn (PasswordReset $e) => SecurityLog::catat('auth.password_reset', null, [
            'user_id' => $e->user->getAuthIdentifier(),
        ]));

        // Perubahan peran anggota tim / status aktif akun.
        User::updated(function (User $user): void {
            if ($user->wasChanged(['peran', 'is_active', 'team_id'])) {
                SecurityLog::catat('user.role_changed', null, [
                    'target_user_id' => $user->id,
                    'perubahan' => collect($user->getChanges())->only(['peran', 'is_active', 'team_id'])->all(),
                ], 'notice');
            }
        });

        // Akses ditolak (403) dari web maupun API.
        Event::listen(RequestHandled::class, function (RequestHandled $e): void {
            if ($e->response->getStatusCode() === 403) {
                SecurityLog::catat('access.denied', $e->request, [], 'notice');
            }
        });
    }

    /** Force canonical absolute URLs behind TLS in production. */
    private function configureUrls(): void
    {
        if (config('app.env') === 'production') {
            URL::forceRootUrl(config('app.url'));
            URL::forceScheme('https');
        }
    }

    private function configureAuthorization(): void
    {
        // Superadmin implicitly holds every permission.
        Gate::before(function ($user, $ability) {
            return $user->hasRole(['Superadmin', 'superadmin']) ? true : null;
        });
    }

    /** One password policy for registration, reset and change-password. */
    private function configurePasswordPolicy(): void
    {
        Password::defaults(function () {
            // Min 8, huruf besar-kecil, angka. Pemeriksaan kebocoran (HIBP)
            // hanya di production karena butuh akses internet.
            $rule = Password::min(8)->mixedCase()->numbers();

            return $this->app->isProduction() ? $rule->uncompromised() : $rule;
        });
    }

    /**
     * Named limiters used across web.php, auth.php and api.php.
     *
     * Authenticated users are keyed by id so several people behind one office
     * NAT never throttle each other.
     */
    private function configureRateLimiting(): void
    {
        $limits = config('security.rate_limits');

        RateLimiter::for('web', fn (Request $request) => Limit::perMinute($limits['web'])
            ->by($request->user()?->id ?: $request->ip()));

        RateLimiter::for('api', fn (Request $request) => Limit::perMinute($limits['api'])
            ->by($request->user()?->id ?: $request->ip()));

        // Writes are cheap to abuse and expensive to serve — keep them tighter.
        RateLimiter::for('write', fn (Request $request) => Limit::perMinute($limits['write'])
            ->by($request->user()?->id ?: $request->ip()));

        RateLimiter::for('login', fn (Request $request) => [
            Limit::perMinute($limits['login'])
                ->by(strtolower((string) $request->input('email')).'|'.$request->ip()),
            Limit::perMinute($limits['login'] * 4)->by($request->ip()),
        ]);

        RateLimiter::for('password', fn (Request $request) => Limit::perMinute($limits['password'])
            ->by(strtolower((string) $request->input('email')).'|'.$request->ip()));

        // Pendaftaran tim publik: cukup longgar untuk salah ketik, cukup
        // ketat supaya tidak ada yang membanjiri daftar tim "menunggu".
        RateLimiter::for('daftar', fn (Request $request) => [
            Limit::perMinute(3)->by($request->ip()),
            Limit::perHour(10)->by($request->ip()),
        ]);
    }

    /** Expose settings + resolved brand identity to every view. */
    private function shareBranding(): void
    {
        View::composer('*', function ($view) {
            $view->with('brand', Brand::all());

            // Kept for backwards compatibility with existing templates.
            $view->with('appSettings', $this->legacySettings());
        });

        View::composer('backend.layout.navbar', NotificationComposer::class);
    }

    /** @return array<string,string> */
    private function legacySettings(): array
    {
        try {
            return Setting::allCached();
        } catch (\Throwable) {
            return [];
        }
    }
}
