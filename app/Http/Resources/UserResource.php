<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\User;
use App\Support\Izin;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->peranLama(),
            'peran' => $this->peran?->value,
            'tim' => $this->team ? [
                'kode' => $this->team->slug,
                'nama' => $this->team->nama,
                'hanya_baca' => $this->team->hanyaBaca(),
                'aktif_sampai' => $this->team->aktif_sampai?->toIso8601String(),
                'lifetime' => $this->team->lifetime,
            ] : null,
            'role_label' => $this->roleLabel(),
            'is_superadmin' => $this->isPengelola(),
            'is_petugas' => $this->isPelaksana(),

            // Dikirim bersama profil agar aplikasi tahu tombol mana yang
            // perlu ditampilkan, memakai aturan yang sama persis dengan
            // yang ditegakkan server.
            'izin' => Izin::ringkasan($this->resource),
            'phone' => $this->phone,
            'jabatan' => $this->jabatan,
            'alamat' => $this->alamat,
            'is_active' => $this->is_active,
            'biometric_enabled' => $this->hasBiometricEnrolled(),
            'biometric_device_name' => $this->biometric_device_name,
            'biometric_expires_at' => $this->biometric_expires_at?->toIso8601String(),
            'password_changed_at' => $this->password_changed_at?->toIso8601String(),
            'last_login_at' => $this->last_login?->toIso8601String(),
        ];
    }
}
