<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '2.0.0',
    description: <<<'DESC'
    API untuk aplikasi **Tukangsync** (SaaS) — catatan proyek konstruksi:
    RAB per bangunan/lantai/ruangan, kebutuhan material, progres, arus kas,
    dan perhitungan bagi hasil.

    ### Tim (multi-tenant)
    Setiap akun adalah anggota satu **tim**, dikenali lewat **kode tim** 4
    karakter (bagian URL `domain/KODE/login`). Semua data yang dibaca dan
    ditulis lewat API otomatis milik tim akun yang login; data tim lain tidak
    pernah terlihat.

    ### Alur autentikasi
    1. `POST /api/auth/login` dengan kode tim + email + password → `access_token`.
    2. Kirim `Authorization: Bearer <access_token>` untuk semua endpoint lain.
    3. Opsional: `POST /api/auth/biometric/enable` → `biometric_token` untuk
       login sidik jari lewat `POST /api/auth/biometric/login`.

    ### Status langganan
    - Tim yang pembayarannya belum dikonfirmasi: `403 TEAM_PENDING`.
    - Tim yang ditangguhkan: `403 TEAM_SUSPENDED`.
    - Masa langganan habis: data tetap bisa DIBACA, tetapi permintaan yang
      mengubah data dijawab `402 SUBSCRIPTION_EXPIRED`.
    - Akun peran *viewer* hanya bisa membaca (`403 FORBIDDEN` untuk menulis).

    ### Aturan angka
    Nilai uang adalah **rupiah bulat** (integer). Persentase dikirim sebagai
    angka desimal biasa, mis. `1.75` untuk 1,75%. ID akun berupa UUID (string).
    DESC,
    title: 'Tukangsync API',
)]
#[OA\Server(url: L5_SWAGGER_CONST_HOST, description: 'Server ini')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    description: 'Masukkan access_token dari endpoint login (tanpa kata "Bearer").',
    name: 'Authorization',
    in: 'header',
    bearerFormat: 'JWT',
    scheme: 'bearer',
)]
#[OA\Tag(name: 'Auth', description: 'Login anggota tim, logout, ganti password, sidik jari')]
#[OA\Tag(name: 'Kegiatan', description: 'Proyek & perhitungan transaksi')]
#[OA\Tag(name: 'Cash Flow', description: 'Pencatatan kas masuk / keluar per proyek')]
#[OA\Tag(name: 'Laporan', description: 'Ringkasan, rekap, dan cetak PDF')]
#[OA\Tag(name: 'Referensi', description: 'Daftar enum & nilai default untuk form')]
#[OA\Tag(name: 'RAB', description: 'Rencana Anggaran Biaya: bangunan › lantai › ruangan, item pekerjaan, kebutuhan material, progres')]
#[OA\Tag(name: 'Katalog', description: 'Katalog harga material/upah/alat dan template analisa (AHSP) milik tim')]
#[OA\Tag(name: 'Log Harian', description: 'Catatan harian lapangan dari mandor/pengawas')]
#[OA\Tag(name: 'Pengguna', description: 'Anggota tim (khusus owner/kontraktor)')]
final class Info {}
