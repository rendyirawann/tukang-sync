<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Satuan volume pekerjaan dan cara menghitungnya dari dimensi.
 *
 *   unit / titik / ls  -> jumlah
 *   m1 (meter lari)    -> jumlah × panjang
 *   m2                 -> jumlah × panjang × (lebar, atau tinggi untuk dinding)
 *   m3                 -> jumlah × panjang × lebar × tinggi
 *   kg / lainnya       -> diisi manual
 */
enum SatuanVolume: string
{
    case Unit = 'unit';
    case Titik = 'titik';
    case Lumpsum = 'ls';
    case MeterLari = 'm1';
    case MeterPersegi = 'm2';
    case MeterKubik = 'm3';
    case Kilogram = 'kg';

    public function label(): string
    {
        return match ($this) {
            self::Unit => 'unit / buah',
            self::Titik => 'titik',
            self::Lumpsum => 'ls (lumpsum)',
            self::MeterLari => "m' (meter lari)",
            self::MeterPersegi => 'm² (luas)',
            self::MeterKubik => 'm³ (volume)',
            self::Kilogram => 'kg',
        };
    }

    public function simbol(): string
    {
        return match ($this) {
            self::MeterLari => "m'",
            self::MeterPersegi => 'm²',
            self::MeterKubik => 'm³',
            default => $this->value,
        };
    }

    /** Dimensi yang relevan, untuk menampilkan isian yang perlu saja. */
    public function dimensi(): array
    {
        return match ($this) {
            self::MeterLari => ['panjang'],
            self::MeterPersegi => ['panjang', 'lebar'],
            self::MeterKubik => ['panjang', 'lebar', 'tinggi'],
            default => [],
        };
    }

    public function otomatis(): bool
    {
        return $this !== self::Kilogram;
    }

    public function hitung(float $jumlah, ?float $p, ?float $l, ?float $t): float
    {
        return match ($this) {
            self::Unit, self::Titik, self::Lumpsum, self::Kilogram => $jumlah,
            self::MeterLari => $jumlah * (float) $p,
            // Dinding diisi panjang × tinggi: lebar kosong berarti pakai tinggi.
            self::MeterPersegi => $jumlah * (float) $p * (float) ($l ?: $t),
            self::MeterKubik => $jumlah * (float) $p * (float) $l * (float) $t,
        };
    }

    /** @return array<string, string> */
    public static function opsi(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $s) => [$s->value => $s->label()])->all();
    }
}
