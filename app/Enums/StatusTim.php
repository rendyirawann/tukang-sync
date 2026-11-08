<?php

declare(strict_types=1);

namespace App\Enums;

enum StatusTim: string
{
    case Menunggu = 'menunggu';
    case Aktif = 'aktif';
    case Ditangguhkan = 'ditangguhkan';

    public function label(): string
    {
        return match ($this) {
            self::Menunggu => 'Menunggu pembayaran',
            self::Aktif => 'Aktif',
            self::Ditangguhkan => 'Ditangguhkan',
        };
    }

    public function warna(): string
    {
        return match ($this) {
            self::Menunggu => 'warning',
            self::Aktif => 'success',
            self::Ditangguhkan => 'danger',
        };
    }
}
