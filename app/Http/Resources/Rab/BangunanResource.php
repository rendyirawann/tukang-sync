<?php

declare(strict_types=1);

namespace App\Http\Resources\Rab;

use App\Models\Bangunan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Bangunan */
class BangunanResource extends JsonResource
{
    /** @var array<int, int> bangunan_id => total */
    public static array $total = [];

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kegiatan_id' => $this->kegiatan_id,
            'nama' => $this->nama,
            'jenis' => $this->jenis,
            'jenis_label' => $this->jenisLabel(),
            'luas_tanah' => $this->luas_tanah,
            'luas_bangunan' => $this->luas_bangunan,
            'keterangan' => $this->keterangan,
            'total' => (int) (self::$total[$this->id] ?? 0),
            'lantai' => LantaiResource::collection($this->whenLoaded('lantai')),
        ];
    }
}
