<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tim;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AkunController extends TimController
{
    public function index(Request $request): View
    {
        return view('tim.akun.index', ['tim' => $this->tim(), 'user' => $request->user()]);
    }

    public function profil(Request $request): RedirectResponse
    {
        $request->user()->update($request->validate([
            'name' => ['required', 'string', 'max:120'],
            'no_wa' => ['nullable', 'string', 'max:20'],
            'jabatan' => ['nullable', 'string', 'max:100'],
        ]));

        return back()->with('success', 'Profil diperbarui.');
    }

    public function password(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], ['current_password.current_password' => 'Password lama salah.']);

        $user = $request->user();
        $user->forceFill(['password' => $request->input('password'), 'password_changed_at' => now()])->save();

        // Sesi aplikasi mobile ikut dicabut.
        $user->tokens()->delete();
        $user->revokeBiometricToken();

        return back()->with('success', 'Password diganti. Login di aplikasi mobile perlu diulang.');
    }
}
