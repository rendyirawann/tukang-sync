<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\MilikTim;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $nomor
 * @property float $luas
 * @property float $tinggi
 */
class Lantai extends Model
{
    use MilikTim, SoftDeletes;

    protected $table = 'lantai';

    protected $fillable = ['team_id', 'bangunan_id', 'nama', 'nomor', 'luas', 'tinggi', 'keterangan'];

    protected function casts(): array
    {
        return ['nomor' => 'integer', 'luas' => 'float', 'tinggi' => 'float'];
    }

    /** @return BelongsTo<Bangunan, $this> */
    public function bangunan(): BelongsTo
    {
        return $this->belongsTo(Bangunan::class);
    }

    /** @return HasMany<Ruangan, $this> */
    public function ruangan(): HasMany
    {
        return $this->hasMany(Ruangan::class)->orderBy('urutan')->orderBy('id');
    }

    /** @return HasMany<Pekerjaan, $this> */
    public function pekerjaan(): HasMany
    {
        return $this->hasMany(Pekerjaan::class);
    }
}
