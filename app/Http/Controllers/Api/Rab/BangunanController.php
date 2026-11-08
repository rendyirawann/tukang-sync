<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Rab;

use App\Http\Resources\Rab\BangunanResource;
use App\Models\Bangunan;
use App\Models\Kegiatan;
use App\Support\ApiResponse;
use App\Support\Izin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

class BangunanController extends RabBaseController
{
    private function aturan(Request $request): array
    {
        $wajib = $request->isMethod('POST') ? 'required' : 'sometimes';

        return $request->validate([
            'nama' => [$wajib, 'string', 'max:120'],
            'jenis' => ['nullable', Rule::in(array_keys(Bangunan::JENIS))],
            'luas_tanah' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'luas_bangunan' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    #[OA\Post(
        path: '/api/kegiatan/{kegiatan}/bangunan',
        operationId: 'bangunanStore',
        summary: 'Tambah bangunan',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(required: ['nama'], properties: [
            new OA\Property(property: 'nama', type: 'string', example: 'Rumah Utama'),
            new OA\Property(property: 'jenis', type: 'string', example: 'rumah'),
            new OA\Property(property: 'luas_tanah', type: 'number'),
            new OA\Property(property: 'luas_bangunan', type: 'number'),
            new OA\Property(property: 'keterangan', type: 'string'),
        ])),
        tags: ['RAB'],
        parameters: [new OA\Parameter(name: 'kegiatan', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 201, description: 'Dibuat'), new OA\Response(response: 403, description: 'Bukan pengelola')],
    )]
    public function store(Request $request, Kegiatan $kegiatan): JsonResponse
    {
        $this->wajib(Izin::kelolaRab($request->user()));
        $data = $this->aturan($request);

        $bangunan = $kegiatan->bangunan()->create($data + [
            'jenis' => $data['jenis'] ?? 'rumah',
            'urutan' => $data['urutan'] ?? ((int) $kegiatan->bangunan()->max('urutan') + 1),
        ]);

        return ApiResponse::success(new BangunanResource($bangunan), 'Bangunan ditambahkan.', 201);
    }

    #[OA\Put(path: '/api/bangunan/{bangunan}', operationId: 'bangunanUpdate', summary: 'Ubah bangunan', security: [['bearerAuth' => []]], tags: ['RAB'],
        parameters: [new OA\Parameter(name: 'bangunan', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Tersimpan')])]
    public function update(Request $request, Bangunan $bangunan): JsonResponse
    {
        $this->wajib(Izin::kelolaRab($request->user()));
        $bangunan->update($this->aturan($request));

        return ApiResponse::success(new BangunanResource($bangunan), 'Bangunan diperbarui.');
    }

    #[OA\Delete(path: '/api/bangunan/{bangunan}', operationId: 'bangunanDestroy', summary: 'Hapus bangunan beserta lantai, ruangan, dan pekerjaannya', security: [['bearerAuth' => []]], tags: ['RAB'],
        parameters: [new OA\Parameter(name: 'bangunan', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Terhapus')])]
    public function destroy(Request $request, Bangunan $bangunan): JsonResponse
    {
        $this->wajib(Izin::kelolaRab($request->user()));

        // Soft delete tidak memicu cascade database, jadi anaknya ikut
        // dihapus manual supaya tidak muncul sebagai pekerjaan yatim.
        $bangunan->pekerjaan()->delete();

        foreach ($bangunan->lantai as $lantai) {
            $lantai->ruangan()->delete();
            $lantai->delete();
        }

        $bangunan->delete();

        return ApiResponse::success(null, 'Bangunan dihapus.');
    }
}
