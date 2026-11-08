<?php

declare(strict_types=1);

namespace App\Http\Resources\Rab;

use App\Models\PekerjaanMaterial;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin PekerjaanMaterial */
class KebutuhanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'material_id' => $this->material_id,
            'nama' => $this->nama,
            'kategori' => $this->kategori,
            'satuan' => $this->satuan,
            'koefisien' => $this->koefisien,
            'qty' => (float) $this->qty,
            'qty_manual' => (bool) $this->qty_manual,
            'harga_satuan' => (float) $this->harga_satuan,
            'subtotal' => (int) $this->subtotal,
        ];
    }
}
