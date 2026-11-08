<?php

declare(strict_types=1);

namespace App\Http\Resources\Rab;

use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Material */
class MaterialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode' => $this->kode,
            'nama' => $this->nama,
            'kategori' => $this->kategori,
            'kategori_label' => $this->kategoriLabel(),
            'satuan' => $this->satuan,
            'harga' => (float) $this->harga,
            'merek' => $this->merek,
            'toko' => $this->toko,
            'keterangan' => $this->keterangan,
            'aktif' => (bool) $this->aktif,
        ];
    }
}
