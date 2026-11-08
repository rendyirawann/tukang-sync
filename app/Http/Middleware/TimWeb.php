<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\StatusTim;
use App\Models\Team;
use App\Support\Tenancy\PenjagaTim;
use App\Support\Tenancy\TimAktif;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menyelesaikan /{tim}/... menjadi tim aktif untuk panel web tim.
 *
 *   tim          -> halaman publik tim (login, status pembayaran)
 *   tim:anggota  -> panel tim; wajib login sebagai anggota tim INI
 */
class TimWeb
{
    /** Rute yang tetap boleh mengubah data walau langganan habis / viewer. */
    private const SELALU_BOLEH = [
        'tim.logout',
        'tim.akun.*',
        'tim.langganan.*',
    ];

    public function handle(Request $request, Closure $next, string $mode = 'publik'): Response
    {
        $param = $request->route('tim');
        $tim = $param instanceof Team
            ? $param
            : Team::where('slug', Team::normalkanSlug((string) $param))->first();

        abort_if($tim === null, 404, 'Kode tim tidak ditemukan.');

        // Kode diketik huruf kecil: arahkan ke bentuk bakunya supaya URL
        // yang dibagikan selalu sama.
        if (is_string($param) && $param !== $tim->slug && $request->isMethod('GET')) {
            return redirect()->to(preg_replace('#/'.preg_quote($param, '#').'(/|$)#', '/'.$tim->slug.'$1', $request->getRequestUri(), 1));
        }

        $request->route()->setParameter('tim', $tim);
        TimAktif::set($tim);
        View::share('tim', $tim);

        if ($mode !== 'anggota') {
            return $next($request);
        }

        $user = $request->user();

        if ($user === null) {
            return redirect()->guest(route('tim.login', $tim->slug));
        }

        // Akun tim lain: kembalikan ke timnya sendiri.
        if ($user->team_id !== $tim->id) {
            if ($user->team) {
                return redirect()->route('tim.dashboard', $user->team->slug);
            }

            return redirect()->route('dashboard');
        }

        if ($tim->status === StatusTim::Menunggu) {
            return redirect()->route('tim.status', $tim->slug);
        }

        if ($pesan = PenjagaTim::pesanTolak($tim)) {
            return $this->keluarkan($request, $tim, $pesan);
        }

        if (! $user->is_active || $user->banned_at !== null) {
            return $this->keluarkan($request, $tim, 'Akun Anda dinonaktifkan. Hubungi pemilik tim.');
        }

        if (! $request->isMethodSafe() && ! $request->routeIs(...self::SELALU_BOLEH)) {
            if ($tim->hanyaBaca()) {
                return $this->tolakTulis($request, PenjagaTim::pesanHanyaBaca($tim), 402);
            }

            if ($user->isViewer()) {
                return $this->tolakTulis($request, 'Akun viewer hanya bisa melihat data.', 403);
            }
        }

        return $next($request);
    }

    private function keluarkan(Request $request, Team $tim, string $pesan): Response
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('tim.login', $tim->slug)->withErrors(['email' => $pesan]);
    }

    private function tolakTulis(Request $request, string $pesan, int $status): Response
    {
        if ($request->expectsJson()) {
            return response()->json(['status' => 'error', 'message' => $pesan], $status);
        }

        return back()->with('error', $pesan);
    }
}
