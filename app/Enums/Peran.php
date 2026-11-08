<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Peran di dalam satu tim.
 *
 * Pengelola (owner, kontraktor) mengatur proyek, anggaran, anggota, dan
 * langganan. Pelaksana (mandor, anggota) mengisi data lapangan. Viewer hanya
 * membaca -- cocok untuk klien atau investor yang ingin memantau.
 */
enum Peran: string
{
    case Owner = 'owner';
    case Kontraktor = 'kontraktor';
    case Mandor = 'mandor';
    case Anggota = 'anggota';
    case Viewer = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::Owner => 'Owner',
            self::Kontraktor => 'Kontraktor',
            self::Mandor => 'Mandor / Pengawas',
            self::Anggota => 'Anggota',
            self::Viewer => 'Viewer (hanya lihat)',
        };
    }

    public function keterangan(): string
    {
        return match ($this) {
            self::Owner, self::Kontraktor => 'Akses penuh: proyek, RAB, keuangan, anggota, langganan.',
            self::Mandor => 'Mengisi progres, log harian, belanja bahan, dan upah.',
            self::Anggota => 'Mengisi belanja bahan, upah, dan administrasi.',
            self::Viewer => 'Hanya melihat proyek dan laporan.',
        };
    }

    public function pengelola(): bool
    {
        return in_array($this, [self::Owner, self::Kontraktor], true);
    }

    public function pelaksana(): bool
    {
        return in_array($this, [self::Mandor, self::Anggota], true);
    }

    /** Peran yang boleh dipilih saat mendaftar (pemilik akun tim). */
    public static function pendaftar(): array
    {
        return [self::Owner, self::Kontraktor];
    }

    /** @return array<string, string> */
    public static function opsi(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $p) => [$p->value => $p->label()])->all();
    }
}
