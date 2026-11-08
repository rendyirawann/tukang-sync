<?php

use App\Http\Controllers\Tim\AuthController;
use App\Http\Controllers\Tim\DashboardController;
use App\Http\Controllers\Tim\StatusController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Panel web tim: domain/{kode}/...
|--------------------------------------------------------------------------
| {tim} adalah kode tim 4 karakter. Polanya dibatasi ke huruf yang memang
| dipakai Team::ALFABET_SLUG (tanpa I, L, O, 0, 1), sehingga path seperti
| /docs atau /admin tidak pernah tertangkap sebagai kode tim.
*/

Route::pattern('tim', '[A-HJKMNP-Za-hjkmnp-z2-9]{4,6}');

Route::prefix('{tim}')->name('tim.')->group(function () {

    // --- Publik (tanpa login) ---
    Route::middleware('tim')->group(function () {
        Route::get('/', [AuthController::class, 'masuk'])->name('home');
        Route::get('/status', [StatusController::class, 'show'])->name('status');
    });

    Route::middleware(['tim', 'guest'])->group(function () {
        Route::get('/login', [AuthController::class, 'create'])->name('login');
        Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:login')->name('login.store');
    });

    // --- Panel anggota tim ---
    Route::middleware(['auth', 'tim:anggota'])->group(function () {
        Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Modul panel tim lainnya (proyek, RAB, katalog, anggota,
        // langganan, akun) didaftarkan di routes/tim_panel.php.
        if (is_file(__DIR__.'/tim_panel.php')) {
            require __DIR__.'/tim_panel.php';
        }
    });
});
