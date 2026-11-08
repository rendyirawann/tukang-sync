<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Panel /admin hanya untuk akun platform (Superadmin, tanpa tim).
 *
 * Anggota tim yang tersasar ke /admin dikembalikan ke dasbor timnya, bukan
 * diberi 403: mereka memang punya sesi yang sah, hanya salah pintu.
 */
class WajibPlatformAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && $user->team_id !== null) {
            if ($user->team) {
                return redirect()->route('tim.dashboard', $user->team->slug);
            }

            Auth::guard('web')->logout();

            return redirect()->route('login');
        }

        return $next($request);
    }
}
