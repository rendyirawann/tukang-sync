<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\AntiSpam;
use App\Support\SecurityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $pesan = 'Jika email tersebut terdaftar, tautan untuk mengatur ulang password sudah kami kirim.';

        if (! AntiSpam::lolos($request)) {
            return back()->with('status', $pesan);
        }

        $status = Password::sendResetLink($request->only('email'));

        SecurityLog::catat('password.reset_link_requested', $request, [
            'email' => SecurityLog::samarkanEmail((string) $request->input('email')),
            'status' => $status,
        ]);

        // Pesan selalu sama, terkirim atau tidak: jangan bocorkan apakah
        // sebuah email terdaftar. Throttle Laravel tetap menjadi error agar
        // pengguna tahu harus menunggu.
        if ($status === Password::RESET_THROTTLED) {
            return back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
        }

        return back()->with('status', $pesan);
    }
}
