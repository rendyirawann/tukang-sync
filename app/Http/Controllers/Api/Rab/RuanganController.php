<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Rab;

use App\Http\Resources\Rab\RuanganResource;
use App\Models\Lantai;
use App\Models\Ruangan;
use App\Support\ApiResponse;
use App\Support\Izin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class RuanganController extends RabBaseController
{
    private function aturan(Request $request): array
    {
        $wajib = $request->isMethod('POST') ? 'required' : 'sometimes';

        return $request->validate([
            'nama' => [$wajib, 'string', 'max:120'],
            'panjang' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'lebar' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'tinggi' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'keterangan' => ['nullable', 'string', 'max:255'],
            'urutan' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    #[OA\Post(
        path: '/api/lantai/{lantai}/ruangan',
        operationId: 'ruanganStore',
        summary: 'Tambah ruangan / zona',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(required: ['nama'], properties: [
            new OA\Property(property: 'nama', type: 'string', example: 'Dapur'),
            new OA\Property(property: 'panjang', type: 'number', example: 3),
            new OA\Property(property: 'lebar', type: 'number', example: 3),
            new OA\Property(property: 'tinggi', type: 'number', example: 3.5),
        ])),
        tags: ['RAB'],
        parameters: [new OA\Parameter(name: 'lantai', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 201, description: 'Dibuat')],
    )]
    public function store(Request $request, Lantai $lantai): JsonResponse
    {
        $this->wajib(Izin::kelolaRab($request->user()));
        $ruangan = $lantai->ruangan()->create($this->aturan($request));

        return ApiResponse::success(new RuanganResource($ruangan), 'Ruangan ditambahkan.', 201);
    }

    #[OA\Put(path: '/api/ruangan/{ruangan}', operationId: 'ruanganUpdate', summary: 'Ubah ruangan', security: [['bearerAuth' => []]], tags: ['RAB'],
        parameters: [new OA\Parameter(name: 'ruangan', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Tersimpan')])]
    public function update(Request $request, Ruangan $ruangan): JsonResponse
    {
        $this->wajib(Izin::kelolaRab($request->user()));
        $ruangan->update($this->aturan($request));

        return ApiResponse::success(new RuanganResource($ruangan), 'Ruangan diperbarui.');
    }

    #[OA\Delete(path: '/api/ruangan/{ruangan}', operationId: 'ruanganDestroy', summary: 'Hapus ruangan beserta pekerjaannya', security: [['bearerAuth' => []]], tags: ['RAB'],
        parameters: [new OA\Parameter(name: 'ruangan', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Terhapus')])]
    public function destroy(Request $request, Ruangan $ruangan): JsonResponse
    {
        $this->wajib(Izin::kelolaRab($request->user()));

        $ruangan->pekerjaan()->delete();
        $ruangan->delete();

        return ApiResponse::success(null, 'Ruangan dihapus.');
    }
}
