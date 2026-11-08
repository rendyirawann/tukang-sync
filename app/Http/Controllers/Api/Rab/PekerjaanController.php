<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Rab;

use App\Http\Requests\Rab\PekerjaanRequest;
use App\Http\Resources\Rab\PekerjaanResource;
use App\Models\Analisa;
use App\Models\Bangunan;
use App\Models\Kegiatan;
use App\Models\Lantai;
use App\Models\Pekerjaan;
use App\Models\Ruangan;
use App\Services\Rab\RabCalculator;
use App\Support\ApiResponse;
use App\Support\Izin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class PekerjaanController extends RabBaseController
{
    public function __construct(private readonly RabCalculator $rab) {}

    #[OA\Get(
        path: '/api/kegiatan/{kegiatan}/pekerjaan',
        operationId: 'pekerjaanIndex',
        summary: 'Daftar item RAB proyek (tanpa rincian kebutuhan)',
        security: [['bearerAuth' => []]],
        tags: ['RAB'],
        parameters: [
            new OA\Parameter(name: 'kegiatan', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'bangunan_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'lantai_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'ruangan_id', in: 'query', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter(name: 'kategori', in: 'query', schema: new OA\Schema(type: 'string')),
        ],
        responses: [new OA\Response(response: 200, description: 'OK')],
    )]
    public function index(Request $request, Kegiatan $kegiatan): JsonResponse
    {
        $daftar = $kegiatan->pekerjaan()
            ->with(['bangunan:id,nama', 'lantai:id,nama', 'ruangan:id,nama', 'analisa:id,nama'])
            ->when($request->filled('bangunan_id'), fn ($q) => $q->where('bangunan_id', $request->integer('bangunan_id')))
            ->when($request->filled('lantai_id'), fn ($q) => $q->where('lantai_id', $request->integer('lantai_id')))
            ->when($request->filled('ruangan_id'), fn ($q) => $q->where('ruangan_id', $request->integer('ruangan_id')))
            ->when($request->filled('kategori'), fn ($q) => $q->where('kategori', $request->query('kategori')))
            ->orderBy('urutan')->orderBy('id')
            ->get();

        return ApiResponse::success(PekerjaanResource::collection($daftar), 'Daftar pekerjaan.');
    }

    #[OA\Get(path: '/api/pekerjaan/{pekerjaan}', operationId: 'pekerjaanShow', summary: 'Detail pekerjaan beserta kebutuhan material', security: [['bearerAuth' => []]], tags: ['RAB'],
        parameters: [new OA\Parameter(name: 'pekerjaan', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'OK')])]
    public function show(Pekerjaan $pekerjaan): JsonResponse
    {
        return ApiResponse::success($this->lengkap($pekerjaan), 'Detail pekerjaan.');
    }

    #[OA\Post(
        path: '/api/kegiatan/{kegiatan}/pekerjaan',
        operationId: 'pekerjaanStore',
        description: 'Volume dihitung dari jumlah × dimensi menurut satuan (unit/titik/ls = jumlah, m1 = ×panjang, m2 = ×panjang×(lebar atau tinggi), m3 = ×p×l×t) kecuali volume_manual. Bila analisa_id diisi, kebutuhan material dibuat dari analisa dengan harga katalog saat ini.',
        summary: 'Tambah item RAB',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(required: ['kategori', 'nama', 'jumlah', 'satuan'], properties: [
            new OA\Property(property: 'kategori', type: 'string', example: 'kusen'),
            new OA\Property(property: 'komponen', type: 'string', example: 'pintu'),
            new OA\Property(property: 'nama', type: 'string', example: 'Pintu kamar'),
            new OA\Property(property: 'bangunan_id', type: 'integer'),
            new OA\Property(property: 'lantai_id', type: 'integer'),
            new OA\Property(property: 'ruangan_id', type: 'integer'),
            new OA\Property(property: 'jumlah', type: 'number', example: 3),
            new OA\Property(property: 'panjang', type: 'number'),
            new OA\Property(property: 'lebar', type: 'number'),
            new OA\Property(property: 'tinggi', type: 'number'),
            new OA\Property(property: 'satuan', type: 'string', example: 'unit'),
            new OA\Property(property: 'volume_manual', type: 'boolean'),
            new OA\Property(property: 'volume', type: 'number'),
            new OA\Property(property: 'harga_satuan', type: 'number'),
            new OA\Property(property: 'analisa_id', type: 'integer'),
            new OA\Property(property: 'progres', type: 'integer'),
        ])),
        tags: ['RAB'],
        parameters: [new OA\Parameter(name: 'kegiatan', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 201, description: 'Dibuat'), new OA\Response(response: 403, description: 'Bukan pengelola'), new OA\Response(response: 422, description: 'Validasi gagal / lokasi tidak konsisten')],
    )]
    public function store(PekerjaanRequest $request, Kegiatan $kegiatan): JsonResponse
    {
        $data = $request->validated();
        $this->periksaLokasi($kegiatan, $data);

        $pekerjaan = DB::transaction(function () use ($request, $kegiatan, $data) {
            $p = new Pekerjaan(collect($data)->except('analisa_id')->all());
            $p->kegiatan_id = $kegiatan->id;
            $p->komponen ??= 'lainnya';
            $p->created_by = $this->userId($request);
            $p->urutan = $data['urutan'] ?? ((int) $kegiatan->pekerjaan()->max('urutan') + 1);
            $p->save();

            return ! empty($data['analisa_id'])
                ? $this->rab->terapkanAnalisa($p, Analisa::findOrFail($data['analisa_id']))
                : $this->rab->hitungUlang($p);
        });

        return ApiResponse::success($this->lengkap($pekerjaan), 'Pekerjaan ditambahkan.', 201);
    }

    #[OA\Put(path: '/api/pekerjaan/{pekerjaan}', operationId: 'pekerjaanUpdate', summary: 'Ubah item RAB (mengganti analisa_id menerapkan ulang analisa)', security: [['bearerAuth' => []]], tags: ['RAB'],
        parameters: [new OA\Parameter(name: 'pekerjaan', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Tersimpan')])]
    public function update(PekerjaanRequest $request, Pekerjaan $pekerjaan): JsonResponse
    {
        $data = $request->validated();
        $gabungan = array_merge($pekerjaan->only(['bangunan_id', 'lantai_id', 'ruangan_id']), $data);
        $this->periksaLokasi($pekerjaan->kegiatan, $gabungan);

        $kunciLokasi = ['bangunan_id', 'lantai_id', 'ruangan_id'];

        if (array_intersect_key($data, array_flip($kunciLokasi)) !== []) {
            $data = array_merge($data, array_intersect_key($gabungan, array_flip($kunciLokasi)));
        }

        $pekerjaan = DB::transaction(function () use ($pekerjaan, $data) {
            $analisaBaru = array_key_exists('analisa_id', $data) && $data['analisa_id'] && (int) $data['analisa_id'] !== $pekerjaan->analisa_id;

            $pekerjaan->fill(collect($data)->except('analisa_id')->all());

            if (array_key_exists('analisa_id', $data) && ! $data['analisa_id']) {
                $pekerjaan->analisa_id = null;
            }

            $pekerjaan->save();

            return $analisaBaru
                ? $this->rab->terapkanAnalisa($pekerjaan, Analisa::findOrFail($data['analisa_id']))
                : $this->rab->hitungUlang($pekerjaan);
        });

        return ApiResponse::success($this->lengkap($pekerjaan), 'Pekerjaan diperbarui.');
    }

    #[OA\Delete(path: '/api/pekerjaan/{pekerjaan}', operationId: 'pekerjaanDestroy', summary: 'Hapus item RAB', security: [['bearerAuth' => []]], tags: ['RAB'],
        parameters: [new OA\Parameter(name: 'pekerjaan', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Terhapus')])]
    public function destroy(Request $request, Pekerjaan $pekerjaan): JsonResponse
    {
        $this->wajib(Izin::kelolaRab($request->user()));
        $pekerjaan->delete();

        return ApiResponse::success(null, 'Pekerjaan dihapus.');
    }

    #[OA\Patch(
        path: '/api/pekerjaan/{pekerjaan}/progres',
        operationId: 'pekerjaanProgres',
        description: 'Boleh diisi pengelola maupun mandor/anggota.',
        summary: 'Perbarui progres pekerjaan',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(required: ['progres'], properties: [
            new OA\Property(property: 'progres', type: 'integer', example: 75),
        ])),
        tags: ['RAB'],
        parameters: [new OA\Parameter(name: 'pekerjaan', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Tersimpan')],
    )]
    public function progres(Request $request, Pekerjaan $pekerjaan): JsonResponse
    {
        $this->wajib(Izin::isiProgres($request->user()));
        $data = $request->validate(['progres' => ['required', 'integer', 'min:0', 'max:100']]);

        $pekerjaan->update(['progres' => $data['progres']]);

        return ApiResponse::success($this->lengkap($pekerjaan), 'Progres diperbarui.');
    }

    #[OA\Post(
        path: '/api/pekerjaan/{pekerjaan}/analisa',
        operationId: 'pekerjaanAnalisa',
        summary: 'Terapkan (ulang) analisa ke pekerjaan',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(required: ['analisa_id'], properties: [
            new OA\Property(property: 'analisa_id', type: 'integer'),
        ])),
        tags: ['RAB'],
        parameters: [new OA\Parameter(name: 'pekerjaan', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Diterapkan')],
    )]
    public function analisa(Request $request, Pekerjaan $pekerjaan): JsonResponse
    {
        $this->wajib(Izin::kelolaRab($request->user()));
        $data = $request->validate(['analisa_id' => ['required', 'integer', PekerjaanRequest::milikTim('analisa')]]);

        $pekerjaan = $this->rab->terapkanAnalisa($pekerjaan, Analisa::findOrFail($data['analisa_id']));

        return ApiResponse::success($this->lengkap($pekerjaan), 'Analisa diterapkan.');
    }

    #[OA\Post(path: '/api/pekerjaan/{pekerjaan}/segarkan-harga', operationId: 'pekerjaanSegarkanHarga', summary: 'Ambil ulang harga kebutuhan dari katalog terkini', security: [['bearerAuth' => []]], tags: ['RAB'],
        parameters: [new OA\Parameter(name: 'pekerjaan', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Diperbarui')])]
    public function segarkanHarga(Request $request, Pekerjaan $pekerjaan): JsonResponse
    {
        $this->wajib(Izin::kelolaRab($request->user()));

        return ApiResponse::success($this->lengkap($this->rab->segarkanHarga($pekerjaan)), 'Harga diperbarui dari katalog.');
    }

    // ------------------------------------------------------------------

    public static function lengkapStatic(Pekerjaan $p): PekerjaanResource
    {
        return new PekerjaanResource($p->fresh(['bangunan', 'lantai', 'ruangan', 'analisa', 'kebutuhan']));
    }

    private function lengkap(Pekerjaan $p): PekerjaanResource
    {
        return self::lengkapStatic($p);
    }

    /**
     * Bangunan harus milik proyek ini, lantai milik bangunan itu, ruangan
     * milik lantai itu. Level atas diisi otomatis dari level bawah bila
     * kosong, sehingga klien cukup mengirim ruangan_id.
     *
     * @param  array<string, mixed>  $data
     */
    private function periksaLokasi(Kegiatan $kegiatan, array &$data): void
    {
        $ruangan = ! empty($data['ruangan_id']) ? Ruangan::find($data['ruangan_id']) : null;
        $lantai = ! empty($data['lantai_id']) ? Lantai::find($data['lantai_id']) : null;

        if ($ruangan) {
            if ($lantai && $ruangan->lantai_id !== $lantai->id) {
                $this->gagal('ruangan_id', 'Ruangan tidak berada di lantai yang dipilih.');
            }
            $lantai ??= $ruangan->lantai;
            $data['lantai_id'] = $lantai?->id;
        }

        $bangunan = ! empty($data['bangunan_id']) ? Bangunan::find($data['bangunan_id']) : null;

        if ($lantai) {
            if ($bangunan && $lantai->bangunan_id !== $bangunan->id) {
                $this->gagal('lantai_id', 'Lantai tidak berada di bangunan yang dipilih.');
            }
            $bangunan ??= $lantai->bangunan;
            $data['bangunan_id'] = $bangunan?->id;
        }

        if ($bangunan && $bangunan->kegiatan_id !== $kegiatan->id) {
            $this->gagal('bangunan_id', 'Bangunan bukan milik proyek ini.');
        }
    }

    private function gagal(string $field, string $pesan): never
    {
        throw ValidationException::withMessages([$field => $pesan]);
    }
}
