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
