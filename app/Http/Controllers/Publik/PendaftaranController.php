<?php

declare(strict_types=1);

namespace App\Http\Controllers\Publik;

use App\Enums\Peran;
use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Plan;
use App\Services\Saas\PenyediaTim;
use App\Support\AntiSpam;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    public function beranda(): View
    {
        return view('publik.beranda', ['plans' => Plan::aktif()->get()]);
    }

    public function create(Request $request): View
    {
        $plans = Plan::aktif()->get();
        $pilih = $plans->firstWhere('kode', $request->query('paket')) ?? $plans->first();

        return view('publik.daftar', [
            'plans' => $plans,
            'pilih' => $pilih,
            'rekening' => BankAccount::aktif()->get(),
            'peran' => Peran::pendaftar(),
        ]);
    }

    public function store(Request $request, PenyediaTim $penyedia): RedirectResponse
    {
        // Honeypot + time-trap (+ Turnstile bila aktif): bot dialihkan
        // diam-diam ke beranda tanpa membuat tim.
        if (! AntiSpam::lolos($request)) {
            return redirect()->route('beranda');
        }

        $data = $request->validate([
            'nama_tim' => ['required', 'string', 'max:120'],
            'kota' => ['nullable', 'string', 'max:80'],
            'alamat' => ['nullable', 'string', 'max:255'],
            'nama' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')],
            'no_wa' => ['required', 'string', 'max:20', 'regex:/^[0-9+\s-]{8,20}$/'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'peran' => ['required', Rule::in(array_map(fn (Peran $p) => $p->value, Peran::pendaftar()))],
            'plan_id' => ['required', Rule::exists('plans', 'id')->where('aktif', true)],
        ], [
            'email.unique' => 'Email ini sudah terdaftar. Gunakan email lain atau masuk ke tim Anda.',
            'no_wa.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, + atau -.',
        ]);

        $bayar = $penyedia->daftar($data, Plan::findOrFail($data['plan_id']));

        return redirect()->route('tim.status', [$bayar->team->slug, 'baru' => 1]);
    }
}
