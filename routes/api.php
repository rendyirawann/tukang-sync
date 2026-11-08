<?php

declare(strict_types=1);

use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BahanBakuController;
use App\Http\Controllers\Api\CashFlowController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\KegiatanController;
use App\Http\Controllers\Api\LampiranController;
use App\Http\Controllers\Api\MasterDataController;
use App\Http\Controllers\Api\Rab\AnalisaController;
use App\Http\Controllers\Api\Rab\BangunanController;
use App\Http\Controllers\Api\Rab\KebutuhanController;
use App\Http\Controllers\Api\Rab\LantaiController;
use App\Http\Controllers\Api\Rab\LogHarianController;
use App\Http\Controllers\Api\Rab\MaterialController;
use App\Http\Controllers\Api\Rab\PekerjaanController;
use App\Http\Controllers\Api\Rab\RabController;
use App\Http\Controllers\Api\Rab\ReferensiRabController;
use App\Http\Controllers\Api\Rab\RuanganController;
use App\Http\Controllers\Api\ReferenceController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Dokumentasi Swagger: /api/documentation    Spesifikasi JSON: /docs
|
| Semua endpoint terproteksi berjalan dalam konteks SATU tim (middleware
| tim.api): data tim lain tidak pernah terlihat, dan saat masa langganan
| habis permintaan yang mengubah data dijawab 402 SUBSCRIPTION_EXPIRED.
*/

Route::get('/health', HealthController::class)->name('api.health');

// ---------------------------------------------------------------------
// Publik. Inilah satu-satunya pintu masuk tanpa token, jadi batasnya paling
// ketat di seluruh aplikasi: dihitung per kombinasi email + IP supaya orang
// lain tidak bisa mengunci akun Anda hanya dengan salah memasukkan sandi
// berulang kali. Rinciannya di RateLimitServiceProvider.
// ---------------------------------------------------------------------
// Penjaga BANJIR (20/menit per IP), sengaja jauh lebih longgar dari limiter
// "login" panel web: penebakan sandi sudah dikunci AuthController (5 gagal
// per email+IP, dengan Retry-After), dan pesan itu harus sampai ke aplikasi
// lebih dulu daripada 429 generik.
Route::prefix('auth')->middleware('throttle:20,1')->group(function (): void {
    Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');
    Route::post('/biometric/login', [AuthController::class, 'biometricLogin'])->name('api.auth.biometric.login');
});

// ---------------------------------------------------------------------
// Terproteksi
// ---------------------------------------------------------------------
Route::middleware(['auth:sanctum', 'tim.api'])->group(function (): void {

    Route::prefix('auth')->group(function (): void {
        Route::get('/me', [AuthController::class, 'me'])->name('api.auth.me');
        Route::put('/profil', [AuthController::class, 'updateProfil'])->name('api.auth.profil.update');
        Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
        Route::post('/change-password', [AuthController::class, 'changePassword'])->name('api.auth.change-password');
        Route::post('/biometric/enable', [AuthController::class, 'enableBiometric'])->name('api.auth.biometric.enable');
        Route::delete('/biometric', [AuthController::class, 'disableBiometric'])->name('api.auth.biometric.disable');
    });

    // ---- Referensi & default rate ----
    Route::prefix('referensi')->group(function (): void {
        Route::get('/', [ReferenceController::class, 'index'])->name('api.referensi.index');
    });

    // ---- Kegiatan ----
    // `preview` didaftarkan lebih dulu agar tidak tertangkap {kegiatan}.
    Route::post('/kegiatan/preview', [KegiatanController::class, 'preview'])->name('api.kegiatan.preview');

    Route::apiResource('kegiatan', KegiatanController::class)
        ->parameters(['kegiatan' => 'kegiatan'])
        ->names('api.kegiatan');

    // ---- Arus kas milik satu kegiatan ----
    Route::prefix('kegiatan/{kegiatan}/cash-flows')->group(function (): void {
        Route::get('/rekap', [CashFlowController::class, 'rekap'])->name('api.kegiatan.cash-flows.rekap');
        Route::get('/', [CashFlowController::class, 'index'])->name('api.kegiatan.cash-flows.index');
        Route::post('/', [CashFlowController::class, 'store'])->name('api.kegiatan.cash-flows.store');
    });

    // ---- Arus kas lintas kegiatan ----
    Route::prefix('cash-flows')->group(function (): void {
        Route::get('/', [CashFlowController::class, 'all'])->name('api.cash-flows.index');
        Route::get('/{cashFlow}', [CashFlowController::class, 'show'])->name('api.cash-flows.show');
        Route::put('/{cashFlow}', [CashFlowController::class, 'update'])->name('api.cash-flows.update');
        Route::delete('/{cashFlow}', [CashFlowController::class, 'destroy'])->name('api.cash-flows.destroy');
    });

    // ---- Rincian bahan baku (sumber angka "Bahan Baku") ----
    Route::prefix('kegiatan/{kegiatan}/bahan-baku')->group(function (): void {
        Route::get('/', [BahanBakuController::class, 'index'])->name('api.bahan-baku.index');
        Route::post('/', [BahanBakuController::class, 'store'])->name('api.bahan-baku.store');
    });

    Route::prefix('bahan-baku')->group(function (): void {
        Route::put('/{bahanBaku}', [BahanBakuController::class, 'update'])->name('api.bahan-baku.update');
        Route::delete('/{bahanBaku}', [BahanBakuController::class, 'destroy'])->name('api.bahan-baku.destroy');
    });

    // ---- Lampiran bukti (foto struk belanja) ----
    Route::prefix('kegiatan/{kegiatan}/lampiran')->group(function (): void {
        Route::get('/', [LampiranController::class, 'index'])->name('api.lampiran.index');
        Route::post('/', [LampiranController::class, 'store'])->name('api.lampiran.store');
    });

    Route::prefix('lampiran')->group(function (): void {
        Route::get('/{lampiran}/berkas', [LampiranController::class, 'berkas'])->name('api.lampiran.berkas');
        Route::delete('/{lampiran}', [LampiranController::class, 'destroy'])->name('api.lampiran.destroy');
    });

    // ---- Akun petugas (khusus superadmin) ----
    Route::apiResource('pengguna', UserController::class)
        ->parameters(['pengguna' => 'user'])
        ->names('api.pengguna');

    // ---- Data master (satuan, toko, sumber dana) ----
    //
    // Membaca terbuka untuk semua peran: pilihannya dibutuhkan saat mengisi
    // bahan baku dan kegiatan. Menulis dijaga MasterDataRequest -> superadmin.
    Route::prefix('master')->group(function (): void {
        Route::get('/', [MasterDataController::class, 'index'])->name('api.master.index');
        Route::get('/{jenis}', [MasterDataController::class, 'perJenis'])->name('api.master.jenis');
        Route::post('/{jenis}', [MasterDataController::class, 'store'])->name('api.master.store');
        Route::match(['put', 'patch'], '/{master}', [MasterDataController::class, 'update'])->name('api.master.update');
        Route::delete('/{master}', [MasterDataController::class, 'destroy'])->name('api.master.destroy');
    });

    // ---- Jejak aktivitas ----
    // "saya" didaftarkan lebih dulu agar tidak tertangkap rute lain, dan
    // dipisah dari "/" supaya petugas tidak punya jalan untuk melihat
    // aktivitas akun lain.
    Route::prefix('aktivitas')->group(function (): void {
        Route::get('/saya', [ActivityLogController::class, 'saya'])->name('api.aktivitas.saya');
        Route::get('/ringkasan', [ActivityLogController::class, 'ringkasan'])->name('api.aktivitas.ringkasan');
        Route::get('/', [ActivityLogController::class, 'index'])->name('api.aktivitas.index');
    });

    // ---- RAB (docs/API-RAB.md) ----
    Route::get('/referensi/rab', ReferensiRabController::class)->name('api.referensi.rab');

    Route::prefix('kegiatan/{kegiatan}')->group(function (): void {
        Route::get('/rab', [RabController::class, 'show'])->name('api.rab.show');
        Route::get('/rekap-material', [RabController::class, 'rekapMaterial'])->name('api.rab.rekap-material');
        Route::post('/bangunan', [BangunanController::class, 'store'])->name('api.bangunan.store');
        Route::get('/pekerjaan', [PekerjaanController::class, 'index'])->name('api.pekerjaan.index');
        Route::post('/pekerjaan', [PekerjaanController::class, 'store'])->name('api.pekerjaan.store');
        Route::get('/log-harian', [LogHarianController::class, 'index'])->name('api.log-harian.index');
        Route::post('/log-harian', [LogHarianController::class, 'store'])->name('api.log-harian.store');
    });

    Route::put('/bangunan/{bangunan}', [BangunanController::class, 'update'])->name('api.bangunan.update');
    Route::delete('/bangunan/{bangunan}', [BangunanController::class, 'destroy'])->name('api.bangunan.destroy');
    Route::post('/bangunan/{bangunan}/lantai', [LantaiController::class, 'store'])->name('api.lantai.store');

    Route::put('/lantai/{lantai}', [LantaiController::class, 'update'])->name('api.lantai.update');
    Route::delete('/lantai/{lantai}', [LantaiController::class, 'destroy'])->name('api.lantai.destroy');
    Route::post('/lantai/{lantai}/ruangan', [RuanganController::class, 'store'])->name('api.ruangan.store');

    Route::put('/ruangan/{ruangan}', [RuanganController::class, 'update'])->name('api.ruangan.update');
    Route::delete('/ruangan/{ruangan}', [RuanganController::class, 'destroy'])->name('api.ruangan.destroy');

    Route::prefix('pekerjaan/{pekerjaan}')->group(function (): void {
        Route::get('/', [PekerjaanController::class, 'show'])->name('api.pekerjaan.show');
        Route::put('/', [PekerjaanController::class, 'update'])->name('api.pekerjaan.update');
        Route::delete('/', [PekerjaanController::class, 'destroy'])->name('api.pekerjaan.destroy');
        Route::patch('/progres', [PekerjaanController::class, 'progres'])->name('api.pekerjaan.progres');
        Route::post('/analisa', [PekerjaanController::class, 'analisa'])->name('api.pekerjaan.analisa');
        Route::post('/segarkan-harga', [PekerjaanController::class, 'segarkanHarga'])->name('api.pekerjaan.segarkan-harga');
        Route::post('/kebutuhan', [KebutuhanController::class, 'store'])->name('api.kebutuhan.store');
    });

    Route::put('/kebutuhan/{kebutuhan}', [KebutuhanController::class, 'update'])->name('api.kebutuhan.update');
    Route::delete('/kebutuhan/{kebutuhan}', [KebutuhanController::class, 'destroy'])->name('api.kebutuhan.destroy');

    Route::apiResource('material', MaterialController::class)->except('show')
        ->parameters(['material' => 'material'])->names('api.material');
    Route::apiResource('analisa', AnalisaController::class)
        ->parameters(['analisa' => 'analisa'])->names('api.analisa');

    Route::put('/log-harian/{logHarian}', [LogHarianController::class, 'update'])->name('api.log-harian.update');
    Route::delete('/log-harian/{logHarian}', [LogHarianController::class, 'destroy'])->name('api.log-harian.destroy');

    // ---- Laporan ----
    Route::prefix('laporan')->group(function (): void {
        Route::get('/ringkasan', [ReportController::class, 'ringkasan'])->name('api.laporan.ringkasan');
        Route::get('/rekap-kegiatan', [ReportController::class, 'rekapKegiatan'])->name('api.laporan.rekap');
        Route::get('/rekap-kegiatan/pdf', [ReportController::class, 'rekapPdf'])->name('api.laporan.rekap.pdf');
        Route::get('/arus-kas/pdf', [ReportController::class, 'arusKasPdf'])->name('api.laporan.arus-kas.pdf');
        Route::get('/kegiatan/{kegiatan}/pdf', [ReportController::class, 'kegiatanPdf'])->name('api.laporan.kegiatan.pdf');
    });
});

// ---------------------------------------------------------------------
// Fallback: URL API yang tidak dikenal tetap membalas JSON, bukan HTML
// ---------------------------------------------------------------------
Route::fallback(fn () => ApiResponse::error('Endpoint tidak ditemukan.', 404, code: 'NOT_FOUND'));
