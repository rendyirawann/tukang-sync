<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\Peran;
use App\Enums\StatusTim;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Team extends Model
{
    use SoftDeletes;

    /**
     * Huruf untuk kode tim. Tanpa 0/O dan 1/I/L supaya kode yang dibacakan
     * lewat telepon atau disalin dari tulisan tangan tidak tertukar.
     */
    public const ALFABET_SLUG = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    /** Kode yang tidak boleh dipakai karena bentrok dengan rute aplikasi. */
    public const SLUG_TERLARANG = ['DOCS', 'API', 'ADMIN', 'DAFTAR', 'LOGIN', 'UP', 'BAYAR', 'ASSETS', 'BUILD', 'STORAGE', 'HELP', 'SAAS'];

    protected $fillable = [
        'slug', 'nama', 'kota', 'alamat', 'email', 'no_wa', 'status', 'plan_id',
        'lifetime', 'aktif_sampai', 'max_users', 'catatan_admin', 'diaktifkan_pada',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusTim::class,
            'lifetime' => 'boolean',
            'aktif_sampai' => 'datetime',
            'diaktifkan_pada' => 'datetime',
            'max_users' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where($field ?? 'slug', self::normalkanSlug((string) $value))->first();
    }

    // ------------------------------------------------------------------
    // Relasi
    // ------------------------------------------------------------------

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(TeamPayment::class)->latest();
    }

    /** Owner atau kontraktor pertama: penanggung jawab akun tim. */
    public function pemilik(): ?User
    {
        return $this->users()
            ->whereIn('peran', [Peran::Owner->value, Peran::Kontraktor->value])
            ->orderBy('created_at')
            ->first();
    }

    // ------------------------------------------------------------------
    // Status langganan
    // ------------------------------------------------------------------

    public function isAktif(): bool
    {
        return $this->status === StatusTim::Aktif;
    }

    public function langgananBerlaku(): bool
    {
        if (! $this->isAktif()) {
            return false;
        }

        if ($this->lifetime) {
            return true;
        }

        return $this->aktif_sampai instanceof Carbon && $this->aktif_sampai->isFuture();
    }

    /**
     * Tim aktif tetapi masa langganannya habis: data tetap terbaca, hanya
     * tidak bisa menambah atau mengubah apa pun sampai diperpanjang.
     */
    public function hanyaBaca(): bool
    {
        return $this->isAktif() && ! $this->langgananBerlaku();
    }

    public function sisaHari(): ?int
    {
        if ($this->lifetime || ! $this->aktif_sampai instanceof Carbon) {
            return null;
        }

        return (int) max(0, now()->startOfDay()->diffInDays($this->aktif_sampai->copy()->startOfDay(), false));
    }

    public function masaBerlakuLabel(): string
    {
        if ($this->lifetime) {
            return 'Lifetime';
        }

        if (! $this->aktif_sampai) {
            return '-';
        }

        return $this->aktif_sampai->translatedFormat('d M Y');
    }

    public function kuotaTersisa(): int
    {
        return max(0, $this->max_users - $this->users()->count());
    }

    /**
     * Menerapkan satu paket yang sudah dibayar ke masa langganan.
     *
     * Perpanjangan dihitung dari tanggal berakhir bila masih berlaku, dari
     * hari ini bila sudah lewat, jadi membayar lebih awal tidak menghanguskan
     * sisa hari.
     *
     * @return array{0: Carbon, 1: Carbon|null}
     */
    public function terapkanPaket(Plan $plan): array
    {
        $mulai = $this->aktif_sampai instanceof Carbon && $this->aktif_sampai->isFuture()
            ? $this->aktif_sampai->copy()
            : now();

        $this->plan_id = $plan->id;
        $this->max_users = max((int) $this->max_users, $plan->max_users);
        $this->status = StatusTim::Aktif;
        $this->diaktifkan_pada ??= now();

        if ($plan->isLifetime()) {
            $this->lifetime = true;
            $this->aktif_sampai = null;
            $selesai = null;
        } else {
            $selesai = $mulai->copy()->addMonthsNoOverflow($plan->durasi_bulan);
            $this->aktif_sampai = $selesai;
        }

        $this->save();

        return [$mulai, $selesai];
    }

    // ------------------------------------------------------------------
    // Kode tim
    // ------------------------------------------------------------------

    public static function buatSlug(int $panjang = 4): string
    {
        $huruf = self::ALFABET_SLUG;

        for ($coba = 0; $coba < 50; $coba++) {
            $slug = '';

            for ($i = 0; $i < $panjang; $i++) {
                $slug .= $huruf[random_int(0, strlen($huruf) - 1)];
            }

            if (in_array($slug, self::SLUG_TERLARANG, true)) {
                continue;
            }

            if (! self::withTrashed()->where('slug', $slug)->exists()) {
                return $slug;
            }
        }

        // Sekitar 923 ribu kombinasi untuk 4 karakter. Bila sampai di sini
        // ruangnya hampir penuh: naik satu karakter daripada gagal mendaftar.
        return self::buatSlug($panjang + 1);
    }

    public static function normalkanSlug(string $slug): string
    {
        return strtoupper(trim($slug));
    }
}
