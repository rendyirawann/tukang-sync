<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Rab;

use App\Http\Requests\Rab\MaterialRequest;
use App\Http\Resources\Rab\MaterialResource;
use App\Models\Material;
use App\Support\ApiResponse;
use App\Support\Izin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class MaterialController extends RabBaseController
{
    #[OA\Get(
        path: '/api/material',
        operationId: 'materialIndex',
        summary: 'Katalog material, upah, dan alat milik tim',
        security: [['bearerAuth' => []]],
        tags: ['Katalog'],
        parameters: [
            new OA\Parameter(name: 'kategori', in: 'query', schema: new OA\Schema(type: 'string', enum: ['material', 'upah', 'alat'])),
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'aktif', in: 'query', schema: new OA\Schema(type: 'boolean')),
        ],
        responses: [new OA\Response(response: 200, description: 'OK')],
    )]
    public function index(Request $request): JsonResponse
    {
        $cari = trim((string) $request->query('search'));

        $daftar = Material::query()
            ->when($request->filled('kategori'), fn ($q) => $q->where('kategori', $request->query('kategori')))
            ->when($request->has('aktif'), fn ($q) => $q->where('aktif', $request->boolean('aktif')))
            ->when($cari !== '', fn ($q) => $q->where(fn ($w) => $w
                ->whereRaw('LOWER(nama) LIKE ?', ['%'.mb_strtolower($cari).'%'])
                ->orWhereRaw('LOWER(COALESCE(kode, \'\')) LIKE ?', ['%'.mb_strtolower($cari).'%'])))
            ->orderBy('kategori')->orderBy('nama')
            ->get();

        return ApiResponse::success(MaterialResource::collection($daftar), 'Katalog material.');
    }

    #[OA\Post(
        path: '/api/material',
        operationId: 'materialStore',
        summary: 'Tambah material',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(required: ['nama', 'kategori', 'satuan', 'harga'], properties: [
            new OA\Property(property: 'kode', type: 'string'),
            new OA\Property(property: 'nama', type: 'string', example: 'Semen PC 50 kg'),
            new OA\Property(property: 'kategori', type: 'string', example: 'material'),
            new OA\Property(property: 'satuan', type: 'string', example: 'zak'),
            new OA\Property(property: 'harga', type: 'number', example: 70000),
            new OA\Property(property: 'merek', type: 'string'),
            new OA\Property(property: 'toko', type: 'string'),
            new OA\Property(property: 'aktif', type: 'boolean'),
        ])),
        tags: ['Katalog'],
        responses: [new OA\Response(response: 201, description: 'Dibuat')],
    )]
    public function store(MaterialRequest $request): JsonResponse
    {
        $material = Material::create($request->validated() + ['aktif' => $request->boolean('aktif', true)]);

        return ApiResponse::success(new MaterialResource($material), 'Material ditambahkan.', 201);
    }

    #[OA\Put(path: '/api/material/{material}', operationId: 'materialUpdate', description: 'Harga baru tidak mengubah RAB yang sudah tersusun; pakai segarkan-harga pada pekerjaan bila ingin.', summary: 'Ubah material', security: [['bearerAuth' => []]], tags: ['Katalog'],
        parameters: [new OA\Parameter(name: 'material', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Tersimpan')])]
    public function update(MaterialRequest $request, Material $material): JsonResponse
    {
        $material->update($request->validated());

        return ApiResponse::success(new MaterialResource($material), 'Material diperbarui.');
    }

    #[OA\Delete(path: '/api/material/{material}', operationId: 'materialDestroy', summary: 'Hapus material (soft delete)', security: [['bearerAuth' => []]], tags: ['Katalog'],
        parameters: [new OA\Parameter(name: 'material', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Terhapus')])]
    public function destroy(Request $request, Material $material): JsonResponse
    {
        $this->wajib(Izin::kelolaKatalog($request->user()));
        $material->delete();

        return ApiResponse::success(null, 'Material dihapus.');
    }
}
