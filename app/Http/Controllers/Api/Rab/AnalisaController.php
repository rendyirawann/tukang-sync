<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Rab;

use App\Http\Requests\Rab\AnalisaRequest;
use App\Http\Resources\Rab\AnalisaResource;
use App\Models\Analisa;
use App\Support\ApiResponse;
use App\Support\Izin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class AnalisaController extends RabBaseController
{
    #[OA\Get(
        path: '/api/analisa',
        operationId: 'analisaIndex',
        summary: 'Template analisa harga satuan (AHSP) milik tim',
        security: [['bearerAuth' => []]],
        tags: ['Katalog'],
        parameters: [
            new OA\Parameter(name: 'kategori', in: 'query', schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'search', in: 'query', schema: new OA\Schema(type: 'string')),
        ],
        responses: [new OA\Response(response: 200, description: 'OK')],
    )]
    public function index(Request $request): JsonResponse
    {
        $cari = trim((string) $request->query('search'));

        $daftar = Analisa::query()
            ->with('items.material')
            ->when($request->filled('kategori'), fn ($q) => $q->where('kategori', $request->query('kategori')))
            ->when($cari !== '', fn ($q) => $q->whereRaw('LOWER(nama) LIKE ?', ['%'.mb_strtolower($cari).'%']))
            ->orderBy('kategori')->orderBy('nama')
            ->get();

        // items dimuat hanya untuk menghitung harga_satuan; tidak ikut dikirim.
        return ApiResponse::success(
            $daftar->map(fn (Analisa $a) => collect((new AnalisaResource($a))->resolve($request))->except('items')),
            'Daftar analisa.',
        );
    }

    #[OA\Get(path: '/api/analisa/{analisa}', operationId: 'analisaShow', summary: 'Detail analisa beserta koefisien', security: [['bearerAuth' => []]], tags: ['Katalog'],
        parameters: [new OA\Parameter(name: 'analisa', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK')])]
    public function show(Analisa $analisa): JsonResponse
    {
        return ApiResponse::success(new AnalisaResource($analisa->load('items.material')), 'Detail analisa.');
    }

    #[OA\Post(
        path: '/api/analisa',
        operationId: 'analisaStore',
        summary: 'Tambah analisa',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(required: ['nama', 'kategori', 'satuan'], properties: [
            new OA\Property(property: 'kode', type: 'string'),
            new OA\Property(property: 'nama', type: 'string', example: 'Pasang dinding bata ringan'),
            new OA\Property(property: 'kategori', type: 'string', example: 'dinding'),
            new OA\Property(property: 'satuan', type: 'string', example: 'm2'),
            new OA\Property(property: 'items', type: 'array', items: new OA\Items(properties: [
                new OA\Property(property: 'material_id', type: 'integer'),
                new OA\Property(property: 'koefisien', type: 'number'),
            ])),
        ])),
        tags: ['Katalog'],
        responses: [new OA\Response(response: 201, description: 'Dibuat')],
    )]
    public function store(AnalisaRequest $request): JsonResponse
    {
        $analisa = DB::transaction(function () use ($request) {
            $analisa = Analisa::create(collect($request->validated())->except('items')->all());
            $this->simpanItems($analisa, $request->validated('items', []));

            return $analisa;
        });

        return ApiResponse::success(new AnalisaResource($analisa->load('items.material')), 'Analisa ditambahkan.', 201);
    }

    #[OA\Put(path: '/api/analisa/{analisa}', operationId: 'analisaUpdate', summary: 'Ubah analisa (items dikirim = mengganti seluruh koefisien)', security: [['bearerAuth' => []]], tags: ['Katalog'],
        parameters: [new OA\Parameter(name: 'analisa', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Tersimpan')])]
    public function update(AnalisaRequest $request, Analisa $analisa): JsonResponse
    {
        DB::transaction(function () use ($request, $analisa) {
            $analisa->update(collect($request->validated())->except('items')->all());

            if ($request->has('items')) {
                $analisa->items()->delete();
                $this->simpanItems($analisa, $request->validated('items', []));
            }
        });

        return ApiResponse::success(new AnalisaResource($analisa->fresh()->load('items.material')), 'Analisa diperbarui.');
    }

    #[OA\Delete(path: '/api/analisa/{analisa}', operationId: 'analisaDestroy', summary: 'Hapus analisa (RAB yang sudah memakainya tidak berubah)', security: [['bearerAuth' => []]], tags: ['Katalog'],
        parameters: [new OA\Parameter(name: 'analisa', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Terhapus')])]
    public function destroy(Request $request, Analisa $analisa): JsonResponse
    {
        $this->wajib(Izin::kelolaKatalog($request->user()));
        $analisa->delete();

        return ApiResponse::success(null, 'Analisa dihapus.');
    }

    /** @param array<int, array{material_id:int, koefisien:float}> $items */
    private function simpanItems(Analisa $analisa, array $items): void
    {
        foreach (array_values($items) as $i => $item) {
            $analisa->items()->create([
                'material_id' => $item['material_id'],
                'koefisien' => $item['koefisien'],
                'urutan' => $i,
            ]);
        }
    }
}
