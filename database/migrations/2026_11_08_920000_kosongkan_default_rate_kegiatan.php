<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Membawa perubahan o-api "reset_default_rate_kegiatan": kegiatan baru tidak
 * lagi diisi persentase bawaan diam-diam. Persentase 0 berarti "belum
 * ditentukan" dan aplikasi menyembunyikan bagian taksasi sampai pengguna
 * mengisinya sendiri (Kegiatan::rateTerisi()).
 */
return new class extends Migration
{
    private const KOLOM = [
        'rate_ppn' => 11, 'rate_pph' => 1.75, 'rate_rencana' => 60, 'rate_kewajiban' => 12,
        'rate_administrasi' => 1, 'rate_perusahaan' => 1.5, 'rate_investor' => 50,
    ];

    public function up(): void
    {
        Schema::table('kegiatan', function (Blueprint $table): void {
            foreach (array_keys(self::KOLOM) as $kolom) {
                $table->decimal($kolom, 6, 3)->default(0)->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('kegiatan', function (Blueprint $table): void {
            foreach (self::KOLOM as $kolom => $nilai) {
                $table->decimal($kolom, 6, 3)->default($nilai)->change();
            }
        });
    }
};
