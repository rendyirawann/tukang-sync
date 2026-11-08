<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tim;

use App\Enums\StatusTim;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function masuk(Request $request, Team $tim): RedirectResponse
    {
        $user = $request->user();

        return $user !== null && $user->team_id === $tim->id
            ? redirect()->route('tim.dashboard', $tim->slug)
            : redirect()->route('tim.login', $tim->slug);
    }

    public function create(Team $tim): View
    {
        return view('tim.login', ['tim' => $tim]);
    }

    public function store(LoginRequest $request, Team $tim): RedirectResponse
    {
        if ($tim->status === StatusTim::Menunggu) {
            return redirect()->route('tim.status', $tim->slug)
                ->with('warning', 'Pembayaran tim belum dikonfirmasi. Silakan konfirmasi ke admin via WhatsApp.');
        }

        if ($tim->status === StatusTim::Ditangguhkan) {
            throw ValidationException::withMessages(['email' => 'Akses tim ini sedang ditangguhkan. Hubungi admin.']);
        }

        $request->authenticate($tim->id);
        $request->session()->regenerate();

        $user = Auth::user();

        if (! $user->is_active || $user->banned_at !== null) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages(['email' => 'Akun Anda dinonaktifkan. Hubungi pemilik tim.']);
        }

        $user->forceFill(['last_login' => now(), 'last_ip' => $request->ip()])->save();

        return redirect()->intended(route('tim.dashboard', $tim->slug));
    }

    public function destroy(Request $request, Team $tim): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('tim.login', $tim->slug);
    }
}
