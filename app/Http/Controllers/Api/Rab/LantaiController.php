<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Rab;

use App\Http\Resources\Rab\LantaiResource;
use App\Models\Bangunan;
use App\Models\Lantai;
use App\Support\ApiResponse;
use App\Support\Izin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class LantaiController extends RabBaseController
{
    private function aturan(Request $request): array
    {
        $wajib = $request->isMethod('POST') ? 'required' : 'sometimes';

        return $request->validate([
            'nama' => [$wajib, 'string', 'max:80'],
            'nomor' => [$wajib, 'integer', 'min:-10', 'max:200'],
            'luas' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
            'tinggi' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);
    }

    #[OA\Post(
        path: '/api/bangunan/{bangunan}/lantai',
        operationId: 'lantaiStore',
        summary: 'Tambah lantai',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(required: ['nama', 'nomor'], properties: [
            new OA\Property(property: 'nama', type: 'string', example: 'Lantai 1'),
            new OA\Property(property: 'nomor', type: 'integer', example: 1),
            new OA\Property(property: 'luas', type: 'number'),
            new OA\Property(property: 'tinggi', type: 'number'),
        ])),
        tags: ['RAB'],
        parameters: [new OA\Parameter(name: 'bangunan', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 201, description: 'Dibuat')],
    )]
    public function store(Request $request, Bangunan $bangunan): JsonResponse
    {
        $this->wajib(Izin::kelolaRab($request->user()));
        $lantai = $bangunan->lantai()->create($this->aturan($request));

        return ApiResponse::success(new LantaiResource($lantai), 'Lantai ditambahkan.', 201);
    }

    #[OA\Put(path: '/api/lantai/{lantai}', operationId: 'lantaiUpdate', summary: 'Ubah lantai', security: [['bearerAuth' => []]], tags: ['RAB'],
        parameters: [new OA\Parameter(name: 'lantai', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Tersimpan')])]
    public function update(Request $request, Lantai $lantai): JsonResponse
    {
        $this->wajib(Izin::kelolaRab($request->user()));
        $lantai->update($this->aturan($request));

        return ApiResponse::success(new LantaiResource($lantai), 'Lantai diperbarui.');
    }

    #[OA\Delete(path: '/api/lantai/{lantai}', operationId: 'lantaiDestroy', summary: 'Hapus lantai beserta ruangan dan pekerjaannya', security: [['bearerAuth' => []]], tags: ['RAB'],
        parameters: [new OA\Parameter(name: 'lantai', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Terhapus')])]
    public function destroy(Request $request, Lantai $lantai): JsonResponse
    {
        $this->wajib(Izin::kelolaRab($request->user()));

        $lantai->pekerjaan()->delete();
        $lantai->ruangan()->delete();
        $lantai->delete();

        return ApiResponse::success(null, 'Lantai dihapus.');
    }
}
