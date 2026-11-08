<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Kebutuhan satu material/upah untuk satu pekerjaan RAB.
 *
 * @property float $koefisien
 * @property float $qty
 * @property bool $qty_manual
 * @property float $harga_satuan
 * @property int $subtotal
 */
class PekerjaanMaterial extends Model
{
    protected $table = 'pekerjaan_material';

    protected $fillable = [
        'pekerjaan_id', 'material_id', 'nama', 'kategori', 'satuan', 'koefisien',
        'qty', 'qty_manual', 'harga_satuan', 'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'koefisien' => 'float',
            'qty' => 'float',
            'qty_manual' => 'boolean',
            'harga_satuan' => 'float',
            'subtotal' => 'integer',
        ];
    }

    /** @return BelongsTo<Pekerjaan, $this> */
    public function pekerjaan(): BelongsTo
    {
        return $this->belongsTo(Pekerjaan::class);
    }

    /** @return BelongsTo<Material, $this> */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class)->withTrashed();
    }
}
