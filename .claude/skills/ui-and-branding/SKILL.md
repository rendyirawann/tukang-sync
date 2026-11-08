---
name: ui-and-branding
description: Cara memakai layout, warna merek, logo, menu, dan aset Tukangsync, serta menambah CDN ke CSP tanpa memblokir aset. Pakai saat membuat/mengubah halaman, menu, logo, atau memasang library front-end.
---

# UI & branding

## Layout
| Area | Layout | Catatan |
|---|---|---|
| Superadmin | `resources/views/backend/layout/app.blade.php` | Metronic horizontal; menu `backend/layout/menu.blade.php` + `sidebar.blade.php` (mobile) |
| Panel tim | `resources/views/tim/layout/app.blade.php` (+ `head`, `scripts`) | sidebar navy, aksen emas, chip langganan |
| Publik | `resources/views/tim/layout/polos.blade.php` | beranda, daftar, status, login tim |

Halaman: `@extends(...)` + `@section('content')`; skrip di `@push('scripts')` (cek stack di layout).

## Merek
- Nama: `config('app.name')` / `$brand['name']` (dari `app/Support/Brand.php`) — jangan hardcode.
- Warna: utama `#1E3A5F` (navy), gelap `#0F1D33`, terang `#4A7BC4`, sekunder emas `#C9A44C`.
- Logo: `public/assets/media/branding/logo-mark.svg` (helm + panah sinkron), PNG/favicon di folder sama;
  `$brand['logo_url']`. Sumber gambar dibuat oleh skrip Pillow (geometri 100×100) — ubah geometrinya
  lalu hasilkan ulang SVG & PNG bersama-sama, juga `../o-mobile/assets/logo/*`.
- Komponen Metronic: `card`, `table align-middle table-row-dashed`, `badge badge-light-{warna}`
  (warna dari `StatusTim::warna()`), ikon `ki-duotone`.

## Aset & CSP
- Aset tema sudah jadi di `public/assets` (tanpa build). Bila memakai Vite (`resources/js`), jalankan `npm run build`.
- Domain CDN baru: tambahkan ke `config/security.php` atau `.env` `SECURITY_CSP_EXTRA_HOSTS`, lalu buka
  halaman dan pastikan console bersih (tanpa CSP violation). Jangan matikan CSP.

## Checklist
- [ ] Teks memakai `config('app.name')`
- [ ] Tombol aksi disembunyikan sesuai `Izin`
- [ ] Responsif di lebar ponsel
- [ ] Tidak ada aset 404 / error console

## Hindari
- Menyalin layout admin ke panel tim (sengaja berbeda tampilan).
- Memuat skrip dari domain yang belum masuk CSP.
