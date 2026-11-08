<?php

namespace App\Models;

use App\Enums\Peran;
use Cog\Contracts\Ban\Bannable as BannableContract;
use Cog\Laravel\Ban\Traits\Bannable;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Satu tabel untuk dua jenis akun:
 *
 *   team_id = null  -> akun platform (Superadmin), masuk lewat /admin/login.
 *   team_id terisi  -> anggota sebuah tim, masuk lewat /{slug}/login atau
 *                      aplikasi mobile; perannya di kolom `peran`.
 *
 * Model ini sengaja TIDAK memakai global scope MilikTim: provider auth dan
 * Sanctum harus bisa menemukan akun sebelum tim aktif diketahui. Query
 * anggota tim selalu disaring eksplisit lewat scope anggotaTim().
 */
class User extends Authenticatable implements BannableContract
{
    /** @use HasFactory<UserFactory> */
    use Bannable, HasApiTokens, HasFactory, HasRoles, HasUuids, Notifiable;

    protected $fillable = [
        'team_id',
        'peran',
        'name',
        'username',
        'email',
        'no_wa',
        'avatar',
        'last_ip',
        'last_login',
        'banned_at',
        'nik',
        'phone',
        'jabatan',
        'alamat',
        'is_active',
        'password',
        'social_id',
        'social_type',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'biometric_token_hash',
    ];

    protected function casts(): array
    {
        return [
            'peran' => Peran::class,
            'email_verified_at' => 'datetime',
            'last_login' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'biometric_enrolled_at' => 'datetime',
            'biometric_expires_at' => 'datetime',
            'password_changed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Username wajib unik di tabel starter, tetapi anggota tim tidak
        // pernah mengetiknya; dibuat otomatis dari email bila kosong.
        static::creating(function (self $user): void {
            if (blank($user->username)) {
                $user->username = self::usernameUnik(Str::before((string) $user->email, '@'));
            }
        });
    }

    public static function usernameUnik(string $dasar): string
    {
        $dasar = Str::slug($dasar, '_') ?: 'user';
        $nama = $dasar;
        $i = 1;

        while (self::where('username', $nama)->exists()) {
            $nama = $dasar.'_'.(++$i);
        }

        return $nama;
    }

    // ------------------------------------------------------------------
    // Relasi & scope
    // ------------------------------------------------------------------

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function kegiatan(): HasMany
    {
        return $this->hasMany(Kegiatan::class, 'created_by');
    }

    public function scopeAnggotaTim(Builder $q, int $teamId): Builder
    {
        return $q->where('team_id', $teamId);
    }

    // ------------------------------------------------------------------
    // Peran
    // ------------------------------------------------------------------

    /** Superadmin platform: pemilik aplikasi SaaS, bukan anggota tim. */
    public function isPlatformAdmin(): bool
    {
        return $this->team_id === null && $this->hasRole('Superadmin');
    }

    public function isAnggotaTim(): bool
    {
        return $this->team_id !== null;
    }

    /** Owner atau kontraktor: mengelola proyek, anggaran, anggota, langganan. */
    public function isPengelola(): bool
    {
        return $this->peran?->pengelola() ?? false;
    }

    /** Mandor atau anggota: mengisi data lapangan. */
    public function isPelaksana(): bool
    {
        return $this->peran?->pelaksana() ?? false;
    }

    public function isViewer(): bool
    {
        return $this->peran === Peran::Viewer;
    }

    /**
     * Peran versi lama ("superadmin" / "petugas") yang masih dibaca aplikasi
     * mobile untuk menentukan tombol mana yang tampil. Pengelola tim
     * memegang hak yang dulu dimiliki superadmin aplikasi tunggal.
     */
    public function peranLama(): string
    {
        return $this->isPengelola() ? 'superadmin' : 'petugas';
    }

    public function roleLabel(): string
    {
        if ($this->peran instanceof Peran) {
            return $this->peran->label();
        }

        return $this->roles->first()->name ?? 'User';
    }

    /** Dipakai view starter. */
    public function getRoleLabelAttribute(): string
    {
        return $this->roleLabel();
    }

    // ------------------------------------------------------------------
    // Avatar (starter)
    // ------------------------------------------------------------------

    public function getAvatarUrlAttribute(): ?string
    {
        $file = $this->avatar;

        if (! is_string($file) || trim($file) === '') {
            return null;
        }

        if (Str::startsWith($file, ['http://', 'https://'])) {
            return $file;
        }

        if (Storage::disk('public')->exists('user/avatar/'.$file)) {
            return Storage::disk('public')->url('user/avatar/'.$file);
        }

        if (is_file(public_path('assets/media/avatars/'.$file))) {
            return asset('assets/media/avatars/'.$file);
        }

        return null;
    }

    public function getAvatarDisplayUrlAttribute(): string
    {
        return $this->avatar_url ?? asset('assets/media/avatars/blank.png');
    }

    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/', trim((string) $this->name), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if ($words === []) {
            return 'U';
        }

        $initials = mb_substr($words[0], 0, 1);

        if (isset($words[1])) {
            $initials .= mb_substr($words[1], 0, 1);
        }

        return Str::upper($initials);
    }

    // ------------------------------------------------------------------
    // Login sidik jari (dari o-api)
    // ------------------------------------------------------------------

    /**
     * Menerbitkan token biometrik baru. Nilai mentahnya hanya dikembalikan di
     * sini; database menyimpan hash SHA-256-nya.
     */
    public function issueBiometricToken(?string $deviceName = null): string
    {
        $plain = Str::random(64);

        $this->forceFill([
            'biometric_token_hash' => hash('sha256', $plain),
            'biometric_enrolled_at' => now(),
            'biometric_expires_at' => now()->addDays((int) config('taksasi.biometric_ttl_days', 90)),
            'biometric_device_name' => $deviceName,
        ])->save();

        return $plain;
    }

    public function revokeBiometricToken(): void
    {
        $this->forceFill([
            'biometric_token_hash' => null,
            'biometric_enrolled_at' => null,
            'biometric_expires_at' => null,
            'biometric_device_name' => null,
        ])->save();
    }

    public function biometricTokenIsValid(string $plain): bool
    {
        if ($this->biometric_token_hash === null) {
            return false;
        }

        if ($this->biometric_expires_at instanceof Carbon && $this->biometric_expires_at->isPast()) {
            return false;
        }

        return hash_equals($this->biometric_token_hash, hash('sha256', $plain));
    }

    public function hasBiometricEnrolled(): bool
    {
        return $this->biometric_token_hash !== null
            && ! ($this->biometric_expires_at instanceof Carbon && $this->biometric_expires_at->isPast());
    }

    public function checkPassword(string $plain): bool
    {
        return Hash::check($plain, $this->password);
    }
}
