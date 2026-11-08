<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend\Saas;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use App\Services\Saas\KelolaAnggota;
use App\Support\ActivityRecorder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Superadmin mengelola akun di dalam sebuah tim (membantu pelanggan). */
class TeamUserController extends Controller
{
    public function __construct(private readonly KelolaAnggota $anggota) {}

    public function store(Request $request, Team $team): RedirectResponse
    {
        $user = $this->anggota->tambah($team, $request);
        ActivityRecorder::log('saas', "Menambah akun {$user->email} ke tim {$team->slug}", [], $team);

        return back()->with('success', 'Akun '.$user->name.' ditambahkan.');
    }

    public function update(Request $request, Team $team, User $user): RedirectResponse
    {
        $this->anggota->ubah($team, $user, $request);
        ActivityRecorder::log('saas', "Mengubah akun {$user->email} di tim {$team->slug}", [], $team);

        return back()->with('success', 'Akun '.$user->name.' diperbarui.');
    }

    public function destroy(Team $team, User $user): RedirectResponse
    {
        $this->anggota->hapus($team, $user);
        ActivityRecorder::log('saas', "Menghapus akun {$user->email} dari tim {$team->slug}", [], $team);

        return back()->with('success', 'Akun '.$user->name.' dihapus.');
    }
}
