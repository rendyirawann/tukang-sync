<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Mengganti nilai branding bawaan starter di tabel settings dengan identitas
 * Tukangsync. Hanya nilai yang MASIH bawaan starter yang diganti, sehingga
 * isian yang sudah diubah admin lewat panel tidak tertimpa.
 */
return new class extends Migration
{
    public function up(): void
    {
        $ganti = [
            'site_logo' => [['base-logo.png', 'assets/media/branding/logo-mark.svg'], 'assets/media/branding/logo-icon.svg'],
            'site_keywords' => [['tukangsync, admin panel, dashboard, manajemen pengguna, laravel'], (string) config('seo.keywords')],
            'site_author' => [['Rendy Irawan'], (string) config('seo.author')],
        ];

        foreach ($ganti as $key => [$lama, $baru]) {
            DB::table('settings')->where('key', $key)->whereIn('value', $lama)->update(['value' => $baru, 'updated_at' => now()]);
        }

        Cache::forget('settings.all');
    }

    public function down(): void
    {
        // Data branding; tidak dikembalikan.
    }
};
