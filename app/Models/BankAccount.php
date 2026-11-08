<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = ['bank', 'nomor', 'atas_nama', 'keterangan', 'aktif', 'urutan'];

    protected function casts(): array
    {
        return ['aktif' => 'boolean', 'urutan' => 'integer'];
    }

    public function scopeAktif(Builder $q): Builder
    {
        return $q->where('aktif', true)->orderBy('urutan')->orderBy('id');
    }
}
