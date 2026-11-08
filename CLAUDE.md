# Tukangsync — panduan untuk Claude

Aplikasi SaaS catatan proyek konstruksi untuk tim: RAB per bangunan/lantai/ruangan,
kebutuhan material otomatis (AHSP), progres, log harian, belanja, arus kas, bagi hasil.
Web (superadmin + panel tim) dan API untuk aplikasi Android (`../o-mobile`).

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

===

<laravel-boost-guidelines>
=== .ai/tukangsync rules ===

# Tukangsync — guideline project

## Multi-tenant

- Model data tim memakai `App\Models\Concerns\MilikTim`; tabel punya `team_id` (FK `teams`, cascade).
- Tim aktif: `App\Support\Tenancy\TimAktif` (diisi `TimWeb`/`TimApi`; `TimAktif::dengan()` di luar request).
- `User` tanpa global scope: pakai `User::anggotaTim($teamId)`.
- Validasi unik/exists pada tabel tim di-scope `team_id`. Akses lintas tim = 404.
- Jangan kirim model ber-ID integer sebagai subject ke `ActivityRecorder::log()`.

## Arsitektur

- Controller tipis → FormRequest/validate → `App\Support\Izin` → Service (`app/Services/*`) → Model.
- Operasi multi-tabel dalam `DB::transaction()`. Status/tipe = Enum (`app/Enums`).
- API: `App\Support\ApiResponse` + JsonResource; error envelope `{success, message, code, errors?}`.
- Hindari N+1 (eager loading); konfigurasi di `config/`, `env()` hanya di `config/`.

## Kode

- `declare(strict_types=1)`, type hint & return type, constructor property promotion, `match`, early return.
- Nama domain Bahasa Indonesia mengikuti yang ada; nama tabel plural snake_case kecuali tabel domain lama (`kegiatan`, `lantai`, …).
- PHPDoc Bahasa Indonesia menjelaskan alasan. Pesan UI/validasi Bahasa Indonesia.
- Uang = integer rupiah; ID akun = UUID.
- Format dengan Pint (preset laravel); Conventional Commits.

## Keamanan (OWASP ringkas)

- Semua rute ubah data: auth + otorisasi (`Izin`, middleware `platform`/`tim:anggota`/`tim.api`).
- Query mentah dengan binding; whitelist kolom sort.
- Unggahan: mimes + max, disk privat, nama acak; tolak php/html/svg.
- Jangan log password/token/isi .env. Header keamanan hanya dari `SecurityHeaders`.

=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.4. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.
- Activate the `deploying-to-cloud` skill whenever deploying to Laravel Cloud, configuring Cloud environments or resources, using the Cloud CLI, or troubleshooting Cloud deployments.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== laravel/v12 rules ===

# Laravel 12

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- This project uses the streamlined Laravel 11+ structure: register middleware, exceptions, and routing in `bootstrap/app.php` and service providers in `bootstrap/providers.php`. There is no `app/Http/Kernel.php` or `app/Console/Kernel.php`, and commands in `app/Console/Commands/` auto-register.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.

- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== laravel-octane/core rules ===

# Laravel Octane

This application uses Laravel Octane, a long-running PHP server. The application bootstraps once and handles many requests within the same process.

- Never store request-specific state in singletons or static properties, because it can leak across requests.
- Use `config('octane.server')` to detect the active driver (`swoole`, `roadrunner`, or `frankenphp`).
- Prefer scoped bindings (`$this->app->scoped()`) over singletons for per-request services.

When working on Octane-specific features (concurrency, shared tables, memory, driver configuration, testing), invoke `octane-development` for detailed rules.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== phpunit/core rules ===

# PHPUnit

- This project uses PHPUnit. Create tests with `php artisan make:test --phpunit {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/phpunit` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.

</laravel-boost-guidelines>
