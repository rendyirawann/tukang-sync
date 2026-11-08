<?php

use App\Http\Controllers\Publik\PendaftaranController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman publik SaaS
|--------------------------------------------------------------------------
|   /         beranda: paket & tombol daftar
|   /daftar   formulir pendaftaran tim -> dialihkan ke /{kode}/status
*/

Route::get('/', [PendaftaranController::class, 'beranda'])->name('beranda');
Route::get('/daftar', [PendaftaranController::class, 'create'])->name('daftar');
Route::post('/daftar', [PendaftaranController::class, 'store'])
    ->middleware('throttle:daftar')
    ->name('daftar.store');
