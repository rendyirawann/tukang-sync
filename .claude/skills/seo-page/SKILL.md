---
name: seo-page
description: Membuat halaman publik Tukangsync yang terindeks benar - title, meta description, canonical, Open Graph, JSON-LD, sitemap - dan menentukan kapan halaman wajib noindex. Pakai saat menambah atau mengubah halaman publik (beranda, paket, daftar, info).
---

# Halaman publik & SEO

## Langkah
1. Rute GET di `routes/publik.php` (nama rute jelas, mis. `beranda`, `daftar`).
2. View di `resources/views/publik/`, extend layout publik (`tim/layout/polos.blade.php`).
3. Isi section SEO (dibaca partial SEO di `resources/views/partials/`, konfigurasi `config/seo.php`):
   ```blade
   @section('title', 'Paket & Harga')
   @section('meta_description', 'Paket Tukangsync: Bulanan Rp49.000 atau Lifetime Rp99.000, maksimal 4 akun tim.')
   ```
   Title ≤ ±60 karakter, description ≤ ±155 karakter, satu `<h1>`, heading berurutan.
4. Gambar: `alt` bermakna (logo `alt="{{ config('app.name') }}"`), `width`/`height`, `loading="lazy"` di bawah lipatan.
5. Daftarkan ke sitemap (rute sitemap dinamis) bila halaman publik tanpa auth.
6. Uji: status 200, ada `<title>`, `meta description`, `canonical`, `og:title`.

## Wajib noindex
Halaman auth (`/admin/login`, `/{KODE}/login`), panel admin & tim, halaman status pembayaran tim
(`/{KODE}/status` berisi data pendaftar), halaman error, dan seluruh situs bila `APP_ENV` bukan production.

## Hindari
- Halaman per tim di sitemap (privat).
- Deskripsi generik yang sama di semua halaman.
