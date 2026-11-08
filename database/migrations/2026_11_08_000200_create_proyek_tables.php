<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel domain yang dibawa dari o-api, kini milik satu tim.
 *
 * Setiap baris membawa team_id; pemisahan antartim dijaga global scope
 * App\Models\Concerns\MilikTim, dan kolom ini menjadi jaring pengaman
 * terakhirnya. Perubahan skema o-api setelah rilis awal (kolom `dibayar`,
 * `no_struk` dihapus, satuan boleh kosong) sudah dilebur ke sini.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatan', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('kode', 40)->nullable();
            $table->string('nama', 150);
            $table->text('keterangan')->nullable();
            $table->string('lokasi', 150)->nullable();
            $table->string('sumber_dana', 100)->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('status', 20)->default('draft')->index();

            $table->decimal('pagu', 18, 0)->default(0);
            $table->decimal('pelaksanaan_real', 18, 0)->nullable();

            $table->decimal('rate_ppn', 6, 3)->default(11);
            $table->decimal('rate_pph', 6, 3)->default(1.75);
            $table->decimal('rate_rencana', 6, 3)->default(60);
            $table->decimal('rate_kewajiban', 6, 3)->default(12);
            $table->decimal('rate_administrasi', 6, 3)->default(1);
            $table->decimal('rate_perusahaan', 6, 3)->default(1.5);
            $table->decimal('rate_investor', 6, 3)->default(50);
            $table->unsignedTinyInteger('jml_owner')->default(3);

            foreach ([
                'ppn', 'pph', 'netto', 'rencana_pelaksanaan', 'biaya_kewajiban',
                'biaya_administrasi', 'biaya_perusahaan', 'profit_kotor',
                'bagi_hasil_investor', 'profit_bersih', 'hasil_bersih_per_owner', 'sisa_pembulatan',
            ] as $kolom) {
                $table->decimal($kolom, 18, 0)->default(0);
            }

            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['team_id', 'kode']);
            $table->index(['team_id', 'status', 'tanggal_mulai']);
        });

        Schema::create('cash_flows', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->date('tanggal')->index();
            $table->string('jenis', 10);
            $table->string('kategori', 30);
            $table->decimal('nominal', 18, 0);
            $table->decimal('dibayar', 18, 0)->default(0);
            $table->string('uraian', 200);
            $table->text('keterangan')->nullable();
            $table->string('metode', 20)->default('kas');
            $table->string('no_bukti', 60)->nullable();
            $table->string('lampiran_path')->nullable();
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['kegiatan_id', 'tanggal']);
            $table->index(['kegiatan_id', 'jenis', 'kategori']);
        });

        Schema::create('master_data', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->string('jenis', 30);
            $table->string('nama', 100);
            $table->string('keterangan', 200)->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['team_id', 'jenis', 'aktif', 'urutan']);
            $table->unique(['team_id', 'jenis', 'nama', 'deleted_at']);
        });

        Schema::create('lampiran', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->string('konteks', 30)->default('biaya_pelaksanaan');
            $table->string('path');
            $table->string('nama_asli', 200);
            $table->string('mime', 100);
            $table->unsignedBigInteger('ukuran')->default(0);
            $table->string('hash', 64)->nullable()->index();
            $table->string('keterangan', 200)->nullable();
            $table->foreignUuid('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['kegiatan_id', 'konteks']);
        });

        Schema::create('activity_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('team_id')->nullable()->constrained('teams')->cascadeOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_nama', 255)->nullable();
            $table->string('user_role', 20)->nullable();
            $table->string('aksi', 120);
            $table->string('modul', 40)->index();
            $table->string('subjek_tipe', 60)->nullable();
            $table->string('subjek_id', 64)->nullable();
            $table->string('subjek_label', 200)->nullable();
            $table->string('metode', 10);
            $table->string('path', 300);
            $table->string('route_name', 120)->nullable();
            $table->unsignedSmallInteger('status');
            $table->boolean('berhasil')->default(true);
            $table->json('payload')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 300)->nullable();
            $table->unsignedInteger('durasi_ms')->nullable();
            $table->timestamps();

            $table->index(['team_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['subjek_tipe', 'subjek_id']);
        });

        Schema::create('personal_access_tokens', function (Blueprint $table): void {
            $table->id();
            $table->uuidMorphs('tokenable');
            $table->text('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['personal_access_tokens', 'activity_logs', 'lampiran', 'master_data', 'cash_flows', 'kegiatan'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
