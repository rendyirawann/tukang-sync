<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Rab;

use App\Http\Requests\Rab\PekerjaanRequest;
use App\Models\Material;
use App\Models\Pekerjaan;
use App\Models\PekerjaanMaterial;
use App\Services\Rab\RabCalculator;
use App\Support\ApiResponse;
use App\Support\Izin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

/**
 * Rincian kebutuhan satu pekerjaan. Tabelnya tidak membawa team_id, jadi
 * kepemilikan dijaga lewat pekerjaan induknya (yang ber-scope tim).
 */
class KebutuhanController extends RabBaseController
{
    public function __construct(private readonly RabCalculator $rab) {}

    #[OA\Post(
        path: '/api/pekerjaan/{pekerjaan}/kebutuhan',
        operationId: 'kebutuhanStore',
        description: 'Kirim material_id (nama/satuan/harga diambil dari katalog) atau nama+satuan+harga_satuan untuk item bebas. Isi koefisien (per satuan pekerjaan) atau qty (jumlah tetap).',
        summary: 'Tambah kebutuhan material ke pekerjaan',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
            new OA\Property(property: 'material_id', type: 'integer'),
            new OA\Property(property: 'nama', type: 'string'),
            new OA\Property(property: 'satuan', type: 'string'),
            new OA\Property(property: 'kategori', type: 'string'),
            new OA\Property(property: 'harga_satuan', type: 'number'),
            new OA\Property(property: 'koefisien', type: 'number'),
            new OA\Property(property: 'qty', type: 'number'),
        ])),
        tags: ['RAB'],
        parameters: [new OA\Parameter(name: 'pekerjaan', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 201, description: 'Ditambahkan; respons = Pekerjaan lengkap')],
    )]
    public function store(Request $request, Pekerjaan $pekerjaan): JsonResponse
    {
        $this->wajib(Izin::kelolaRab($request->user()));

        $data = $request->validate([
            'material_id' => ['nullable', 'integer', PekerjaanRequest::milikTim('materials')],
            'nama' => ['required_without:material_id', 'nullable', 'string', 'max:150'],
            'satuan' => ['required_without:material_id', 'nullable', 'string', 'max:30'],
            'kategori' => ['nullable', Rule::in(array_keys(Material::KATEGORI))],
            'harga_satuan' => ['required_without:material_id', 'nullable', 'numeric', 'min:0'],
            'koefisien' => ['required_without:qty', 'nullable', 'numeric', 'min:0'],
            'qty' => ['required_without:koefisien', 'nullable', 'numeric', 'min:0'],
        ]);

        $material = ! empty($data['material_id']) ? Material::find($data['material_id']) : null;
        $pakaiQty = isset($data['qty']) && ($data['koefisien'] ?? null) === null;

        $pekerjaan->kebutuhan()->create([
            'material_id' => $material?->id,
            'nama' => $data['nama'] ?? $material?->nama,
            'kategori' => $data['kategori'] ?? $material->kategori ?? 'material',
            'satuan' => $data['satuan'] ?? $material?->satuan,
            'harga_satuan' => $data['harga_satuan'] ?? $material->harga ?? 0,
            'koefisien' => $pakaiQty ? null : $data['koefisien'],
            'qty' => $pakaiQty ? $data['qty'] : 0,
            'qty_manual' => $pakaiQty,
        ]);

        $this->rab->hitungUlang($pekerjaan);

        return ApiResponse::success(PekerjaanController::lengkapStatic($pekerjaan), 'Kebutuhan ditambahkan.', 201);
    }

    #[OA\Put(path: '/api/kebutuhan/{kebutuhan}', operationId: 'kebutuhanUpdate', description: 'Mengisi qty membuat qty tetap (qty_manual=true); mengisi koefisien mengembalikannya ke hitungan otomatis.', summary: 'Ubah kebutuhan', security: [['bearerAuth' => []]], tags: ['RAB'],
        parameters: [new OA\Parameter(name: 'kebutuhan', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Tersimpan; respons = Pekerjaan lengkap')])]
    public function update(Request $request, PekerjaanMaterial $kebutuhan): JsonResponse
    {
        $this->wajib(Izin::kelolaRab($request->user()));
        $pekerjaan = $this->induk($kebutuhan);

        $data = $request->validate([
            'koefisien' => ['nullable', 'numeric', 'min:0'],
            'qty' => ['nullable', 'numeric', 'min:0'],
            'harga_satuan' => ['nullable', 'numeric', 'min:0'],
        ]);

        if (array_key_exists('qty', $data) && $data['qty'] !== null) {
            $kebutuhan->qty = $data['qty'];
            $kebutuhan->qty_manual = true;
        } elseif (array_key_exists('koefisien', $data) && $data['koefisien'] !== null) {
            $kebutuhan->koefisien = $data['koefisien'];
            $kebutuhan->qty_manual = false;
        }

        if (array_key_exists('harga_satuan', $data) && $data['harga_satuan'] !== null) {
            $kebutuhan->harga_satuan = $data['harga_satuan'];
        }

        $kebutuhan->save();
        $this->rab->hitungUlang($pekerjaan);

        return ApiResponse::success(PekerjaanController::lengkapStatic($pekerjaan), 'Kebutuhan diperbarui.');
    }

    #[OA\Delete(path: '/api/kebutuhan/{kebutuhan}', operationId: 'kebutuhanDestroy', summary: 'Hapus kebutuhan', security: [['bearerAuth' => []]], tags: ['RAB'],
        parameters: [new OA\Parameter(name: 'kebutuhan', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Terhapus; respons = Pekerjaan lengkap')])]
    public function destroy(Request $request, PekerjaanMaterial $kebutuhan): JsonResponse
    {
        $this->wajib(Izin::kelolaRab($request->user()));
        $pekerjaan = $this->induk($kebutuhan);

        $kebutuhan->delete();
        $this->rab->hitungUlang($pekerjaan);

        return ApiResponse::success(PekerjaanController::lengkapStatic($pekerjaan), 'Kebutuhan dihapus.');
    }

    /** Pekerjaan induk lewat query ber-scope tim: tim lain -> 404. */
    private function induk(PekerjaanMaterial $kebutuhan): Pekerjaan
    {
        return Pekerjaan::query()->findOrFail($kebutuhan->pekerjaan_id);
    }
}
