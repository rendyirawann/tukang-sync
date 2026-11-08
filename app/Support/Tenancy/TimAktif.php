<?php

declare(strict_types=1);

namespace App\Support\Tenancy;

use App\Models\Team;

/**
 * Tim yang sedang dilayani request ini.
 *
 * Diisi middleware: dari slug URL untuk panel web tim, dari akun yang login
 * untuk API. Seluruh model ber-trait MilikTim membaca nilai ini untuk
 * menyaring query dan mengisi team_id saat membuat baris baru.
 *
 * Disimpan sebagai properti statis, bukan singleton container, agar tetap
 * bisa dibaca dari global scope model tanpa resolusi container -- dan
 * DIRESET di akhir setiap request (lihat AppServiceProvider) supaya worker
 * Octane tidak membawa tim request sebelumnya.
 */
final class TimAktif
{
    private static ?Team $tim = null;

    private function __construct() {}

    public static function set(?Team $tim): void
    {
        self::$tim = $tim;
    }

    public static function get(): ?Team
    {
        return self::$tim;
    }

    public static function id(): ?int
    {
        if (self::$tim !== null) {
            return self::$tim->id;
        }

        // Jaring pengaman: anggota tim yang sudah login selalu terkunci ke
        // timnya sendiri walau middleware lupa dipasang di suatu rute.
        $user = auth()->user();

        return $user?->team_id;
    }

    public static function reset(): void
    {
        self::$tim = null;
    }

    /**
     * Menjalankan callback dengan tim tertentu, lalu mengembalikan nilai
     * sebelumnya. Dipakai superadmin saat mengelola data milik sebuah tim.
     */
    public static function dengan(Team $tim, callable $callback): mixed
    {
        $sebelum = self::$tim;
        self::$tim = $tim;

        try {
            return $callback();
        } finally {
            self::$tim = $sebelum;
        }
    }
}
