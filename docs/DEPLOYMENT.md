# Deploy Tukangsync ke server

Panduan ini memasang **Tukangsync** (web superadmin, web tim, dan API untuk
aplikasi Android) di **https://tukang.hustlesync.my.id**, dari repo
**github.com/rendyirawann/tukang-sync**, ke folder `/var/www/tukang-sync` di server.
Alamat ini sudah tertanam di APK `tukangsync.apk`.

Satu aplikasi Laravel melayani semuanya:

| Alamat | Isi |
|---|---|
| `https://tukang.hustlesync.my.id/` | beranda & paket |
| `/daftar` | pendaftaran tim |
| `/admin/login` | panel superadmin (Anda) |
| `/{KODE}/login` | panel web tim, mis. `/K7QX/login` |
| `/api/...` | API aplikasi Android |
| `/api/documentation` | Swagger |

## Arsitektur di server

```
Internet ──443──> nginx (TLS, berkas statis /assets)
                    └──> 127.0.0.1:8010  Laravel Octane (FrankenPHP)
                                          ├── PostgreSQL  (db: tukangsync)
                                          └── Redis       (cache, session, antrean)
systemd: tukangsync-octane, tukangsync-queue, tukangsync-scheduler
```

```
/var/www/tukang-sync/
├── repo/              klon git (branch main)
├── releases/2026…/    satu folder per rilis (5 terakhir disimpan)
├── shared/
│   ├── .env           konfigurasi produksi
│   └── storage/       bukti transfer (privat), foto struk, log
└── current -> releases/2026…
```

## Berkas di repo

| Berkas | Fungsi |
|---|---|
| `deploy/setup-server.sh` | menyiapkan server dari nol (sekali) |
| `deploy/deploy.sh` | deploy setiap ada perubahan |
| `deploy/nginx/tukang.hustlesync.my.id.conf` | konfigurasi nginx |
| `deploy/systemd/tukangsync-*.service` | Octane, antrean, penjadwal |
| `deploy/backup-db.sh` | cadangan harian database + berkas unggahan |

---

## 0. Sebelum mulai

1. **Push kode ke GitHub** (dari komputer lokal, folder `o/o-api`):
   ```bash
   git checkout main
   git push origin main
   ```
2. **DNS**: buat A record `tukang` di zona `hustlesync.my.id` → IP server.
   Periksa: `dig +short tukang.hustlesync.my.id` (harus menampilkan IP server)
   sebelum menjalankan setup, karena sertifikat SSL diminta untuk domain ini.
3. Server Ubuntu 22.04/24.04, akses root, port 80 & 443 terbuka:
   ```bash
   ufw allow OpenSSH && ufw allow 'Nginx Full' && ufw enable
   ```

### Aplikasi lain di server yang sama

Tukangsync berdiri sendiri: folder `/var/www/tukang-sync`, port Octane
**8010**, database `tukangsync`, unit systemd `tukangsync-*`, dan konfigurasi
nginx khusus `tukang.hustlesync.my.id`. Aplikasi lain (mis. o-api lama di
`tukang.hustlesync.my.id`) tidak disentuh, selama tidak memakai port 8010.

---

## 1. Siapkan server (sekali)

```bash
# di server, sebagai root
curl -fsSL https://raw.githubusercontent.com/rendyirawann/tukang-sync/main/deploy/setup-server.sh -o setup.sh
DOMAIN=tukang.hustlesync.my.id EMAIL=email-anda@gmail.com bash setup.sh
```

Skrip ini memasang PHP 8.3, nginx, PostgreSQL, Redis, Composer, FrankenPHP,
certbot; membuat database `tukangsync` + penggunanya; mengklon repo; mengisi
`shared/.env` (nilai produksi, kredensial database, `APP_KEY`); memasang unit
systemd; lalu **menerbitkan sertifikat Let's Encrypt baru** untuk domain —
ini yang memperbaiki sertifikat salah yang terpasang sekarang.

> Tambahkan `WITH_WWW=1` hanya bila `www.tukang.hustlesync.my.id` juga
> sudah diarahkan ke server.

### Periksa `.env`

```bash
nano /var/www/tukang-sync/shared/.env
```

Yang sudah diisi otomatis: `APP_ENV=production`, `APP_DEBUG=false`,
`APP_URL=https://tukang.hustlesync.my.id`, `LOG_LEVEL=error`, `DB_*`,
`CACHE_STORE/SESSION_DRIVER/QUEUE_CONNECTION=redis`,
`SESSION_SECURE_COOKIE=true`, `TRUSTED_PROXIES=127.0.0.1`,
`L5_SWAGGER_CONST_HOST`, `APP_KEY`.

Lengkapi sendiri bila perlu: `MAIL_*` (email lupa sandi), `REVERB_*`
(hanya bila memakai notifikasi real-time; bawaannya `BROADCAST_CONNECTION=log`).

## 2. Deploy pertama

```bash
sudo -u www-data /var/www/tukang-sync/repo/deploy/deploy.sh
systemctl enable --now tukangsync-octane tukangsync-queue tukangsync-scheduler
```

## 3. Isi data awal (sekali — TANPA data demo)

```bash
cd /var/www/tukang-sync/current
sudo -u www-data php artisan db:seed --class=RolePermissionSeeder --force
sudo -u www-data php artisan db:seed --class=SuperAdminSeeder --force
sudo -u www-data php artisan db:seed --class=SaasSeeder --force
```

**Jangan** menjalankan `db:seed` tanpa `--class`: `DemoTimSeeder` (tim
`DEMO`) hanya untuk lokal — `DatabaseSeeder` memang melewatinya di production,
tetapi lebih aman eksplisit.

## 4. Langsung setelah itu

1. Buka `https://tukang.hustlesync.my.id/admin/login`, masuk dengan akun
   superadmin bawaan (lihat `database/seeders/SuperAdminSeeder.php`), lalu
   **segera ganti sandinya** di menu profil › Keamanan.
2. **Langganan › Rekening**: hapus rekening contoh (BCA 0000000000), isi
   rekening Anda.
3. **Langganan › Kontak WhatsApp**: ganti nomor contoh dengan nomor WA Anda,
   tandai sebagai *utama* (tujuan tombol konfirmasi pendaftar).
4. **Langganan › Paket**: periksa harga (Bulanan Rp49.000, Lifetime Rp99.000).

## 5. Nyalakan HSTS (setelah HTTPS terbukti jalan)

```bash
sed -i 's/^SECURITY_HSTS=.*/SECURITY_HSTS=true/' /var/www/tukang-sync/shared/.env
cd /var/www/tukang-sync/current && sudo -u www-data php artisan config:cache
sudo -u www-data php artisan octane:reload
```

HSTS lengket di browser selama setahun — nyalakan hanya bila sertifikat
sudah benar.

## 6. Cadangan harian

```bash
chmod +x /var/www/tukang-sync/repo/deploy/backup-db.sh
( crontab -l 2>/dev/null; echo "0 1 * * * /var/www/tukang-sync/repo/deploy/backup-db.sh >> /var/log/tukangsync/backup.log 2>&1" ) | crontab -
```

Hasilnya di `/var/backups/tukangsync` (14 hari terakhir), berisi dump
database dan arsip `storage/app` (bukti transfer & foto struk). Salin ke
tempat lain secara berkala.

Pulihkan:
```bash
gunzip -c /var/backups/tukangsync/tukangsync-YYYYMMDD-HHMMSS.sql.gz | sudo -u postgres psql tukangsync
```

---

## Deploy berikutnya

Setiap ada perubahan yang sudah di-push ke branch `main`:

```bash
sudo -u www-data /var/www/tukang-sync/repo/deploy/deploy.sh
```

Skrip ini: ambil kode → rilis baru → `composer install --no-dev` →
`octane:install` → `migrate --force` → cache config/route/view/event →
Swagger → alihkan `current` → `octane:reload` + `queue:restart` → hapus rilis
lama → health check.

## Rollback

```bash
ls -1t /var/www/tukang-sync/releases        # rilis terbaru di atas
ln -sfn /var/www/tukang-sync/releases/<RILIS-SEBELUMNYA> /var/www/tukang-sync/current
cd /var/www/tukang-sync/current && sudo -u www-data php artisan octane:reload
```

Migrasi tidak ikut dibatalkan. Bila rilis yang gagal membawa migrasi yang
merusak, pulihkan dari cadangan database.

## Memeriksa setelah deploy

```bash
curl -s https://tukang.hustlesync.my.id/api/health            # {"success":true,...}
curl -s -o /dev/null -w '%{http_code}\n' https://tukang.hustlesync.my.id/            # 200
curl -s -o /dev/null -w '%{http_code}\n' https://tukang.hustlesync.my.id/admin/login # 200
curl -s -o /dev/null -w '%{http_code}\n' https://tukang.hustlesync.my.id/api/documentation # 200
curl -sI https://tukang.hustlesync.my.id/ | grep -iE 'strict-transport|content-security|x-frame'

# Sertifikat harus atas nama domain ini:
echo | openssl s_client -connect tukang.hustlesync.my.id:443 -servername tukang.hustlesync.my.id 2>/dev/null \
  | openssl x509 -noout -subject -dates

# Login API (tim harus sudah aktif):
curl -s -X POST https://tukang.hustlesync.my.id/api/auth/login \
  -H 'Accept: application/json' -H 'Content-Type: application/json' \
  -d '{"kode_tim":"K7QX","email":"owner@contoh.com","password":"..."}'
```

Uji ujung ke ujung: daftar tim lewat `/daftar` → ACC di `/admin` → login di
`/{KODE}/login` → login di APK dengan kode tim yang sama.

## Log

```bash
tail -f /var/www/tukang-sync/shared/storage/logs/laravel.log
journalctl -u tukangsync-octane -f
tail -f /var/log/tukangsync/{octane,queue,scheduler}.error.log
tail -f /var/log/nginx/tukangsync.error.log
```

## Masalah umum

| Gejala | Penyebab & perbaikan |
|---|---|
| Unggah bukti/struk gagal 413 | `client_max_body_size` nginx atau `upload_max_filesize` PHP; keduanya diatur skrip (12M). |
| Health check 000/502 | Octane mati: `journalctl -u tukangsync-octane -n 50`; pastikan `/usr/local/bin/frankenphp version` jalan. |
| Semua pengguna kena 429 bersamaan | `TRUSTED_PROXIES` kosong → semua IP terbaca 127.0.0.1. Isi `127.0.0.1`, lalu `config:cache` + `octane:reload`. |
| Tautan/redirect memakai `http://` | Sama: `TRUSTED_PROXIES`, dan `APP_URL` harus `https://`. |
| Perubahan `.env` tidak berlaku | Konfigurasi di-cache: `php artisan config:cache && php artisan octane:reload`. |
| Error 419 setelah login | `SESSION_SECURE_COOKIE=true` tetapi dibuka lewat `http://`; selalu pakai `https://`. |
| Aplikasi Android "Tidak dapat terhubung" | Sertifikat belum valid untuk domain — periksa dengan `openssl s_client` di atas. |

## Checklist produksi

- [ ] `APP_ENV=production`, `APP_DEBUG=false`, `LOG_LEVEL=error`
- [ ] HTTPS valid, lalu `SECURITY_HSTS=true`
- [ ] `TRUSTED_PROXIES=127.0.0.1`, `SESSION_SECURE_COOKIE=true`
- [ ] Sandi superadmin bawaan sudah diganti
- [ ] Rekening & kontak WA contoh sudah diganti
- [ ] `tukangsync-octane`, `-queue`, `-scheduler` aktif (`systemctl status`)
- [ ] Cron cadangan terpasang dan pernah diuji pulihkan
- [ ] `config:cache`, `route:cache`, `view:cache` (dijalankan `deploy.sh`)
- [ ] Izin `shared/storage` milik `www-data`, `.env` mode 640
- [ ] `php.ini`: `expose_php = Off` (diatur skrip)
