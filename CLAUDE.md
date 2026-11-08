# Tukangsync — panduan untuk Claude

Aplikasi SaaS catatan proyek konstruksi untuk tim: RAB per bangunan/lantai/ruangan,
kebutuhan material otomatis (AHSP), progres, log harian, belanja, arus kas, bagi hasil.
Web (superadmin + panel tim) dan API untuk aplikasi Android (`../o-saas-mobile`).

## Tech stack

- PHP 8.2+ (server 8.3), **Laravel 12** (bootstrap/app.php, tanpa Kernel), PostgreSQL, Redis
- Blade + tema Metronic di `public/assets` (jQuery, DataTables) — tidak perlu `npm run build`
- Sanctum (API), spatie/laravel-permission (superadmin), spatie/activitylog, laravel-ban,
  l5-swagger, dompdf, Octane (FrankenPHP) di produksi

## Perintah

```bash
composer install && php artisan migrate --seed       # lokal, termasuk tim DEMO
php artisan serve --port=8010                        # atau ../start-dev-saas.bat
php artisan test                                     # DB o_saas_test (phpunit.xml)
DB_DATABASE=o_saas_test_x php artisan test           # DB tes terpisah
./vendor/bin/pint                                    # format
./vendor/bin/phpstan analyse                         # bila phpstan.neon ada
php artisan l5-swagger:generate                      # Swagger /api/documentation
php artisan boost:update                             # bila Laravel Boost terpasang
```

Akun lokal: `/admin/login` superadmin (lihat `database/seeders/SuperAdminSeeder.php`);
`/DEMO/login` `owner@demo.test` / `password123`, `mandor@demo.test` / `password123`.

## Aturan multi-tenant (WAJIB)

1. Setiap model data milik tim memakai trait `App\Models\Concerns\MilikTim`
   (`app/Models/Concerns/MilikTim.php`) dan tabelnya punya `team_id` FK `teams` cascade.
2. Tim aktif ada di `App\Support\Tenancy\TimAktif` (`app/Support/Tenancy/TimAktif.php`),
   diisi middleware `TimWeb` (`/{tim}`) dan `TimApi` (API). Untuk bekerja atas nama tim
   di luar request (seeder, superadmin): `TimAktif::dengan($tim, fn () => ...)`.
3. `User` TIDAK memakai global scope — selalu `User::anggotaTim($teamId)`.
4. Saat menerima ID dari input (FK di body, child di URL), pastikan milik tim/induk yang sama
   (mis. lantai → bangunan → kegiatan). Akses lintas tim harus 404.
5. `Rule::unique`/`exists` pada tabel tim wajib `->where('team_id', TimAktif::id())`.
6. **Jangan** memberi model ber-ID integer (Team, Kegiatan, Pekerjaan, …) sebagai subject ke
   `App\Support\ActivityRecorder::log()` — kolom `activity_log.subject_id` UUID, transaksi
   Postgres batal diam-diam. Kirim identitasnya lewat properties: `['tim' => $tim->slug]`.

## Otorisasi

- `app/Support/Izin.php` = satu sumber aturan peran (dipakai controller dan dikirim ke
  aplikasi sebagai `izin`). Tambah izin baru di sini, bukan if-else di Blade.
- Peran (`app/Enums/Peran.php`): owner/kontraktor = pengelola; mandor/anggota = pelaksana;
  viewer = hanya baca. Superadmin = `team_id` null + role spatie `Superadmin` (Gate::before).
- Langganan habis (`Team::hanyaBaca()`) dan viewer ditolak menulis di middleware
  (`TimWeb`, `TimApi`); rute `tim.akun.*`, `tim.langganan.*`, `tim.logout` dikecualikan.

## Struktur penting

| Lokasi | Isi |
|---|---|
| `routes/web.php` | panel `/admin` (+ `routes/admin_saas.php`); `publik.php` & `tim.php` di-require PALING AKHIR |
| `routes/tim.php`, `routes/tim_panel.php` | panel tim `/{tim}/...` (nama `tim.*`) |
| `routes/api.php` | API (`auth:sanctum` + `tim.api`) |
| `app/Http/Controllers/{Backend,Backend/Saas,Tim,Publik,Api,Api/Rab}` | controller per pintu |
| `app/Services/Saas/` | `PenyediaTim` (daftar/konfirmasi/bukti), `KelolaAnggota`, `KatalogAwal` |
| `app/Services/Rab/RabCalculator.php` | volume, kebutuhan, ringkasan, rekap material |
| `app/Enums/` | Peran, StatusTim, StatusPembayaran, KategoriPekerjaan, JenisKomponen, SatuanVolume, … |
| `app/Http/Middleware/` | TimWeb, TimApi, WajibPlatformAdmin, ResetTimAktif, SecurityHeaders, BlockSuspiciousRequests, SanitizeInput, CatatAktivitas |
| `resources/views/backend/` | panel superadmin (layout `backend/layout/app.blade.php`) |
| `resources/views/tim/`, `resources/views/publik/` | panel tim (layout `tim/layout/app.blade.php`) & publik (`tim/layout/polos.blade.php`) |
| `resources/views/errors/`, `resources/views/partials/` | halaman error, meta/SEO |
| `app/Support/Brand.php`, `public/assets/media/branding/` | identitas & logo |
| `config/security.php` | CSP, HSTS, rate limit (dari `.env` SECURITY_*, RATE_LIMIT_*) |
| `deploy/`, `docs/` | skrip server, dokumentasi |

## Standar kode (ringkas)

- Controller tipis → FormRequest/validate → `Izin` → Service → Model. Multi-tabel dalam `DB::transaction()`.
- Status/tipe pakai Enum; respon API lewat `App\Support\ApiResponse` + JsonResource.
- `declare(strict_types=1)` di file baru, type hint & return type, constructor promotion, `match`.
- Penamaan domain memakai Bahasa Indonesia yang sudah ada (Kegiatan, Pekerjaan, Lantai, `kelolaRab`); jangan campur sinonim baru.
- PHPDoc Bahasa Indonesia: jelaskan MENGAPA. Pesan UI & validasi Bahasa Indonesia.
- Uang = integer rupiah. ID akun = UUID string.
- Edit berkas dengan tool Edit/Write; jangan `php -r`/`sed` regex dengan backslash (pernah mengosongkan berkas).
- Commit: Conventional Commits (`feat:`, `fix:`, `docs:`, `test:`, `chore:`).

## Keamanan (ringkas)

- Jangan tampilkan isi `.env`, APP_KEY, sandi, token di output/log.
- Header keamanan HANYA dari `App\Http\Middleware\SecurityHeaders` (bukan nginx). Ubah CSP lewat
  `config/security.php` / `SECURITY_CSP_EXTRA_HOSTS`, lalu cek console browser.
- Unggahan: validasi mimes + ukuran, simpan di disk privat (`bukti-bayar/`, lampiran), nama acak.
- Query mentah wajib binding; kolom sort dari input wajib whitelist.
- Teks UI memakai `config('app.name')`, bukan nama hardcode.

## Testing

- Feature test per pintu: `tests/Feature/Admin`, `tests/Feature/Tim`, `tests/Feature/Publik`, `tests/Feature/Api`.
- Factory tim: `Team::factory()`, `User::factory()->forTeam($tim, 'owner')`; reset `TimAktif` sebelum
  membuat superadmin lewat factory.
- Setiap fitur baru: uji izin per peran, viewer, langganan habis, dan isolasi lintas tim.

## Alur kerja

Untuk pekerjaan besar: rencanakan dulu, kerjakan bertahap, verifikasi (`php artisan test`, render halaman)
sebelum menyatakan selesai. Pakai skill superpowers (brainstorming, writing-plans,
systematic-debugging, verification-before-completion) bila terpasang.

## Skill project (`.ai/skills/`, disalin ke `.claude/skills/`)

| Skill | Kapan |
|---|---|
| `laravel-feature` | membuat modul/CRUD baru end-to-end |
| `code-standards` | menulis/merapikan kode |
| `security-review` | sebelum commit/merge fitur |
| `validation-and-errors` | form, FormRequest, 422, halaman error |
| `ui-and-branding` | layout, logo, warna, menu, CSP aset |
| `seo-page` | halaman publik baru |
| `testing` | menulis/menjalankan test |
| `deployment` | rilis ke server |
| `project-setup` | setup ulang identitas/keamanan/dokumentasi (idempoten) |

## Dokumen

[README.md](README.md) · [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) · [docs/DATABASE.md](docs/DATABASE.md) ·
[docs/API.md](docs/API.md) · [docs/API-RAB.md](docs/API-RAB.md) · [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) ·
[docs/CHANGELOG.md](docs/CHANGELOG.md) · docs/SECURITY.md
