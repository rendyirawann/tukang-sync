<?php

use App\Http\Controllers\Backend\Saas\AdminContactController;
use App\Http\Controllers\Backend\Saas\BankAccountController;
use App\Http\Controllers\Backend\Saas\PaymentController;
use App\Http\Controllers\Backend\Saas\PlanController;
use App\Http\Controllers\Backend\Saas\TeamController;
use App\Http\Controllers\Backend\Saas\TeamUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Panel superadmin: pengelolaan SaaS
|--------------------------------------------------------------------------
| Di-include dari web.php di dalam grup auth + platform. Seluruhnya
| khusus Superadmin (can:view_resources, yang dilewati Gate::before).
*/

Route::prefix('admin/saas')->name('saas.')->middleware('can:view_resources')->group(function () {

    // --- Tim ---
    Route::get('/tim', [TeamController::class, 'index'])->name('tim.index');
    Route::get('/tim/baru', [TeamController::class, 'create'])->name('tim.create');
    Route::get('/tim/{team}', [TeamController::class, 'show'])->name('tim.show');
    Route::get('/tim/{team}/ubah', [TeamController::class, 'edit'])->name('tim.edit');

    // --- Pembayaran ---
    Route::get('/pembayaran', [PaymentController::class, 'index'])->name('bayar.index');
    Route::get('/pembayaran/{payment}', [PaymentController::class, 'show'])->name('bayar.show');
    Route::get('/pembayaran/{payment}/bukti', [PaymentController::class, 'bukti'])->name('bayar.bukti');

    // --- Paket, rekening, kontak ---
    Route::get('/paket', [PlanController::class, 'index'])->name('paket.index');
    Route::get('/rekening', [BankAccountController::class, 'index'])->name('rekening.index');
    Route::get('/kontak', [AdminContactController::class, 'index'])->name('kontak.index');

    Route::middleware('throttle:write')->group(function () {
        Route::post('/tim', [TeamController::class, 'store'])->name('tim.store');
        Route::put('/tim/{team}', [TeamController::class, 'update'])->name('tim.update');
        Route::delete('/tim/{team}', [TeamController::class, 'destroy'])->name('tim.destroy');
        Route::post('/tim/{team}/status', [TeamController::class, 'status'])->name('tim.status');
        Route::post('/tim/{team}/pembayaran', [TeamController::class, 'tambahPembayaran'])->name('tim.bayar');

        Route::post('/tim/{team}/anggota', [TeamUserController::class, 'store'])->name('tim.anggota.store');
        Route::put('/tim/{team}/anggota/{user}', [TeamUserController::class, 'update'])->name('tim.anggota.update');
        Route::delete('/tim/{team}/anggota/{user}', [TeamUserController::class, 'destroy'])->name('tim.anggota.destroy');

        Route::post('/pembayaran/{payment}/bukti', [PaymentController::class, 'unggahBukti'])->name('bayar.unggah');
        Route::post('/pembayaran/{payment}/terima', [PaymentController::class, 'terima'])->name('bayar.terima');
        Route::post('/pembayaran/{payment}/tolak', [PaymentController::class, 'tolak'])->name('bayar.tolak');

        Route::post('/paket', [PlanController::class, 'store'])->name('paket.store');
        Route::put('/paket/{plan}', [PlanController::class, 'update'])->name('paket.update');
        Route::delete('/paket/{plan}', [PlanController::class, 'destroy'])->name('paket.destroy');

        Route::post('/rekening', [BankAccountController::class, 'store'])->name('rekening.store');
        Route::put('/rekening/{bankAccount}', [BankAccountController::class, 'update'])->name('rekening.update');
        Route::delete('/rekening/{bankAccount}', [BankAccountController::class, 'destroy'])->name('rekening.destroy');

        Route::post('/kontak', [AdminContactController::class, 'store'])->name('kontak.store');
        Route::put('/kontak/{adminContact}', [AdminContactController::class, 'update'])->name('kontak.update');
        Route::delete('/kontak/{adminContact}', [AdminContactController::class, 'destroy'])->name('kontak.destroy');
    });
});
