<?php

declare(strict_types=1);

namespace App\Http\Resources\Rab;

use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Ruangan */
class RuanganResource extends JsonResource
{
    /** @var array<int, int> ruangan_id => total, diisi RabController */
    public static array $total = [];

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'lantai_id' => $this->lantai_id,
            'nama' => $this->nama,
            'panjang' => $this->panjang,
            'lebar' => $this->lebar,
            'tinggi' => $this->tinggi,
            'luas_lantai' => $this->luasLantai(),
            'luas_dinding' => $this->luasDinding(),
            'keterangan' => $this->keterangan,
            'total' => (int) (self::$total[$this->id] ?? 0),
        ];
    }
}
