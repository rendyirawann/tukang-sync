# Keamanan Tukangsync

Acuan: OWASP Top 10 (2021) dan OWASP ASVS Level 2. Dokumen ini mencatat
kontrol yang terpasang, temuan audit, dan cara melaporkan celah.

## Melaporkan celah keamanan

Jangan membuka issue publik. Kirim rincian (langkah reproduksi, dampak,
URL/endpoint) ke pemilik aplikasi lewat email atau WhatsApp admin yang
tercantum di halaman pendaftaran. Kami menanggapi dalam 3 hari kerja dan
memperbaiki celah kritis secepatnya. Mohon tidak mengakses data tim lain
atau menjalankan uji yang merusak/membebani server.

## Kontrol per kategori OWASP

### A01 — Broken Access Control
- Tiga ruang akses terpisah: `/admin/*` hanya akun platform (`auth` +
  `platform` + `can:view_resources`), `/{kode}/*` hanya anggota tim itu
  (`auth` + `tim:anggota`), `/api/*` hanya token anggota tim aktif
  (`auth:sanctum` + `tim.api`).
- Isolasi data antartim (anti-IDOR): seluruh model domain memakai global
  scope `App\Models\Concerns\MilikTim`; id milik tim lain selalu 404.
  `App\Support\Tenancy\TimAktif` direset tiap request (aman untuk Octane).
- Hak per peran terpusat di `App\Support\Izin` (owner/kontraktor,
  mandor/anggota, viewer). Viewer dan langganan habis diblokir menulis di
  middleware (`TimWeb`, `TimApi`), bukan hanya di tampilan.
- **Diperbaiki:** `Backend\MyProfile\AccountController@updateAvatar`
  menerima `{id}` akun lain (IDOR). Sekarang hanya pemilik akun atau
  Superadmin.

### A02 — Cryptographic Failures
- Password: cast `hashed` (bcrypt). Token biometrik disimpan sebagai hash
  SHA-256; token Sanctum di-hash oleh Sanctum.
- Sesi terenkripsi (`SESSION_ENCRYPT=true`, default kini `true`), cookie
  HttpOnly + SameSite=lax, `Secure` bila `SESSION_SECURE_COOKIE=true`.
- Bukti transfer pelanggan disimpan di disk privat (`storage/app/private`),
  hanya bisa dibuka lewat rute superadmin.
- Catatan: nomor rekening di `bank_accounts` sengaja TIDAK dienkripsi:
  data itu memang ditampilkan publik di formulir pendaftaran. Kolom `nik`
  bawaan starter tidak dipakai.

### A03 — Injection
- Seluruh `whereRaw/selectRaw/orderByRaw` diaudit: semua memakai binding
  `?` atau nama kolom konstanta dari kode. Kolom sort API Kegiatan
  di-whitelist (`KegiatanController@index`).
- `{!! !!}` hanya dua tempat, keduanya HTML statis milik developer
  (ikon SVG login sosial, slot tombol header modul Langganan).
- `SanitizeInput` membersihkan tag berbahaya dari input teks;
  `BlockSuspiciousRequests` hanya memeriksa URL + query string (BUKAN isi
  formulir), sehingga catatan berisi tanda kutip atau kata "select" tetap
  tersimpan (diuji di `tests/Feature/Security/SecurityTest.php`).
- Unggahan: validasi `mimes` + `max`, nama berkas acak, disk privat untuk
  lampiran & bukti; `storage/app/public/.htaccess` menolak eksekusi skrip.
  Avatar kini memakai ekstensi hasil deteksi isi, bukan nama kiriman klien.

### A04 — Insecure Design
- Hapus tim, tangguhkan tim, ubah peran: tombol dengan konfirmasi, hanya
  Superadmin/pengelola, tercatat di log aktivitas & log keamanan.
- Tim wajib punya minimal satu owner/kontraktor aktif (`KelolaAnggota`).
- **Temuan (sudah diperbaiki):** perintah `kegiatan:bersihkan` warisan o-api
  dulu melakukan `TRUNCATE` untuk SEMUA tim. Sekarang wajib menyebut kode tim
  (`php artisan kegiatan:bersihkan KODE`) dan hanya menghapus proyek tim itu.

### A05 — Security Misconfiguration
- Header keamanan global (`App\Http\Middleware\SecurityHeaders`): CSP
  longgar-tapi-ada, `X-Content-Type-Options`, `X-Frame-Options`,
  `Referrer-Policy`, `Permissions-Policy` (hanya mematikan fitur yang tidak
  dipakai), HSTS bila `SECURITY_HSTS=true` dan HTTPS, `X-Powered-By`
  dihapus. Tidak memakai COEP/COOP/CORP ketat.
- `public/.htaccess`: tanpa directory listing, berkas tersembunyi (`.env`,
  `.git`) dan berkas sensitif ditolak.
- `.env` tidak pernah ter-commit (dicek di riwayat git). `.env.example`
  tanpa secret.
- Produksi: `APP_DEBUG=false`, `LOG_LEVEL=error`.

### A06 — Vulnerable Components
- `composer audit`: tidak ada advisory. `npm audit --omit=dev`: 0 celah.
- semgrep tidak terpasang di lingkungan ini; jalankan
  `semgrep --config p/laravel` saat tersedia.

### A07 — Identification & Authentication Failures
- `Password::defaults()`: min 8, huruf besar-kecil, angka;
  `uncompromised()` di produksi.
- Login: `session()->regenerate()` setelah login, logout melakukan
  `invalidate()` + `regenerateToken()`. Throttle per email+IP plus kunci
  bertahap 10–60 detik.
- Pesan login generik ("Akun atau Password salah."). **Diperbaiki:** lupa
  password kini selalu menjawab pesan yang sama, tidak membocorkan apakah
  email terdaftar.
- Anti-bot di semua formulir publik (login admin & tim, daftar tim,
  register, lupa & reset password): honeypot + time-trap 3 detik
  (`App\Support\AntiSpam`, komponen `<x-honeypot />`), opsional Cloudflare
  Turnstile (`TURNSTILE_ENABLED`).

### A08 — Software & Data Integrity
- `composer.lock` dan `package-lock.json` ter-commit.
- Tidak ada webhook eksternal; pengecualian CSRF kosong.

### A09 — Logging & Monitoring
- Channel `security` (`storage/logs/security-YYYY-MM-DD.log`, 30 hari):
  login berhasil/gagal, lockout, logout, permintaan & reset password,
  perubahan peran/status akun, akses ditolak 403, bot ditolak, URL
  mencurigakan. Email disamarkan; password/token tidak pernah dicatat.

### A10 — SSRF
- Tidak ada fitur yang mengambil URL dari input pengguna. Satu-satunya
  request keluar adalah verifikasi Turnstile ke URL tetap Cloudflare.

## Rate limit

| Limiter | Default | Kunci |
|---|---|---|
| web | 300/menit | user id / IP |
| write | 60/menit | user id / IP |
| api | 60/menit | user id / IP |
| login | 5/menit + 20/menit per IP | email + IP |
| password | 5/menit | email + IP |
| daftar | 3/menit, 10/jam | IP |

Respons 429 membawa `Retry-After`. Di balik nginx/Cloudflare, isi
`TRUSTED_PROXIES` agar IP pengguna terbaca benar.

## Tindakan manual di server
- `expose_php = Off` di php.ini.
- Document root nginx/Apache = `public/`; blok `/\.` di nginx.
- Aktifkan HTTPS lalu `SECURITY_HSTS=true` dan `SESSION_SECURE_COOKIE=true`.
- Isi `TRUSTED_PROXIES` bila memakai Cloudflare/reverse proxy.
