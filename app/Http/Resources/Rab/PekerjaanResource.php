<?php

declare(strict_types=1);

namespace App\Http\Resources\Rab;

use App\Models\Pekerjaan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Pekerjaan */
class PekerjaanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kegiatan_id' => $this->kegiatan_id,
            'bangunan_id' => $this->bangunan_id,
            'lantai_id' => $this->lantai_id,
            'ruangan_id' => $this->ruangan_id,
            'lokasi' => $this->lokasiLabel(),
            'kategori' => $this->kategori?->value,
            'kategori_label' => $this->kategori?->label(),
            'komponen' => $this->komponen?->value,
            'komponen_label' => $this->komponen?->label(),
            'nama' => $this->nama,
            'jumlah' => (float) $this->jumlah,
            'panjang' => $this->panjang,
            'lebar' => $this->lebar,
            'tinggi' => $this->tinggi,
            'satuan' => $this->satuan,
            'satuan_simbol' => $this->satuanSimbol(),
            'volume' => (float) $this->volume,
            'volume_manual' => (bool) $this->volume_manual,
            'harga_satuan' => (float) $this->harga_satuan,
            'total' => (int) $this->total,
            'progres' => (int) $this->progres,
            'nilai_progres' => $this->nilaiProgres(),
            'analisa_id' => $this->analisa_id,
            'analisa_nama' => $this->analisa?->nama,
            'rencana_mulai' => $this->rencana_mulai?->toDateString(),
            'rencana_selesai' => $this->rencana_selesai?->toDateString(),
            'keterangan' => $this->keterangan,
            'kebutuhan' => KebutuhanResource::collection($this->whenLoaded('kebutuhan')),
        ];
    }
}
