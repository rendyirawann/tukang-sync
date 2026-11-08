<?php

use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| robots.txt & sitemap.xml
|--------------------------------------------------------------------------
| Didaftarkan sebelum rute /{tim} supaya tidak tertangkap sebagai kode tim,
| dan dibebaskan dari pembatas laju web: crawler membacanya berulang kali.
*/

Route::withoutMiddleware(['throttle:web'])->group(function () {
    Route::get('/robots.txt', [SeoController::class, 'robots'])->name('seo.robots');
    Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('seo.sitemap');
});
