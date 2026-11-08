<?php

use App\Http\Controllers\Tim\AkunController;
use App\Http\Controllers\Tim\AnalisaController;
use App\Http\Controllers\Tim\AnggotaController;
use App\Http\Controllers\Tim\BelanjaController;
use App\Http\Controllers\Tim\LanggananController;
use App\Http\Controllers\Tim\LogHarianController;
use App\Http\Controllers\Tim\MaterialController;
use App\Http\Controllers\Tim\PekerjaanController;
use App\Http\Controllers\Tim\ProyekController;
use App\Http\Controllers\Tim\StrukturController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Modul panel tim
|--------------------------------------------------------------------------
| Di-require dari routes/tim.php di dalam prefix {tim}, nama 'tim.', dan
| middleware auth + tim:anggota. Model anak dimuat lewat scope MilikTim,
| lalu controller memastikan anak memang milik proyek di URL.
*/

// --- Proyek ---
Route::get('/proyek', [ProyekController::class, 'index'])->name('proyek.index');
Route::get('/proyek/baru', [ProyekController::class, 'create'])->name('proyek.create');
Route::post('/proyek', [ProyekController::class, 'store'])->name('proyek.store');
Route::get('/proyek/{kegiatan}', [ProyekController::class, 'show'])->name('proyek.show');
Route::get('/proyek/{kegiatan}/ubah', [ProyekController::class, 'edit'])->name('proyek.edit');
Route::put('/proyek/{kegiatan}', [ProyekController::class, 'update'])->name('proyek.update');
Route::delete('/proyek/{kegiatan}', [ProyekController::class, 'destroy'])->name('proyek.destroy');

// --- Struktur bangunan > lantai > ruangan ---
Route::post('/proyek/{kegiatan}/bangunan', [StrukturController::class, 'storeBangunan'])->name('proyek.bangunan.store');
Route::put('/proyek/{kegiatan}/bangunan/{bangunan}', [StrukturController::class, 'updateBangunan'])->name('proyek.bangunan.update');
Route::delete('/proyek/{kegiatan}/bangunan/{bangunan}', [StrukturController::class, 'destroyBangunan'])->name('proyek.bangunan.destroy');
Route::post('/proyek/{kegiatan}/bangunan/{bangunan}/lantai', [StrukturController::class, 'storeLantai'])->name('proyek.lantai.store');
Route::put('/proyek/{kegiatan}/lantai/{lantai}', [StrukturController::class, 'updateLantai'])->name('proyek.lantai.update');
Route::delete('/proyek/{kegiatan}/lantai/{lantai}', [StrukturController::class, 'destroyLantai'])->name('proyek.lantai.destroy');
Route::post('/proyek/{kegiatan}/lantai/{lantai}/ruangan', [StrukturController::class, 'storeRuangan'])->name('proyek.ruangan.store');
Route::put('/proyek/{kegiatan}/ruangan/{ruangan}', [StrukturController::class, 'updateRuangan'])->name('proyek.ruangan.update');
Route::delete('/proyek/{kegiatan}/ruangan/{ruangan}', [StrukturController::class, 'destroyRuangan'])->name('proyek.ruangan.destroy');

// --- Pekerjaan (item RAB) ---
Route::get('/proyek/{kegiatan}/pekerjaan/baru', [PekerjaanController::class, 'create'])->name('proyek.pekerjaan.create');
Route::post('/proyek/{kegiatan}/pekerjaan', [PekerjaanController::class, 'store'])->name('proyek.pekerjaan.store');
Route::get('/proyek/{kegiatan}/pekerjaan/{pekerjaan}', [PekerjaanController::class, 'show'])->name('proyek.pekerjaan.show');
Route::get('/proyek/{kegiatan}/pekerjaan/{pekerjaan}/ubah', [PekerjaanController::class, 'edit'])->name('proyek.pekerjaan.edit');
Route::put('/proyek/{kegiatan}/pekerjaan/{pekerjaan}', [PekerjaanController::class, 'update'])->name('proyek.pekerjaan.update');
Route::delete('/proyek/{kegiatan}/pekerjaan/{pekerjaan}', [PekerjaanController::class, 'destroy'])->name('proyek.pekerjaan.destroy');
Route::patch('/proyek/{kegiatan}/pekerjaan/{pekerjaan}/progres', [PekerjaanController::class, 'progres'])->name('proyek.pekerjaan.progres');
Route::post('/proyek/{kegiatan}/pekerjaan/{pekerjaan}/segarkan-harga', [PekerjaanController::class, 'segarkanHarga'])->name('proyek.pekerjaan.segarkan');

// --- Log harian & belanja ---
Route::post('/proyek/{kegiatan}/log-harian', [LogHarianController::class, 'store'])->name('proyek.log.store');
Route::put('/proyek/{kegiatan}/log-harian/{logHarian}', [LogHarianController::class, 'update'])->name('proyek.log.update');
Route::delete('/proyek/{kegiatan}/log-harian/{logHarian}', [LogHarianController::class, 'destroy'])->name('proyek.log.destroy');
Route::post('/proyek/{kegiatan}/belanja', [BelanjaController::class, 'store'])->name('proyek.belanja.store');
Route::delete('/proyek/{kegiatan}/belanja/{bahanBaku}', [BelanjaController::class, 'destroy'])->name('proyek.belanja.destroy');

// --- Katalog ---
Route::get('/material', [MaterialController::class, 'index'])->name('material.index');
Route::post('/material', [MaterialController::class, 'store'])->name('material.store');
Route::put('/material/{material}', [MaterialController::class, 'update'])->name('material.update');
Route::delete('/material/{material}', [MaterialController::class, 'destroy'])->name('material.destroy');

Route::get('/analisa', [AnalisaController::class, 'index'])->name('analisa.index');
Route::get('/analisa/baru', [AnalisaController::class, 'create'])->name('analisa.create');
Route::post('/analisa', [AnalisaController::class, 'store'])->name('analisa.store');
Route::get('/analisa/{analisa}/ubah', [AnalisaController::class, 'edit'])->name('analisa.edit');
Route::put('/analisa/{analisa}', [AnalisaController::class, 'update'])->name('analisa.update');
Route::delete('/analisa/{analisa}', [AnalisaController::class, 'destroy'])->name('analisa.destroy');

// --- Anggota ---
Route::get('/anggota', [AnggotaController::class, 'index'])->name('anggota.index');
Route::post('/anggota', [AnggotaController::class, 'store'])->name('anggota.store');
Route::put('/anggota/{user}', [AnggotaController::class, 'update'])->name('anggota.update');
Route::delete('/anggota/{user}', [AnggotaController::class, 'destroy'])->name('anggota.destroy');

// --- Langganan & akun (tetap bisa dipakai saat masa langganan habis) ---
Route::get('/langganan', [LanggananController::class, 'index'])->name('langganan.index');
Route::post('/langganan', [LanggananController::class, 'store'])->name('langganan.store');

Route::get('/akun', [AkunController::class, 'index'])->name('akun.index');
Route::put('/akun/profil', [AkunController::class, 'profil'])->name('akun.profil');
Route::put('/akun/password', [AkunController::class, 'password'])->name('akun.password');
