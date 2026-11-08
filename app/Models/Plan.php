<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'kode', 'nama', 'harga', 'durasi_bulan', 'max_users', 'deskripsi', 'fitur', 'aktif', 'urutan',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'integer',
            'durasi_bulan' => 'integer',
            'max_users' => 'integer',
            'fitur' => 'array',
            'aktif' => 'boolean',
        ];
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function scopeAktif(Builder $q): Builder
    {
        return $q->where('aktif', true)->orderBy('urutan')->orderBy('harga');
    }

    public function isLifetime(): bool
    {
        return $this->durasi_bulan === null;
    }

    public function periodeLabel(): string
    {
        if ($this->isLifetime()) {
            return 'sekali bayar, selamanya';
        }

        return $this->durasi_bulan === 1 ? 'per bulan' : "per {$this->durasi_bulan} bulan";
    }

    public function hargaLabel(): string
    {
        return 'Rp '.number_format($this->harga, 0, ',', '.');
    }
}
