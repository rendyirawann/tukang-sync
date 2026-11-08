# Changelog

Semua perubahan penting dicatat di sini. Format mengikuti
[Keep a Changelog](https://keepachangelog.com/id-ID/1.1.0/), versi mengikuti
[Semantic Versioning](https://semver.org/lang/id/).

## [Unreleased]

### Ditambahkan
- Setup project **Tukangsync**: identitas (nama, logo helm + panah sinkron,
  favicon), dokumentasi `docs/`, `CLAUDE.md`, guideline & skill project di
  `.ai/` dan `.claude/skills/`.
- Skrip deploy server baru untuk `transaksi.dormansyah.com`
  (`deploy/`, `docs/DEPLOYMENT.md`): FrankenPHP/Octane di port 8010, nginx +
  Let's Encrypt, systemd, cadangan harian.
- Perintah terjadwal `saas:bersihkan-pendaftaran` (harian 02:00).

## [2.0.0] - 2026-11-08

Konversi aplikasi tunggal "Transaksi Pekerjaan" (o-api) menjadi SaaS
multi-tim di atas kerangka startertempalt.

### Ditambahkan
- Tim (tenant) dengan kode 4 karakter, scope `MilikTim` + `TimAktif`.
- Paket langganan Bulanan Rp49.000 & Lifetime Rp99.000, maks 4 akun per tim.
- Peran tim: owner, kontraktor, mandor, anggota, viewer.
- Pendaftaran publik `/daftar` → halaman status → konfirmasi WhatsApp.
- Panel superadmin: tim, pembayaran (bukti tempel/unggah, ACC/tolak), paket,
  rekening, kontak WhatsApp, kelola akun tiap tim, ringkasan SaaS di dashboard.
- Panel web tim `/{KODE}`: dashboard, proyek, RAB, katalog, anggota,
  langganan, akun.
- RAB universal: bangunan › lantai › ruangan, item pekerjaan dengan volume
  otomatis, analisa harga satuan (AHSP), kebutuhan material, rekap vs
  realisasi, progres, log harian. Katalog awal 35 material/upah + 12 analisa.
- API RAB (lihat `docs/API-RAB.md`), login API dengan `kode_tim`, kode error
  `TEAM_PENDING`, `TEAM_SUSPENDED`, `SUBSCRIPTION_EXPIRED`.
- Aplikasi Android SaaS (`o-saas-mobile`, applicationId `com.oproject.o_saas`).

### Diubah
- ID akun menjadi UUID; peran lama `superadmin/petugas` dipetakan ke
  pengelola/pelaksana (field `role` lama tetap dikirim untuk kompatibilitas).
- Kop PDF memakai identitas tim.
- Keunikan kode proyek & data master per tim.

### Keamanan
- Isolasi data antartim diuji (akses lintas tim → 404).
- Bukti transfer disimpan di disk privat, hanya dibuka superadmin.
