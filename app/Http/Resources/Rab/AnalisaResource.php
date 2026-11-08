<?php

declare(strict_types=1);

namespace App\Http\Resources\Rab;

use App\Models\Analisa;
use App\Models\AnalisaItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Analisa */
class AnalisaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode' => $this->kode,
            'nama' => $this->nama,
            'kategori' => $this->kategori?->value,
            'kategori_label' => $this->kategori?->label(),
            'satuan' => $this->satuan,
            'keterangan' => $this->keterangan,
            'harga_satuan' => round($this->hargaSatuan(), 2),
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn (AnalisaItem $i) => [
                'id' => $i->id,
                'material_id' => $i->material_id,
                'nama' => $i->material?->nama,
                'kategori' => $i->material?->kategori,
                'satuan' => $i->material?->satuan,
                'koefisien' => (float) $i->koefisien,
                'harga' => (float) ($i->material->harga ?? 0),
                'subtotal' => round($i->koefisien * (float) ($i->material->harga ?? 0), 2),
            ])->values()),
        ];
    }
}
