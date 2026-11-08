<?php

declare(strict_types=1);

namespace App\Services\Saas;

use App\Enums\Peran;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/**
 * Menambah, mengubah, dan menghapus akun anggota tim.
 *
 * Dipakai superadmin (membantu pelanggan) dan pengelola tim sendiri, jadi
 * aturan kuota dan "minimal satu pengelola aktif" berlaku sama di keduanya.
 */
final class KelolaAnggota
{
    /** @return array<string, mixed> */
    public function aturan(?User $user = null): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:120', Rule::unique('users', 'email')->ignore($user?->id)],
            'no_wa' => ['nullable', 'string', 'max:20'],
            'jabatan' => ['nullable', 'string', 'max:100'],
            'peran' => ['required', Rule::enum(Peran::class)],
            'password' => [$user ? 'nullable' : 'required', 'string', Password::min(8)->letters()->numbers()],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function tambah(Team $tim, Request $request): User
    {
        $data = $request->validate($this->aturan());

        if ($tim->kuotaTersisa() < 1) {
            throw ValidationException::withMessages([
                'email' => "Kuota akun tim sudah penuh ({$tim->max_users} akun termasuk owner).",
            ]);
        }

        return User::create([
            'team_id' => $tim->id,
            'peran' => $data['peran'],
            'name' => $data['name'],
            'email' => Str::lower($data['email']),
            'no_wa' => $data['no_wa'] ?? null,
            'jabatan' => $data['jabatan'] ?? null,
            'password' => $data['password'],
            'is_active' => true,
        ]);
    }

    public function ubah(Team $tim, User $user, Request $request): User
    {
        $this->pastikanAnggota($tim, $user);
        $data = $request->validate($this->aturan($user));

        $peranBaru = Peran::from($data['peran']);
        $aktif = $request->has('is_active') ? $request->boolean('is_active') : $user->is_active;

        if ($user->isPengelola() && (! $peranBaru->pengelola() || ! $aktif)) {
            $this->pastikanAdaPengelolaLain($tim, $user);
        }

        $user->fill([
            'name' => $data['name'],
            'email' => Str::lower($data['email']),
            'no_wa' => $data['no_wa'] ?? null,
            'jabatan' => $data['jabatan'] ?? null,
            'peran' => $peranBaru,
            'is_active' => $aktif,
        ]);

        $gantiSandi = filled($data['password'] ?? null);

        if ($gantiSandi) {
            $user->password = $data['password'];
            $user->password_changed_at = now();
        }

        $user->save();

        if ($gantiSandi || ! $aktif) {
            $user->tokens()->delete();
            $user->revokeBiometricToken();
        }

        return $user;
    }

    public function hapus(Team $tim, User $user): void
    {
        $this->pastikanAnggota($tim, $user);

        if ($user->isPengelola()) {
            $this->pastikanAdaPengelolaLain($tim, $user);
        }

        $user->tokens()->delete();
        $user->delete();
    }

    private function pastikanAnggota(Team $tim, User $user): void
    {
        abort_unless($user->team_id === $tim->id, 404);
    }

    /** Tim tidak boleh kehilangan pengelola aktif terakhirnya. */
    private function pastikanAdaPengelolaLain(Team $tim, User $user): void
    {
        $lain = User::anggotaTim($tim->id)
            ->whereKeyNot($user->id)
            ->whereIn('peran', [Peran::Owner->value, Peran::Kontraktor->value])
            ->where('is_active', true)
            ->exists();

        if (! $lain) {
            throw ValidationException::withMessages([
                'peran' => 'Tim harus punya minimal satu owner/kontraktor aktif. Tambahkan pengelola lain dulu.',
            ]);
        }
    }
}
