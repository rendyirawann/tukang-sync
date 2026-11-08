<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tim;

use App\Enums\Peran;
use App\Models\User;
use App\Services\Saas\KelolaAnggota;
use App\Support\Izin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AnggotaController extends TimController
{
    public function __construct(private readonly KelolaAnggota $anggota) {}

    public function index(Request $request): View
    {
        $tim = $this->tim();

        return view('tim.anggota.index', [
            'tim' => $tim,
            'anggota' => User::anggotaTim($tim->id)
                ->orderByRaw("CASE peran WHEN 'owner' THEN 0 WHEN 'kontraktor' THEN 1 WHEN 'mandor' THEN 2 WHEN 'anggota' THEN 3 ELSE 4 END")
                ->orderBy('name')->get(),
            'peran' => Peran::cases(),
            'boleh' => Izin::kelolaPengguna($request->user()),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->izin(Izin::kelolaPengguna($request->user()));
        $user = $this->anggota->tambah($this->tim(), $request);

        return back()->with('success', "Akun {$user->name} ditambahkan. Ia bisa masuk di ".route('tim.login', $this->tim()->slug));
    }

    public function update(Request $request, $tim, User $user): RedirectResponse
    {
        $this->izin(Izin::kelolaPengguna($request->user()));
        $this->anggota->ubah($this->tim(), $user, $request);

        return back()->with('success', "Akun {$user->name} diperbarui.");
    }

    public function destroy(Request $request, $tim, User $user): RedirectResponse
    {
        $this->izin(Izin::kelolaPengguna($request->user()));
        abort_if($user->id === $request->user()->id, 422, 'Tidak bisa menghapus akun sendiri.');
        $this->anggota->hapus($this->tim(), $user);

        return back()->with('success', "Akun {$user->name} dihapus.");
    }
}
