<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalisaItem extends Model
{
    protected $fillable = ['analisa_id', 'material_id', 'koefisien', 'urutan'];

    protected function casts(): array
    {
        return ['koefisien' => 'float'];
    }

    public function analisa(): BelongsTo
    {
        return $this->belongsTo(Analisa::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class)->withTrashed();
    }
}
