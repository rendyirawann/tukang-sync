<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fondasi SaaS: paket langganan, tim (tenant), pembayaran, serta data yang
 * ditampilkan di formulir pendaftaran (rekening tujuan dan kontak WhatsApp).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table): void {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->string('nama', 80);
            $table->decimal('harga', 14, 0)->default(0);

            // null = lifetime (sekali bayar, tidak pernah berakhir).
            $table->unsignedSmallInteger('durasi_bulan')->nullable();

            // Batas akun per tim, TERMASUK owner.
            $table->unsignedSmallInteger('max_users')->default(4);

            $table->string('deskripsi', 255)->nullable();
            $table->json('fitur')->nullable();
            $table->boolean('aktif')->default(true);
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('teams', function (Blueprint $table): void {
            $table->id();

            // Kode unik 4 karakter yang menjadi path URL: domain/{slug}/login.
            $table->string('slug', 8)->unique();

            $table->string('nama', 120);
            $table->string('kota', 80)->nullable();
            $table->string('alamat', 255)->nullable();
            $table->string('email', 120)->nullable();
            $table->string('no_wa', 20)->nullable();

            // menunggu   = sudah mendaftar, pembayaran belum dikonfirmasi
            // aktif      = boleh dipakai
            // ditangguhkan = dihentikan sementara oleh superadmin
            $table->string('status', 20)->default('menunggu')->index();

            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();
            $table->boolean('lifetime')->default(false);

            // null bersama lifetime=false berarti belum pernah aktif.
            $table->timestamp('aktif_sampai')->nullable()->index();

            $table->unsignedSmallInteger('max_users')->default(4);
            $table->text('catatan_admin')->nullable();
            $table->timestamp('diaktifkan_pada')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table): void {
            // null = akun platform (superadmin), bukan anggota tim mana pun.
            $table->foreignId('team_id')->nullable()->after('id')->constrained('teams')->cascadeOnDelete();

            // owner | kontraktor | mandor | anggota | viewer
            $table->string('peran', 20)->nullable()->after('team_id');

            $table->string('jabatan', 100)->nullable();
            $table->string('alamat', 255)->nullable();

            $table->string('biometric_token_hash', 64)->nullable()->index();
            $table->timestamp('biometric_enrolled_at')->nullable();
            $table->timestamp('biometric_expires_at')->nullable();
            $table->string('biometric_device_name')->nullable();
            $table->timestamp('password_changed_at')->nullable();

            $table->index(['team_id', 'peran']);
        });

        Schema::create('bank_accounts', function (Blueprint $table): void {
            $table->id();
            $table->string('bank', 60);
            $table->string('nomor', 40);
            $table->string('atas_nama', 120);
            $table->string('keterangan', 200)->nullable();
            $table->boolean('aktif')->default(true);
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('admin_contacts', function (Blueprint $table): void {
            $table->id();
            $table->string('nama', 80);
            $table->string('no_wa', 20);
            $table->string('keterangan', 200)->nullable();
            $table->boolean('aktif')->default(true);

            // Kontak utama dipakai tombol "Konfirmasi via WhatsApp".
            $table->boolean('utama')->default(false);
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('team_payments', function (Blueprint $table): void {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->foreignId('team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained('plans')->nullOnDelete();

            // pendaftaran | perpanjangan
            $table->string('jenis', 20)->default('pendaftaran');
            $table->decimal('nominal', 14, 0)->default(0);

            // menunggu | diterima | ditolak
            $table->string('status', 20)->default('menunggu')->index();

            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->string('bukti_path')->nullable();
            $table->string('bukti_mime', 100)->nullable();
            $table->string('catatan', 255)->nullable();
            $table->string('alasan_tolak', 255)->nullable();

            $table->timestamp('periode_mulai')->nullable();
            $table->timestamp('periode_selesai')->nullable();

            $table->foreignUuid('dikonfirmasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dikonfirmasi_pada')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_payments');
        Schema::dropIfExists('admin_contacts');
        Schema::dropIfExists('bank_accounts');

        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('team_id');
            $table->dropColumn([
                'peran', 'jabatan', 'alamat', 'biometric_token_hash', 'biometric_enrolled_at',
                'biometric_expires_at', 'biometric_device_name', 'password_changed_at',
            ]);
        });

        Schema::dropIfExists('teams');
        Schema::dropIfExists('plans');
    }
};
