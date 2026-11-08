<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\Peran;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Saas\KelolaAnggota;
use App\Support\ApiResponse;
use App\Support\Izin;
use App\Support\Tenancy\TimAktif;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

/**
 * Pengelolaan akun anggota tim oleh owner/kontraktor.
 *
 * Hanya anggota tim akun yang login yang terlihat; akun platform
 * (superadmin) tidak pernah bisa dibaca atau diubah lewat sini. Kuota akun
 * dan aturan "minimal satu pengelola aktif" ditegakkan KelolaAnggota, sama
 * dengan panel web.
 */
class UserController extends Controller
{
    public function __construct(private readonly KelolaAnggota $anggota) {}

    #[OA\Get(
        path: '/api/pengguna',
        operationId: 'penggunaIndex',
        description: 'Daftar akun anggota tim beserta kuota. Hanya owner/kontraktor.',
        summary: 'Daftar anggota tim',
        security: [['bearerAuth' => []]],
        tags: ['Pengguna'],
        parameters: [
            new OA\Parameter(name: 'search', description: 'Cari nama atau email', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', default: 25)),
        ],
        responses: [new OA\Response(response: 200, description: 'OK'), new OA\Response(response: 403, description: 'Bukan pengelola')],
    )]
    public function index(Request $request): JsonResponse
    {
        if (! Izin::kelolaPengguna($request->user())) {
            return $this->tolak();
        }

        $perPage = min(100, max(1, (int) $request->query('per_page', 25)));
        $cari = $request->query('search');

        $paginator = User::anggotaTim((int) TimAktif::id())
            ->when(filled($cari), function ($q) use ($cari) {
                $term = '%'.mb_strtolower((string) $cari).'%';
                $q->where(fn ($b) => $b->whereRaw('LOWER(name) LIKE ?', [$term])->orWhereRaw('LOWER(email) LIKE ?', [$term]));
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();

        $tim = TimAktif::get();
        $response = ApiResponse::paginated(UserResource::collection($paginator), 'Daftar anggota tim.');
        $body = $response->getData(true);
        $body['meta']['kuota'] = ['maksimal' => $tim?->max_users, 'terpakai' => $tim?->users()->count(), 'tersisa' => $tim?->kuotaTersisa()];

        return $response->setData($body);
    }

    #[OA\Post(
        path: '/api/pengguna',
        operationId: 'penggunaStore',
        description: 'Menambah akun anggota tim. Ditolak 422 bila kuota tim (termasuk owner) sudah penuh.',
        summary: 'Tambah anggota',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['name', 'email', 'password'], properties: [
            new OA\Property(property: 'name', type: 'string', example: 'Sinta Pratiwi'),
            new OA\Property(property: 'email', type: 'string', format: 'email'),
            new OA\Property(property: 'password', description: 'Minimal 8 karakter, ada huruf & angka', type: 'string', format: 'password'),
            new OA\Property(property: 'peran', type: 'string', enum: ['owner', 'kontraktor', 'mandor', 'anggota', 'viewer'], example: 'anggota'),
            new OA\Property(property: 'no_wa', type: 'string', nullable: true),
            new OA\Property(property: 'jabatan', type: 'string', nullable: true),
        ])),
        tags: ['Pengguna'],
        responses: [new OA\Response(response: 201, description: 'Dibuat'), new OA\Response(response: 403, description: 'Bukan pengelola'), new OA\Response(response: 422, description: 'Validasi gagal / kuota penuh')],
    )]
    public function store(Request $request): JsonResponse
    {
        if (! Izin::kelolaPengguna($request->user())) {
            return $this->tolak();
        }

        $this->kompatibel($request, null);
        $user = $this->anggota->tambah(TimAktif::get(), $request);

        return ApiResponse::success(new UserResource($user->fresh('team')), 'Akun anggota berhasil dibuat.', 201);
    }

    #[OA\Get(path: '/api/pengguna/{id}', operationId: 'penggunaShow', summary: 'Detail anggota', security: [['bearerAuth' => []]], tags: ['Pengguna'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))],
        responses: [new OA\Response(response: 200, description: 'OK')])]
    public function show(Request $request, string $user): JsonResponse
    {
        if (! Izin::kelolaPengguna($request->user())) {
            return $this->tolak();
        }

        return ApiResponse::success(new UserResource($this->cari($user)));
    }

    #[OA\Put(
        path: '/api/pengguna/{id}',
        operationId: 'penggunaUpdate',
        description: 'Mengubah data anggota. Kosongkan password bila tidak ingin menggantinya. Mengganti password atau menonaktifkan akun mencabut sesi dan sidik jarinya.',
        summary: 'Ubah anggota',
        security: [['bearerAuth' => []]],
        tags: ['Pengguna'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))],
        responses: [new OA\Response(response: 200, description: 'Tersimpan')],
    )]
    public function update(Request $request, string $user): JsonResponse
    {
        if (! Izin::kelolaPengguna($request->user())) {
            return $this->tolak();
        }

        $akun = $this->cari($user);
        $this->kompatibel($request, $akun);
        $akun = $this->anggota->ubah(TimAktif::get(), $akun, $request);

        return ApiResponse::success(new UserResource($akun->fresh('team')), 'Data anggota diperbarui.');
    }

    #[OA\Delete(
        path: '/api/pengguna/{id}',
        operationId: 'penggunaDestroy',
        description: 'Menonaktifkan akun dan mencabut seluruh sesinya. Akunnya tidak dihapus agar jejak siapa yang menginput data lama tetap terbaca. Pengelola aktif terakhir tidak bisa dinonaktifkan.',
        summary: 'Nonaktifkan anggota',
        security: [['bearerAuth' => []]],
        tags: ['Pengguna'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))],
        responses: [new OA\Response(response: 200, description: 'Dinonaktifkan')],
    )]
    public function destroy(Request $request, string $user): JsonResponse
    {
        if (! Izin::kelolaPengguna($request->user())) {
            return $this->tolak();
        }

        $akun = $this->cari($user);
        $request->merge([
            'name' => $akun->name,
            'email' => $akun->email,
            'peran' => $akun->peran?->value,
            'is_active' => false,
        ]);
        $akun = $this->anggota->ubah(TimAktif::get(), $akun, $request);

        return ApiResponse::success(new UserResource($akun->fresh('team')), 'Akun dinonaktifkan dan seluruh sesinya dicabut.');
    }

    /** Anggota tim aktif saja; ID tim lain atau akun platform -> 404. */
    private function cari(string $id): User
    {
        return User::anggotaTim((int) TimAktif::id())->whereKey($id)->firstOrFail();
    }

    /**
     * Klien lama (aplikasi sebelum SaaS) tidak mengirim `peran` dan memakai
     * `phone`; nilai yang hilang dilengkapi dari data yang ada.
     */
    private function kompatibel(Request $request, ?User $akun): void
    {
        $request->merge(array_filter([
            'peran' => $request->input('peran') ?? $akun?->peran?->value ?? Peran::Anggota->value,
            'name' => $request->input('name') ?? $akun?->name,
            'email' => $request->input('email') ?? $akun?->email,
            'no_wa' => $request->input('no_wa') ?? $request->input('phone') ?? $akun?->no_wa,
        ], fn ($v) => $v !== null));
    }

    private function tolak(): JsonResponse
    {
        return ApiResponse::error('Hanya owner atau kontraktor yang boleh mengelola akun tim.', 403, code: 'FORBIDDEN');
    }
}
