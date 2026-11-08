<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property float $koefisien
 */
class AnalisaItem extends Model
{
    protected $fillable = ['analisa_id', 'material_id', 'koefisien', 'urutan'];

    protected function casts(): array
    {
        return ['koefisien' => 'float'];
    }

    /** @return BelongsTo<Analisa, $this> */
    public function analisa(): BelongsTo
    {
        return $this->belongsTo(Analisa::class);
    }

    /** @return BelongsTo<Material, $this> */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class)->withTrashed();
    }
}
