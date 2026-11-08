<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Komponen fisik yang bisa dihitung di sebuah bangunan.
 *
 * Satuan bawaan dan kategori bawaan dipakai untuk mengisi form otomatis;
 * keduanya tetap bisa diubah per item.
 */
enum JenisKomponen: string
{
    case Pondasi = 'pondasi';
    case Sloof = 'sloof';
    case Kolom = 'kolom';
    case Balok = 'balok';
    case PlatLantai = 'plat';
    case Tangga = 'tangga';
    case Dinding = 'dinding';
    case Lantai = 'lantai';
    case Pintu = 'pintu';
    case Jendela = 'jendela';
    case Kusen = 'kusen';
    case Atap = 'atap';
    case Plafon = 'plafon';
    case Cat = 'cat';
    case TitikListrik = 'listrik';
    case TitikAir = 'air';
    case Sanitair = 'sanitair';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::Pondasi => 'Pondasi',
            self::Sloof => 'Sloof',
            self::Kolom => 'Kolom / Tiang',
            self::Balok => 'Balok / Ring Balok',
            self::PlatLantai => 'Plat Lantai / Dak',
            self::Tangga => 'Tangga',
            self::Dinding => 'Dinding',
            self::Lantai => 'Lantai / Keramik',
            self::Pintu => 'Pintu',
            self::Jendela => 'Jendela',
            self::Kusen => 'Kusen',
            self::Atap => 'Atap',
            self::Plafon => 'Plafon',
            self::Cat => 'Pengecatan',
            self::TitikListrik => 'Titik Listrik / Lampu',
            self::TitikAir => 'Titik Air / Pipa',
            self::Sanitair => 'Sanitair (closet, wastafel)',
            self::Lainnya => 'Lainnya',
        };
    }

    public function satuanBawaan(): string
    {
        return match ($this) {
            self::Pondasi, self::Sloof, self::Kolom, self::Balok, self::PlatLantai, self::Tangga => 'm3',
            self::Dinding, self::Lantai, self::Atap, self::Plafon, self::Cat => 'm2',
            self::Kusen => 'm1',
            self::Pintu, self::Jendela, self::TitikListrik, self::TitikAir, self::Sanitair => 'unit',
            self::Lainnya => 'ls',
        };
    }

    public function kategoriBawaan(): KategoriPekerjaan
    {
        return match ($this) {
            self::Pondasi => KategoriPekerjaan::Pondasi,
            self::Sloof, self::Kolom, self::Balok, self::PlatLantai, self::Tangga => KategoriPekerjaan::Struktur,
            self::Dinding => KategoriPekerjaan::Dinding,
            self::Lantai => KategoriPekerjaan::Lantai,
            self::Pintu, self::Jendela, self::Kusen => KategoriPekerjaan::KusenPintuJendela,
            self::Atap => KategoriPekerjaan::Atap,
            self::Plafon => KategoriPekerjaan::Plafon,
            self::Cat => KategoriPekerjaan::Finishing,
            self::TitikListrik => KategoriPekerjaan::Listrik,
            self::TitikAir, self::Sanitair => KategoriPekerjaan::Sanitasi,
            self::Lainnya => KategoriPekerjaan::Lainnya,
        };
    }

    /** @return array<string, string> */
    public static function opsi(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $k) => [$k->value => $k->label()])->all();
    }
}
