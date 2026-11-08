<?php

declare(strict_types=1);

namespace App\Http\Resources\Rab;

use App\Models\LogHarian;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin LogHarian */
class LogHarianResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kegiatan_id' => $this->kegiatan_id,
            'tanggal' => $this->tanggal?->toDateString(),
            'cuaca' => $this->cuaca,
            'cuaca_label' => LogHarian::CUACA[$this->cuaca] ?? null,
            'jumlah_pekerja' => $this->jumlah_pekerja,
            'uraian' => $this->uraian,
            'kendala' => $this->kendala,
            'dibuat_oleh' => $this->pembuat ? ['id' => $this->pembuat->id, 'name' => $this->pembuat->name] : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
