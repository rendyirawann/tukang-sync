<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Rab;

use App\Enums\JenisKomponen;
use App\Enums\KategoriPekerjaan;
use App\Enums\SatuanVolume;
use App\Models\Bangunan;
use App\Models\LogHarian;
use App\Models\Material;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ReferensiRabController extends RabBaseController
{
    #[OA\Get(
        path: '/api/referensi/rab',
        operationId: 'referensiRab',
        description: 'Pilihan untuk form RAB: kategori pekerjaan, komponen (dengan satuan & kategori bawaan), satuan (dengan dimensi yang relevan), jenis bangunan, kategori material, cuaca.',
        summary: 'Referensi RAB',
        security: [['bearerAuth' => []]],
        tags: ['RAB'],
        responses: [new OA\Response(response: 200, description: 'OK')],
    )]
    public function __invoke(): JsonResponse
    {
        $pasang = fn (array $map) => collect($map)->map(fn ($label, $value) => ['value' => $value, 'label' => $label])->values();

        return ApiResponse::success([
            'kategori_pekerjaan' => collect(KategoriPekerjaan::cases())
                ->map(fn (KategoriPekerjaan $k) => ['value' => $k->value, 'label' => $k->label()]),
            'komponen' => collect(JenisKomponen::cases())->map(fn (JenisKomponen $k) => [
                'value' => $k->value,
                'label' => $k->label(),
                'satuan' => $k->satuanBawaan(),
                'kategori' => $k->kategoriBawaan()->value,
            ]),
            'satuan' => collect(SatuanVolume::cases())->map(fn (SatuanVolume $s) => [
                'value' => $s->value,
                'label' => $s->label(),
                'simbol' => $s->simbol(),
                'dimensi' => $s->dimensi(),
                'otomatis' => $s->otomatis(),
            ]),
            'jenis_bangunan' => $pasang(Bangunan::JENIS),
            'kategori_material' => $pasang(Material::KATEGORI),
            'cuaca' => $pasang(LogHarian::CUACA),
        ], 'Referensi RAB.');
    }
}
