# Tukangsync

Aplikasi SaaS catatan proyek konstruksi untuk tim: RAB per bangunan, lantai
dan ruangan, kebutuhan material otomatis, progres, log harian, belanja, arus
kas, dan bagi hasil. Web dan Android tersinkron dalam satu tim.

## Fitur utama

- **Langganan per tim**: paket Bulanan (Rp49.000) dan Lifetime (Rp99.000),
  maksimal 4 akun per tim (owner, kontraktor, mandor, anggota, viewer).
- **Pendaftaran publik** di `/daftar` → kode tim unik 4 karakter →
  konfirmasi transfer via WhatsApp → superadmin ACC dengan bukti tempel (Ctrl+V)
  atau unggah PNG/JPG/PDF.
- **Panel superadmin** `/admin`: tim, pembayaran, paket, rekening tujuan,
  kontak WhatsApp, akun anggota tiap tim, pengguna & peran platform, log aktivitas.
- **Panel tim** `/{KODE}`: proyek, struktur bangunan › lantai › ruangan, item
  pekerjaan dengan volume otomatis (unit/m'/m²/m³), analisa harga satuan (AHSP),
  rekap kebutuhan material vs realisasi, log harian, belanja, katalog harga,
  anggota, langganan.
- **API** untuk aplikasi Android (Sanctum) dengan Swagger di `/api/documentation`.
- Masa langganan habis → data tetap bisa dibaca, tidak bisa diubah.

## Tech stack

| Bagian | Teknologi |
|---|---|
| Backend | PHP 8.2+ (server: 8.3), Laravel 12, PostgreSQL 15+, Redis |
| Auth | Session (web), Laravel Sanctum (API), spatie/laravel-permission (superadmin) |
| UI web | Blade + tema Metronic (`public/assets`), jQuery, DataTables |
| Dokumen | barryvdh/laravel-dompdf (PDF), darkaonline/l5-swagger (OpenAPI) |
| Server | Laravel Octane (FrankenPHP) di belakang nginx |
| Mobile | Flutter (folder terpisah `../o-saas-mobile`) |

## Kebutuhan sistem (lokal)

- PHP 8.2+ dengan ekstensi pgsql, mbstring, intl, gd, zip, fileinfo
- Composer 2
- PostgreSQL (lokal memakai port 5433)
- Node.js hanya bila mengubah `resources/js` (tampilan memakai aset jadi di `public/assets`)

## Instalasi

```bash
composer install
cp .env.example .env            # lalu isi DB_* sesuai PostgreSQL lokal
php artisan key:generate
php artisan migrate --seed      # termasuk tim contoh DEMO di lingkungan non-produksi
php artisan storage:link
```

## Menjalankan

```bash
php artisan serve --port=8010   # atau klik dua kali ../start-dev-saas.bat
php artisan queue:work          # bila QUEUE_CONNECTION bukan sync
php artisan schedule:work       # penjadwal (pembersihan pendaftaran tak dibayar)
```

| Alamat | Akun contoh |
|---|---|
| http://127.0.0.1:8010/admin/login | superadmin dari `database/seeders/SuperAdminSeeder.php` |
| http://127.0.0.1:8010/DEMO/login | `owner@demo.test` / `password123`, `mandor@demo.test` / `password123` |
| http://127.0.0.1:8010/api/documentation | Swagger |

## Konfigurasi `.env` penting

| Kunci | Keterangan |
|---|---|
| `APP_NAME`, `APP_DESCRIPTION` | identitas & SEO |
| `DB_*` | PostgreSQL (`tukangsync`) |
| `SANCTUM_TOKEN_TTL` | umur token API (menit) |
| `L5_SWAGGER_CONST_HOST` | URL dasar di Swagger |
| `SECURITY_*`, `RATE_LIMIT_*`, `TRUSTED_PROXIES` | header keamanan & pembatas laju |

## Testing

```bash
php artisan test                      # PostgreSQL, database o_saas_test (phpunit.xml)
DB_DATABASE=nama_lain php artisan test # pakai database tes lain
```

## Struktur folder

```
app/
  Enums/                    Peran, StatusTim, KategoriPekerjaan, SatuanVolume, …
  Http/Controllers/Api/     API mobile (+ Rab/)
  Http/Controllers/Backend/ panel superadmin (Saas/ untuk langganan)
  Http/Controllers/Tim/     panel web tim
  Http/Controllers/Publik/  beranda & pendaftaran
  Http/Middleware/          TimWeb, TimApi, WajibPlatformAdmin, SecurityHeaders, …
  Models/Concerns/MilikTim.php   global scope per tim
  Services/Rab/RabCalculator.php hitung volume, kebutuhan, ringkasan
  Services/Saas/            PenyediaTim, KelolaAnggota, KatalogAwal
  Support/Izin.php          aturan hak akses per peran
  Support/Tenancy/          TimAktif, PenjagaTim
routes/  web.php, admin_saas.php, publik.php, tim.php, tim_panel.php, api.php
deploy/  skrip server
docs/    dokumentasi
```

## Dokumentasi

- [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md): alur request & modul
- [docs/DATABASE.md](docs/DATABASE.md): tabel & ERD
- [docs/API.md](docs/API.md) dan [docs/API-RAB.md](docs/API-RAB.md): API
- [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md): deploy ke server
- [docs/CHANGELOG.md](docs/CHANGELOG.md)
- `docs/SECURITY.md`: kontrol keamanan

## Claude Code

Konvensi project untuk Claude ada di [CLAUDE.md](CLAUDE.md), guideline di
`.ai/guidelines/`, dan skill project di `.ai/skills/` (disalin ke
`.claude/skills/`): laravel-feature, code-standards, security-review,
validation-and-errors, ui-and-branding, seo-page, testing, deployment,
project-setup.
