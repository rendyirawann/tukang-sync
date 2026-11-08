<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\MilikTim;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Katalog harga material, upah, dan alat milik satu tim.
 *
 * @property float $harga
 * @property bool $aktif
 */
class Material extends Model
{
    use MilikTim, SoftDeletes;

    public const KATEGORI = [
        'material' => 'Material',
        'upah' => 'Upah Tenaga',
        'alat' => 'Sewa Alat',
    ];

    protected $fillable = [
        'team_id', 'kode', 'nama', 'kategori', 'satuan', 'harga', 'merek', 'toko', 'keterangan', 'aktif',
    ];

    protected function casts(): array
    {
        return ['harga' => 'float', 'aktif' => 'boolean'];
    }

    public function scopeAktif(Builder $q): Builder
    {
        return $q->where('aktif', true)->orderBy('kategori')->orderBy('nama');
    }

    public function kategoriLabel(): string
    {
        return self::KATEGORI[$this->kategori] ?? ucfirst($this->kategori);
    }
}
