---
name: project-setup
description: Setup menyeluruh project Laravel turunan starter (identitas, logo/favicon, header keamanan, rate limit, anti injection/CSRF/spam/defacement, SEO, halaman error, standar kode, dokumentasi, Laravel Boost, CLAUDE.md, skill project, verifikasi). Pakai saat diminta "jalankan skill project-setup" atau saat folder ini disalin menjadi project baru. Idempoten - aman dijalankan ulang.
---

# Project setup (idempoten)

Jalankan bagian berurutan. Di setiap bagian: **periksa dulu apakah sudah terpenuhi**; bila ya, lewati
dan catat "sudah" di laporan. Jangan menduplikasi middleware/header/berkas yang sudah ada.

## 0. Deteksi identitas (tanpa bertanya kecuali mustahil)

- Nama folder root → PROJECT_NAME (Title Case), PROJECT_SLUG (kebab), PROJECT_SHORT (1–3 huruf),
  DB_DATABASE (snake). APP_URL tetap. **Bila pengguna sudah memilih nama merek** (contoh project ini:
  folder `o-saas`, merek "Tukangsync"), merek itu menang atas nama folder.
- PROJECT_DESCRIPTION (≤155 karakter, Bahasa Indonesia) dan 5–10 keyword dari README, routes, Models,
  Controllers, migration, menu — fungsi nyata aplikasi.
- WARNA_UTAMA & WARNA_SEKUNDER (teks putih di atas warna utama lolos WCAG AA; selaraskan tema).
- Tampilkan hasil deteksi lalu lanjut.

## Aturan kerja

1. Branch `setup/<slug>`, commit kondisi awal. 2. Audit (bootstrap/app.php, config/, routes/,
middleware, providers, views layout/partials/auth/errors, js, vite, public/) sebelum mengubah.
3. Jangan hapus fitur/rute/logika bisnis. 4. Jangan tampilkan rahasia. 5. Tidak boleh ada 500,
white screen, error console, CSP violation, aset 404. 6. Commit per bagian (Conventional Commits).
7. Pakai skill/plugin yang terpasang (superpowers, frontend-design, webapp-testing, semgrep,
supply-chain-risk-auditor, skill-creator, Laravel Boost); bila tidak ada, lanjut tanpa.

## 1. Rename & identitas

- Cari sisa nama lama (case-insensitive: "StarterTemp", nama folder base, "Laravel" di title/meta/footer).
- `.env` & `.env.example`: APP_NAME, APP_DESCRIPTION, DB_DATABASE, MAIL_FROM_NAME="${APP_NAME}",
  SESSION_COOKIE=<slug>_session, CACHE_PREFIX=<slug>_cache. `.env.example` tanpa secret asli.
- `php artisan key:generate`, REVERB_APP_ID/KEY/SECRET acak baru.
- composer.json name/description, package.json name, README, fallback `config/app.php`.
- Navbar/sidebar/auth/email/footer pakai `config('app.name')`;
  footer `&copy; {{ now()->year }} {{ config('app.name') }}. All rights reserved.`

## 2. Logo & favicon

- logo.svg (ikon+teks), logo-icon.svg, logo-light.svg; favicon.ico (16/32/48), favicon.svg,
  favicon-32x32.png, apple-touch-icon.png (180), android-chrome-192/512, og-image.png (1200×630),
  site.webmanifest. Ganti semua referensi lama; cache busting `?v=filemtime`.
  (Project ini: `public/assets/media/branding/`, dibaca `app/Support/Brand.php`.)

## 3. Header keamanan (grade A, tidak terlalu ketat)

Satu middleware global yang sudah ada (`app/Http/Middleware/SecurityHeaders.php`, `config/security.php`):
CSP longgar (default-src 'self'; script-src 'self' 'unsafe-inline' + CDN terdeteksi (+'unsafe-eval' bila
perlu); style/img/font/media https: data:; connect-src + Reverb ws/wss; frame-ancestors 'self'; base-uri
'self'; form-action 'self'; object-src 'none'; Vite dev di local; SECURITY_CSP_EXTRA_HOSTS;
SECURITY_CSP_REPORT_ONLY), nosniff, X-Frame-Options SAMEORIGIN, Referrer-Policy
strict-origin-when-cross-origin, Permissions-Policy minimal, HSTS hanya bila SECURITY_HSTS=true & HTTPS,
hapus X-Powered-By. Tanpa COEP/COOP/CORP ketat. Berlaku juga di halaman error & JSON; aset statis tidak diblokir.

## 4. Rate limit wajar

Limiter bernama dari RATE_LIMIT_* (web, login, password, write, api); per user id / IP; login per email+IP;
aset, sitemap, robots, health tidak kena limiter ketat; TrustProxies benar; 429 dengan Retry-After.

## 5. Anti injection

Binding di raw query; whitelist sort/filter; `{!! !!}` diaudit; validasi semua input; `$fillable`;
upload mimes+max, nama acak, storage privat, tolak php/phtml/svg/html; SECURITY_BLOCK_SUSPICIOUS hanya URL/query.

## 6. Anti 419

`@csrf`/`@method` di semua form; meta csrf-token; AJAX kirim X-CSRF-TOKEN; APP_URL/SESSION_* konsisten;
tabel sessions; tangani 419 dengan pesan + tombol muat ulang; pengecualian CSRF hanya webhook.

## 7. Anti spam

Honeypot + time-trap (<3 detik) + rate limit di form publik; Turnstile opsional (TURNSTILE_ENABLED=false);
throttle email.

## 8. Anti defacement

Docroot public/; .env/.git/composer/storage/logs tak terjangkau; tanpa directory listing; semua rute ubah
data auth+otorisasi; APP_DEBUG=false & LOG_LEVEL=error di catatan produksi; SRI untuk CDN bila aman.

## 9. SEO

`config/seo.php` + `partials/seo.blade.php` (title, description, keywords, canonical, OG, Twitter, JSON-LD
Organization/WebSite/WebApplication), override via @section; robots.txt (disallow privat, sitemap);
sitemap.xml dinamis tercache; noindex untuk auth/admin/error dan seluruh situs non-production; satu h1,
alt gambar, lazy-load, width/height; beranda publik bila belum ada.

## 10. Halaman error

`errors/layout.blade.php` mandiri (CSS inline, tanpa DB/auth/vite); 400–504 Bahasa Indonesia; 4xx boleh
pesan abort, 5xx tidak; 422 web (error per field + ringkasan) & JSON; `lang/id/validation.php`;
APP_LOCALE=id; withExceptions JSON konsisten.

## 11. OWASP Top 10 / ASVS L2

Audit A01–A10 (akses & IDOR, kripto & cast encrypted & $hidden, injection, desain aksi berbahaya &
signed URL, misconfig, `composer audit`/`npm audit`, auth & Password::defaults, integritas, log keamanan
channel `security` tanpa data rahasia, SSRF); `preventSilentlyDiscardingAttributes` non-production;
tulis `docs/SECURITY.md`.

## 12. Standar kode

Deteksi stack/versi; Pint (commit terpisah `style: apply pint`); Larastan ≥ level 5; controller tipis,
FormRequest, Service/Action, Policy/Izin, Enum, API Resource, queue untuk proses berat, eager loading &
`preventLazyLoading` non-production; type hint, promotion, match; konvensi penamaan; Conventional Commits.

## 13. Dokumentasi

PHPDoc Bahasa Indonesia; `.env.example` berkomentar; README; `docs/` ARCHITECTURE, DATABASE (Mermaid),
SECURITY, DEPLOYMENT, API (bila ada api.php), CHANGELOG (Keep a Changelog).

## 14. Laravel Boost & plugin

`composer require laravel/boost --dev` → `php artisan boost:install` (agent Claude Code; bila interaktif
dan tak bisa dijawab, berhenti & minta pengguna menjalankan). MCP `laravel-boost` di `.mcp.json`
(`claude mcp add -s local -t stdio laravel-boost php artisan boost:mcp`). Guideline `.ai/guidelines/*.md`,
skill `.ai/skills/*/SKILL.md`, lalu `php artisan boost:update`. Laporkan status plugin (superpowers,
example-skills, semgrep, supply-chain-risk-auditor, ask-questions-if-underspecified) + perintah `/plugin`.
Jangan pasang dari sumber lain tanpa izin.

## 15. CLAUDE.md & skill

CLAUDE.md (di luar blok `<laravel-boost-guidelines>`), ±100–150 baris spesifik project. Skill dengan
frontmatter name/description: laravel-feature, code-standards, security-review, validation-and-errors,
ui-and-branding, seo-page, testing, deployment, project-setup. `.gitignore`: commit CLAUDE.md, .ai/,
.claude/skills/, .claude/settings.json, .mcp.json; abaikan .claude/settings.local.json.

## 16. Verifikasi (wajib)

1. composer install, npm install, npm run build. 2. optimize:clear → config/route/view:cache → optimize:clear.
3. migrate --pretend, route:list. 4. pint --test, phpstan. 5. composer audit, npm audit --omit=dev (+semgrep).
6. php artisan test (header keamanan, halaman error, 422 web/JSON, csrf, honeypot, guest 302/401,
403, robots & sitemap 200, meta SEO). 7. serve + `curl -I` halaman/aset/favicon/sitemap. 8. Browser:
tanpa CSP violation/console error. 9. Uji kode error via rute sementara (hapus setelahnya).
10. grep sisa nama lama/logo lama/secret. 11. Validasi SKILL.md & link docs. 12. Laporan akhir
(identitas, berkas per bagian, temuan keamanan, hasil alat, skill, plugin, tindakan manual:
php.ini, nginx/Apache, HTTPS, Cloudflare, Search Console).

## Catatan khusus project ini (Tukangsync)

- Multi-tenant: lihat `CLAUDE.md` (MilikTim, TimAktif, Izin); jangan merusak isolasi saat mengubah middleware.
- Tiga layout berbeda (admin, tim, publik) — terapkan SEO/CSRF/branding ke ketiganya.
- Deploy: `docs/DEPLOYMENT.md`, `deploy/`.
