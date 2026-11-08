<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Rab;

use App\Http\Resources\Rab\BangunanResource;
use App\Http\Resources\Rab\LantaiResource;
use App\Http\Resources\Rab\RuanganResource;
use App\Models\Kegiatan;
use App\Models\Pekerjaan;
use App\Services\Rab\RabCalculator;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class RabController extends RabBaseController
{
    public function __construct(private readonly RabCalculator $rab) {}

    #[OA\Get(
        path: '/api/kegiatan/{kegiatan}/rab',
        operationId: 'rabRingkasan',
        description: 'Ringkasan RAB (total, progres tertimbang, per kategori, per lokasi, realisasi belanja) beserta struktur bangunan › lantai › ruangan lengkap dengan total RAB tiap level.',
        summary: 'Ringkasan & struktur RAB proyek',
        security: [['bearerAuth' => []]],
        tags: ['RAB'],
        parameters: [new OA\Parameter(name: 'kegiatan', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 404, description: 'Proyek tidak ditemukan')],
    )]
    public function show(Kegiatan $kegiatan): JsonResponse
    {
        $total = fn (string $kolom) => Pekerjaan::query()
            ->where('kegiatan_id', $kegiatan->id)
            ->whereNotNull($kolom)
            ->groupBy($kolom)
            ->selectRaw("{$kolom} AS k, SUM(total) AS t")
            ->pluck('t', 'k')
            ->map(fn ($v) => (int) $v)
            ->all();

        BangunanResource::$total = $total('bangunan_id');
        LantaiResource::$total = $total('lantai_id');
        RuanganResource::$total = $total('ruangan_id');

        $bangunan = $kegiatan->bangunan()->with('lantai.ruangan')->get();

        return ApiResponse::success([
            'ringkasan' => $this->rab->ringkasan($kegiatan),
            'bangunan' => BangunanResource::collection($bangunan),
        ], 'Ringkasan RAB.');
    }

    #[OA\Get(
        path: '/api/kegiatan/{kegiatan}/rekap-material',
        operationId: 'rabRekapMaterial',
        description: 'Total kebutuhan material/upah seluruh pekerjaan (daftar belanja), dibandingkan dengan pembelian nyata (bahan baku yang ditautkan ke material yang sama).',
        summary: 'Rekap kebutuhan material',
        security: [['bearerAuth' => []]],
        tags: ['RAB'],
        parameters: [new OA\Parameter(name: 'kegiatan', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK')],
    )]
    public function rekapMaterial(Kegiatan $kegiatan): JsonResponse
    {
        return ApiResponse::success($this->rab->rekapMaterial($kegiatan)->values(), 'Rekap kebutuhan material.');
    }
}
