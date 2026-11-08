<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Pendaftaran yang tidak dibayar 30 hari melepaskan email & kode timnya.
Schedule::command('saas:bersihkan-pendaftaran')->dailyAt('02:00');
