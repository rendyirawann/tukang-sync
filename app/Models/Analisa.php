<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\KategoriPekerjaan;
use App\Models\Concerns\MilikTim;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Analisa Harga Satuan Pekerjaan (AHSP): resep kebutuhan untuk 1 satuan.
 *
 * Contoh "Pasang 1 m² dinding bata merah": 70 bh bata, 0,22 zak semen,
 * 0,043 m³ pasir, 0,3 OH tukang, ...
 *
 * @property KategoriPekerjaan $kategori
 */
class Analisa extends Model
{
    use MilikTim, SoftDeletes;

    protected $table = 'analisa';

    protected $fillable = ['team_id', 'kode', 'nama', 'kategori', 'satuan', 'keterangan'];

    protected function casts(): array
    {
        return ['kategori' => KategoriPekerjaan::class];
    }

    /** @return HasMany<AnalisaItem, $this> */
    public function items(): HasMany
    {
        return $this->hasMany(AnalisaItem::class)->orderBy('urutan')->orderBy('id');
    }

    /** Harga per 1 satuan berdasarkan harga katalog terkini. */
    public function hargaSatuan(): float
    {
        $this->loadMissing('items.material');

        return (float) $this->items->sum(fn (AnalisaItem $i) => $i->koefisien * (float) ($i->material->harga ?? 0));
    }
}
