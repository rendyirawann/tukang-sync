<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AdminContact extends Model
{
    protected $fillable = ['nama', 'no_wa', 'keterangan', 'aktif', 'utama', 'urutan'];

    protected function casts(): array
    {
        return ['aktif' => 'boolean', 'utama' => 'boolean', 'urutan' => 'integer'];
    }

    public function scopeAktif(Builder $q): Builder
    {
        return $q->where('aktif', true)->orderByDesc('utama')->orderBy('urutan')->orderBy('id');
    }

    /** Kontak yang dipakai tombol "Konfirmasi via WhatsApp". */
    public static function utama(): ?self
    {
        return self::query()->aktif()->first();
    }

    /** Nomor format internasional tanpa tanda plus, untuk wa.me. */
    public function nomorInternasional(): string
    {
        $angka = preg_replace('/\D+/', '', (string) $this->no_wa) ?? '';

        if (str_starts_with($angka, '0')) {
            return '62'.substr($angka, 1);
        }

        return $angka;
    }

    public function linkWa(string $pesan = ''): string
    {
        return 'https://wa.me/'.$this->nomorInternasional()
            .($pesan !== '' ? '?text='.rawurlencode($pesan) : '');
    }
}
