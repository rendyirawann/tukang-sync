# API Tukangsync

Dokumentasi interaktif: **`/api/documentation`** (Swagger, dari atribut
OpenAPI di controller). Spesifikasi JSON: `/docs`. Detail endpoint RAB:
[API-RAB.md](API-RAB.md).

## Dasar

- Base URL: `https://tukang.hustlesync.my.id/api`
- Header: `Accept: application/json`, `Authorization: Bearer <token>`
- Envelope:
  ```json
  { "success": true,  "message": "…", "data": … }
  { "success": false, "message": "…", "code": "VALIDATION_ERROR", "errors": { "field": ["…"] } }
  ```
- Uang = integer rupiah; ID akun = UUID string; ID lain = integer.
- Semua data otomatis milik tim akun yang login; data tim lain → 404.

## Autentikasi

| Metode | Path | Keterangan |
|---|---|---|
| POST | `/auth/login` | `{kode_tim, email, password, device_name}` → `{access_token, expires_at, user}`; satu sesi per akun |
| POST | `/auth/biometric/login` | `{email, biometric_token}` |
| GET | `/auth/me` | profil + `peran`, `tim`, `izin` |
| PUT | `/auth/profil` | ubah profil |
| POST | `/auth/change-password` | ganti sandi (mencabut token lain & sidik jari) |
| POST | `/auth/biometric/enable`, DELETE `/auth/biometric` | sidik jari |
| POST | `/auth/logout` | |

Contoh:
```bash
curl -X POST https://tukang.hustlesync.my.id/api/auth/login \
  -H 'Accept: application/json' -H 'Content-Type: application/json' \
  -d '{"kode_tim":"DEMO","email":"owner@demo.test","password":"password123","device_name":"curl"}'
```

## Kelompok endpoint

| Kelompok | Path |
|---|---|
| Kesehatan | `GET /health` (publik) |
| Referensi | `GET /referensi`, `GET /referensi/rab` |
| Proyek (kegiatan) | `apiResource /kegiatan`, `POST /kegiatan/preview` |
| Arus kas | `/kegiatan/{id}/cash-flows[/rekap]`, `/cash-flows/{id}` |
| Bahan baku | `/kegiatan/{id}/bahan-baku`, `/bahan-baku/{id}` |
| Lampiran | `/kegiatan/{id}/lampiran`, `/lampiran/{id}[/berkas]` |
| Anggota tim | `apiResource /pengguna` (owner/kontraktor) |
| Data master | `/master[/{jenis}]`, `/master/{id}` |
| Aktivitas | `/aktivitas`, `/aktivitas/saya`, `/aktivitas/ringkasan` |
| Laporan | `/laporan/ringkasan`, `/laporan/rekap-kegiatan[/pdf]`, `/laporan/arus-kas/pdf`, `/laporan/kegiatan/{id}/pdf` |
| RAB | `/kegiatan/{id}/rab`, struktur, pekerjaan, kebutuhan, rekap material, katalog material & analisa, log harian — lihat [API-RAB.md](API-RAB.md) |

## Kode error

| HTTP | `code` | Arti |
|---|---|---|
| 401 | `UNAUTHENTICATED`, `INVALID_CREDENTIALS`, `BIOMETRIC_INVALID` | token habis / kredensial salah |
| 402 | `SUBSCRIPTION_EXPIRED` | masa langganan habis, hanya baca |
| 403 | `FORBIDDEN` | peran tidak berhak (termasuk viewer menulis) |
| 403 | `TEAM_PENDING` | pembayaran tim belum dikonfirmasi |
| 403 | `TEAM_SUSPENDED` | tim ditangguhkan |
| 403 | `ACCOUNT_DISABLED`, `NOT_TEAM_MEMBER` | akun nonaktif / bukan anggota tim |
| 404 | `NOT_FOUND`, `MODEL_NOT_FOUND` | endpoint / data tidak ada (atau milik tim lain) |
| 405 | `METHOD_NOT_ALLOWED` | |
| 413 | `PAYLOAD_TOO_LARGE` | berkas > batas |
| 422 | `VALIDATION_ERROR` | `errors` per field |
| 429 | `TOO_MANY_REQUESTS`, `TOO_MANY_ATTEMPTS` | header `Retry-After` |
| 500 | `SERVER_ERROR` | tanpa detail di production |
