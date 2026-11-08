<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Rab;

use App\Http\Requests\Rab\LogHarianRequest;
use App\Http\Resources\Rab\LogHarianResource;
use App\Models\Kegiatan;
use App\Models\LogHarian;
use App\Support\ApiResponse;
use App\Support\Izin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class LogHarianController extends RabBaseController
{
    #[OA\Get(
        path: '/api/kegiatan/{kegiatan}/log-harian',
        operationId: 'logHarianIndex',
        summary: 'Log harian lapangan satu proyek',
        security: [['bearerAuth' => []]],
        tags: ['Log Harian'],
        parameters: [
            new OA\Parameter(name: 'kegiatan', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'per_page', in: 'query', schema: new OA\Schema(type: 'integer', default: 25)),
        ],
        responses: [new OA\Response(response: 200, description: 'OK (paginasi)')],
    )]
    public function index(Request $request, Kegiatan $kegiatan): JsonResponse
    {
        $perPage = min(100, max(1, (int) $request->query('per_page', 25)));

        $paginator = $kegiatan->logHarian()
            ->with('pembuat:id,name')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return ApiResponse::paginated(LogHarianResource::collection($paginator), 'Log harian.');
    }

    #[OA\Post(
        path: '/api/kegiatan/{kegiatan}/log-harian',
        operationId: 'logHarianStore',
        summary: 'Tulis log harian',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(required: ['tanggal', 'uraian'], properties: [
            new OA\Property(property: 'tanggal', type: 'string', format: 'date'),
            new OA\Property(property: 'cuaca', type: 'string', example: 'cerah'),
            new OA\Property(property: 'jumlah_pekerja', type: 'integer', example: 6),
            new OA\Property(property: 'uraian', type: 'string'),
            new OA\Property(property: 'kendala', type: 'string'),
        ])),
        tags: ['Log Harian'],
        parameters: [new OA\Parameter(name: 'kegiatan', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 201, description: 'Dibuat')],
    )]
    public function store(LogHarianRequest $request, Kegiatan $kegiatan): JsonResponse
    {
        $log = $kegiatan->logHarian()->create($request->validated() + ['created_by' => $this->userId($request)]);

        return ApiResponse::success(new LogHarianResource($log->load('pembuat')), 'Log harian disimpan.', 201);
    }

    #[OA\Put(path: '/api/log-harian/{logHarian}', operationId: 'logHarianUpdate', summary: 'Ubah log harian (pembuat atau pengelola)', security: [['bearerAuth' => []]], tags: ['Log Harian'],
        parameters: [new OA\Parameter(name: 'logHarian', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Tersimpan')])]
    public function update(LogHarianRequest $request, LogHarian $logHarian): JsonResponse
    {
        $this->wajib($this->bolehUbah($request, $logHarian));
        $logHarian->update($request->validated());

        return ApiResponse::success(new LogHarianResource($logHarian->load('pembuat')), 'Log harian diperbarui.');
    }

    #[OA\Delete(path: '/api/log-harian/{logHarian}', operationId: 'logHarianDestroy', summary: 'Hapus log harian (pembuat atau pengelola)', security: [['bearerAuth' => []]], tags: ['Log Harian'],
        parameters: [new OA\Parameter(name: 'logHarian', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Terhapus')])]
    public function destroy(Request $request, LogHarian $logHarian): JsonResponse
    {
        $this->wajib($this->bolehUbah($request, $logHarian));
        $logHarian->delete();

        return ApiResponse::success(null, 'Log harian dihapus.');
    }

    private function bolehUbah(Request $request, LogHarian $log): bool
    {
        $user = $request->user();

        return Izin::isiLogHarian($user) && ($user->isPengelola() || $log->created_by === $user->id);
    }
}
