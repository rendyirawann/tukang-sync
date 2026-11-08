<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RAB (Rencana Anggaran Biaya) yang bisa dipakai untuk proyek apa pun.
 *
 *   Proyek (kegiatan)
 *     └─ Bangunan                 rumah, ruko, gudang, pagar, jalan …
 *          └─ Lantai              Lantai 1, Lantai 2, Atap …
 *               └─ Ruangan/Zona   Kamar Tidur 1, Dapur, Teras …
 *
 *   Pekerjaan (item RAB) menempel di level mana pun di atas -- pondasi di
 *   bangunan, kolom di lantai, keramik di ruangan -- dan punya volume yang
 *   dihitung dari jumlah × dimensi menurut satuannya.
 *
 *   Analisa (AHSP) = resep per 1 satuan pekerjaan: "1 m² dinding bata =
 *   70 bh bata + 0,25 sak semen + …". Memasang analisa ke sebuah pekerjaan
 *   menghasilkan daftar kebutuhan material = koefisien × volume.
 *
 *   Bahan baku (belanja nyata) bisa ditautkan ke material katalog dan ke
 *   pekerjaan, sehingga RAB dan realisasi bisa dibandingkan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('kode', 30)->nullable();
            $table->string('nama', 150);

            // material | upah | alat
            $table->string('kategori', 20)->default('material')->index();
            $table->string('satuan', 30);
            $table->decimal('harga', 18, 2)->default(0);
            $table->string('merek', 80)->nullable();
            $table->string('toko', 120)->nullable();
            $table->string('keterangan', 200)->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['team_id', 'kategori', 'nama']);
        });

        Schema::create('analisa', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('kode', 30)->nullable();
            $table->string('nama', 200);

            // Lihat App\Enums\KategoriPekerjaan.
            $table->string('kategori', 30)->index();
            $table->string('satuan', 20);
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('analisa_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('analisa_id')->constrained('analisa')->cascadeOnDelete();
            $table->foreignId('material_id')->constrained('materials')->cascadeOnDelete();

            // Kebutuhan per 1 satuan pekerjaan.
            $table->decimal('koefisien', 14, 4);
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('bangunan', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->string('nama', 120);
            $table->string('jenis', 30)->default('rumah');
            $table->decimal('luas_tanah', 12, 2)->nullable();
            $table->decimal('luas_bangunan', 12, 2)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('lantai', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('bangunan_id')->constrained('bangunan')->cascadeOnDelete();
            $table->string('nama', 80);
            $table->smallInteger('nomor')->default(1);
            $table->decimal('luas', 12, 2)->nullable();
            $table->decimal('tinggi', 8, 2)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['bangunan_id', 'nomor']);
        });

        Schema::create('ruangan', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('lantai_id')->constrained('lantai')->cascadeOnDelete();
            $table->string('nama', 120);
            $table->decimal('panjang', 10, 2)->nullable();
            $table->decimal('lebar', 10, 2)->nullable();
            $table->decimal('tinggi', 10, 2)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pekerjaan', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->foreignId('bangunan_id')->nullable()->constrained('bangunan')->cascadeOnDelete();
            $table->foreignId('lantai_id')->nullable()->constrained('lantai')->cascadeOnDelete();
            $table->foreignId('ruangan_id')->nullable()->constrained('ruangan')->cascadeOnDelete();
            $table->foreignId('analisa_id')->nullable()->constrained('analisa')->nullOnDelete();

            $table->string('kategori', 30)->index();
            // pintu, jendela, kolom, dinding … (App\Enums\JenisKomponen)
            $table->string('komponen', 30)->default('lainnya');
            $table->string('nama', 200);

            $table->decimal('jumlah', 12, 2)->default(1);
            $table->decimal('panjang', 12, 3)->nullable();
            $table->decimal('lebar', 12, 3)->nullable();
            $table->decimal('tinggi', 12, 3)->nullable();
            $table->string('satuan', 20);

            // Volume final. Dihitung otomatis dari jumlah × dimensi kecuali
            // volume_manual = true (mis. besi dalam kg dari gambar kerja).
            $table->decimal('volume', 16, 4)->default(0);
            $table->boolean('volume_manual')->default(false);

            // Dipakai bila pekerjaan tidak punya rincian material (borongan).
            $table->decimal('harga_satuan', 18, 2)->default(0);
            $table->decimal('total', 18, 0)->default(0);

            $table->unsignedTinyInteger('progres')->default(0);
            $table->date('rencana_mulai')->nullable();
            $table->date('rencana_selesai')->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['kegiatan_id', 'bangunan_id', 'lantai_id']);
        });

        Schema::create('pekerjaan_material', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('pekerjaan_id')->constrained('pekerjaan')->cascadeOnDelete();
            $table->foreignId('material_id')->nullable()->constrained('materials')->nullOnDelete();

            // Disalin dari katalog saat dipasang, supaya RAB lama tidak ikut
            // berubah ketika harga katalog diperbarui.
            $table->string('nama', 150);
            $table->string('kategori', 20)->default('material');
            $table->string('satuan', 30);
            $table->decimal('koefisien', 14, 4)->nullable();
            $table->decimal('qty', 16, 4)->default(0);
            $table->boolean('qty_manual')->default(false);
            $table->decimal('harga_satuan', 18, 2)->default(0);
            $table->decimal('subtotal', 18, 0)->default(0);
            $table->timestamps();
        });

        Schema::create('bahan_baku_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->foreignId('material_id')->nullable()->constrained('materials')->nullOnDelete();
            $table->foreignId('pekerjaan_id')->nullable()->constrained('pekerjaan')->nullOnDelete();
            $table->string('nama', 150);
            $table->string('satuan', 30)->nullable();
            $table->decimal('qty', 14, 3)->default(1);
            $table->decimal('harga_satuan', 18, 0)->default(0);
            $table->decimal('subtotal', 18, 0)->default(0);
            $table->date('tanggal_beli')->nullable();
            $table->string('toko', 120)->nullable();
            $table->text('keterangan')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['kegiatan_id', 'urutan']);
            $table->index(['kegiatan_id', 'tanggal_beli']);
        });

        Schema::create('log_harian', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->date('tanggal')->index();
            $table->string('cuaca', 20)->nullable();
            $table->unsignedSmallInteger('jumlah_pekerja')->nullable();
            $table->text('uraian');
            $table->text('kendala')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        foreach ([
            'log_harian', 'bahan_baku_items', 'pekerjaan_material', 'pekerjaan',
            'ruangan', 'lantai', 'bangunan', 'analisa_items', 'analisa', 'materials',
        ] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
