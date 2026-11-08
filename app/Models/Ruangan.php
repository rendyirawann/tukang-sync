<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\MilikTim;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ruangan extends Model
{
    use MilikTim, SoftDeletes;

    protected $table = 'ruangan';

    protected $fillable = ['team_id', 'lantai_id', 'nama', 'panjang', 'lebar', 'tinggi', 'keterangan', 'urutan'];

    protected function casts(): array
    {
        return ['panjang' => 'float', 'lebar' => 'float', 'tinggi' => 'float'];
    }

    public function lantai(): BelongsTo
    {
        return $this->belongsTo(Lantai::class);
    }

    public function pekerjaan(): HasMany
    {
        return $this->hasMany(Pekerjaan::class);
    }

    public function luasLantai(): ?float
    {
        return $this->panjang && $this->lebar ? round($this->panjang * $this->lebar, 2) : null;
    }

    /** Luas dinding keliling (tanpa dikurangi bukaan pintu/jendela). */
    public function luasDinding(): ?float
    {
        return $this->panjang && $this->lebar && $this->tinggi
            ? round(2 * ($this->panjang + $this->lebar) * $this->tinggi, 2)
            : null;
    }
}
