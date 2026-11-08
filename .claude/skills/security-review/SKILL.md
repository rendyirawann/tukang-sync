---
name: security-review
description: Checklist keamanan OWASP Top 10 / ASVS L2 khusus Tukangsync (multi-tenant). Pakai sebelum commit atau merge fitur, saat menambah rute/endpoint/unggahan, atau saat diminta audit keamanan.
---

# Review keamanan

## Langkah

1. Daftar rute yang berubah: `php artisan route:list --path=...`.
2. Periksa tiap kategori di bawah; perbaiki atau laporkan.
3. Jalankan `php artisan test`, `composer audit`, dan semgrep bila tersedia.
4. Catat kontrol baru di `docs/SECURITY.md`.

## Checklist

**A01 Akses**
- [ ] Rute ubah data di dalam `auth` + `platform` (admin) / `tim:anggota` (web tim) / `auth:sanctum` + `tim.api` (API).
- [ ] Otorisasi lewat `App\Support\Izin` di controller/FormRequest.
- [ ] Model tim memakai `MilikTim`; FK dari input dicek milik tim & induk yang sama → 404/422.
- [ ] Ada test isolasi lintas tim (lihat `tests/Feature/Api/TimTest.php`, `tests/Feature/Tim/PanelTimTest.php`).
- [ ] Viewer & langganan habis tetap ditolak menulis (middleware `TimWeb`, `TimApi`).

**A02 Kripto**: sandi via cast `hashed`; token biometrik disimpan hash (`User::issueBiometricToken`); atribut sensitif di `$hidden`.

**A03 Injection**: `whereRaw/selectRaw/orderByRaw` dengan binding; sort/filter whitelist; Blade `{{ }}` bukan `{!! !!}`.

**A04 Desain**: hapus tim/akun dengan konfirmasi + log; bukti transfer hanya lewat rute superadmin (`saas.bayar.bukti`).

**A05 Konfigurasi**: `APP_DEBUG=false` produksi; header hanya dari `app/Http/Middleware/SecurityHeaders.php`;
CSP diubah lewat `config/security.php`.

**A06 Komponen**: `composer audit` bersih.

**A07 Auth**: login web `LoginRequest::authenticate($teamId)` (`app/Http/Requests/Auth/LoginRequest.php`)
membatasi akun ke tim/admin; regenerate session; pesan generik; throttle.

**A08 Integritas**: lock file ter-commit.

**A09 Logging**: jangan log sandi/token/isi .env; `ActivityRecorder::log()` tanpa model ber-ID integer sebagai subject.

**A10 SSRF**: tidak ada fetch URL dari input; bila ditambah, hanya http/https dan tolak IP privat.

**Unggahan**: `mimes` + `max`, disk privat (`Storage::disk('local')`), nama acak, MIME dari isi (`finfo`, lihat `PenyediaTim::bacaDataUrl`).

## Hindari

- Menambah header keamanan di nginx (ganda → diabaikan browser).
- `User::find($id)` dari input tanpa `anggotaTim()`.
