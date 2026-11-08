<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Pengelompokan pekerjaan mengikuti urutan lazim RAB bangunan di Indonesia,
 * supaya laporan tersusun seperti RAB yang biasa dibaca kontraktor.
 */
enum KategoriPekerjaan: string
{
    case Persiapan = 'persiapan';
    case Tanah = 'tanah';
    case Pondasi = 'pondasi';
    case Struktur = 'struktur';
    case Dinding = 'dinding';
    case Lantai = 'lantai';
    case KusenPintuJendela = 'kusen';
    case Atap = 'atap';
    case Plafon = 'plafon';
    case Finishing = 'finishing';
    case Listrik = 'listrik';
    case Sanitasi = 'sanitasi';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::Persiapan => 'Pekerjaan Persiapan',
            self::Tanah => 'Pekerjaan Tanah',
            self::Pondasi => 'Pekerjaan Pondasi',
            self::Struktur => 'Pekerjaan Struktur Beton',
            self::Dinding => 'Pekerjaan Dinding',
            self::Lantai => 'Pekerjaan Lantai',
            self::KusenPintuJendela => 'Pekerjaan Kusen, Pintu & Jendela',
            self::Atap => 'Pekerjaan Atap',
            self::Plafon => 'Pekerjaan Plafon',
            self::Finishing => 'Pekerjaan Finishing & Pengecatan',
            self::Listrik => 'Instalasi Listrik',
            self::Sanitasi => 'Instalasi Air & Sanitasi',
            self::Lainnya => 'Pekerjaan Lain-lain',
        };
    }

    /** @return array<string, string> */
    public static function opsi(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $k) => [$k->value => $k->label()])->all();
    }
}
