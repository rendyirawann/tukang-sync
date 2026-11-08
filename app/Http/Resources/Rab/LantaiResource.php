<?php

declare(strict_types=1);

namespace App\Http\Resources\Rab;

use App\Models\Lantai;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Lantai */
class LantaiResource extends JsonResource
{
    /** @var array<int, int> lantai_id => total */
    public static array $total = [];

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bangunan_id' => $this->bangunan_id,
            'nama' => $this->nama,
            'nomor' => $this->nomor,
            'luas' => $this->luas,
            'tinggi' => $this->tinggi,
            'keterangan' => $this->keterangan,
            'total' => (int) (self::$total[$this->id] ?? 0),
            'ruangan' => RuanganResource::collection($this->whenLoaded('ruangan')),
        ];
    }
}
