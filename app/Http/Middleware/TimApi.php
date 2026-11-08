<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\ApiResponse;
use App\Support\Tenancy\PenjagaTim;
use App\Support\Tenancy\TimAktif;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

/**
 * Mengikat request API ke tim milik akun yang login.
 *
 * Dipasang SETELAH auth:sanctum. Menolak akun tanpa tim, tim yang belum
 * aktif atau ditangguhkan, dan -- saat masa langganan habis -- semua
 * permintaan yang mengubah data, kecuali urusan akun sendiri (logout,
 * profil, sandi) supaya pengguna tidak terkunci dari akunnya.
 */
class TimApi
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || $user->team_id === null) {
            return ApiResponse::error('Akun ini bukan anggota tim.', 403, code: 'NOT_TEAM_MEMBER');
        }

        $tim = $user->team;

        if ($tolak = PenjagaTim::alasanTolak($tim)) {
            return $tolak;
        }

        if (! $user->is_active || $user->banned_at !== null) {
            $token = $user->currentAccessToken();

            if ($token instanceof PersonalAccessToken) {
                $token->delete();
            }

            return ApiResponse::error('Akun Anda dinonaktifkan. Hubungi pemilik tim.', 403, code: 'ACCOUNT_DISABLED');
        }

        TimAktif::set($tim);

        $mengubah = ! $request->isMethodSafe();

        if ($mengubah && $tim->hanyaBaca() && ! $request->is('api/auth/*')) {
            return ApiResponse::error(PenjagaTim::pesanHanyaBaca($tim), 402, code: 'SUBSCRIPTION_EXPIRED');
        }

        if ($mengubah && $user->isViewer() && ! $request->is('api/auth/*')) {
            return ApiResponse::error('Akun viewer hanya bisa melihat data.', 403, code: 'FORBIDDEN');
        }

        return $next($request);
    }
}
