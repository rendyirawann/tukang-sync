# Arsitektur Tukangsync

## Gambaran besar

Satu aplikasi Laravel 12 dengan tiga "pintu":

| Pintu | Rute | Auth | Siapa |
|---|---|---|---|
| Panel superadmin | `routes/web.php` (+ `routes/admin_saas.php`), prefix `/admin` | session, middleware `auth` + `platform` | pemilik SaaS (akun tanpa tim, role spatie `Superadmin`) |
| Web publik & panel tim | `routes/publik.php`, `routes/tim.php`, `routes/tim_panel.php`, prefix `/{tim}` | session, middleware `tim` / `tim:anggota` | calon pelanggan dan anggota tim |
| API mobile | `routes/api.php`, prefix `/api` | Sanctum + middleware `tim.api` | aplikasi Android |

`routes/publik.php` dan `routes/tim.php` di-`require` **paling akhir** di
`web.php`, karena `/{tim}` menangkap path satu segmen apa pun yang belum
terdaftar.

## Multi-tenant (inti desain)

- Tabel `teams`: satu tim = satu pelanggan, dikenali lewat `slug` 4 karakter.
- Setiap tabel data proyek memiliki `team_id`.
- `app/Support/Tenancy/TimAktif.php`: tim yang dilayani request ini. Diisi oleh:
  - `app/Http/Middleware/TimWeb.php` dari slug di URL;
  - `app/Http/Middleware/TimApi.php` dari akun yang login (API).
  Bila kosong, `TimAktif::id()` jatuh ke `auth()->user()->team_id`.
  `ResetTimAktif` mengosongkannya di awal & akhir setiap request (aman untuk Octane).
- `app/Models/Concerns/MilikTim.php`: global scope `where team_id = TimAktif::id()`
  dan pengisian `team_id` otomatis saat `creating`. Dipakai Kegiatan, CashFlow,
  BahanBakuItem, Lampiran, MasterData, ActivityLog, Material, Analisa,
  Bangunan, Lantai, Ruangan, Pekerjaan, LogHarian.
- `User` sengaja **tanpa** global scope (provider auth & Sanctum harus bisa
  menemukan akun sebelum tim diketahui); query anggota memakai
  `User::anggotaTim($teamId)`.
- Superadmin (tanpa tim) tidak tersaring → melihat semua tim di `/admin`.

## Alur request

```
Request
 → ResetTimAktif, SecurityHeaders, BlockSuspiciousRequests (global)
 → grup web / api (session, CSRF, SanitizeInput, throttle)
 → auth / auth:sanctum
 → platform | tim | tim:anggota | tim.api   (status tim, langganan, viewer)
 → Controller (tipis)
     → FormRequest / validate()
     → Izin::... (otorisasi per peran)
     → Service (PenyediaTim, KelolaAnggota, RabCalculator)
     → Model (MilikTim)
 → View Blade | ApiResponse / JsonResource
```

## Peran & izin

`app/Enums/Peran.php` + `app/Support/Izin.php` (satu sumber untuk server dan
flag `izin` yang dikirim ke aplikasi):

| Peran | Kelompok | Boleh |
|---|---|---|
| owner, kontraktor | pengelola | semua: proyek, RAB, katalog, keuangan, anggota, langganan |
| mandor, anggota | pelaksana | progres, log harian, bahan baku, kas kategori upah/administrasi |
| viewer | — | hanya baca (ditolak di middleware untuk semua penulisan) |

Langganan habis (`Team::hanyaBaca()`): penulisan ditolak (web: kembali dengan
pesan; API: `402 SUBSCRIPTION_EXPIRED`), kecuali akun sendiri & langganan.

## Modul

| Modul | Lokasi utama |
|---|---|
| Langganan SaaS | `app/Services/Saas/PenyediaTim.php` (daftar, tagihan, konfirmasi, tolak, simpan bukti), `KelolaAnggota.php`, `KatalogAwal.php` (data awal tim), controller `Backend/Saas/*` |
| Pendaftaran publik | `Publik/PendaftaranController`, `Tim/StatusController` (tautan wa.me) |
| Proyek & taksasi | `Kegiatan`, `app/Services/TaksasiCalculator.php` |
| Arus kas, bahan baku, lampiran | `CashFlow`, `BahanBakuItem`, `Lampiran`, controller `Api/*` |
| RAB | `Bangunan`, `Lantai`, `Ruangan`, `Pekerjaan`, `PekerjaanMaterial`, `Analisa`, `AnalisaItem`, `Material`, `LogHarian`; hitungan di `app/Services/Rab/RabCalculator.php`; enum `KategoriPekerjaan`, `JenisKomponen`, `SatuanVolume` |
| Laporan PDF | `Api/ReportController`, `resources/views/pdf/*` |
| Jejak aktivitas | API: `CatatAktivitas` → `activity_logs` (per tim); panel admin: spatie `activity_log` via `App\Support\ActivityRecorder` |

## Hitungan RAB

1. Volume = `SatuanVolume::hitung(jumlah, p, l, t)` kecuali `volume_manual`.
2. Menerapkan analisa menyalin item resep ke `pekerjaan_material` dengan harga
   katalog **saat itu** (RAB lama tidak berubah saat harga katalog naik).
3. qty kebutuhan = koefisien × volume (kecuali `qty_manual`); subtotal = qty × harga.
4. Total pekerjaan = Σ subtotal, atau volume × harga_satuan bila tanpa rincian (borongan).
5. Rekap material proyek dibandingkan dengan belanja `bahan_baku_items` yang ditautkan ke `material_id`.

## Tampilan

- Superadmin: `resources/views/backend/**`, layout `backend/layout/app.blade.php` (Metronic horizontal).
- Tim: `resources/views/tim/**`, layout `tim/layout/app.blade.php` (sidebar navy + emas).
- Publik: `resources/views/publik/**`, layout `tim/layout/polos.blade.php`.
- Identitas: `app/Support/Brand.php` (nama, logo, favicon dari `settings` + `config('app.*')`).
