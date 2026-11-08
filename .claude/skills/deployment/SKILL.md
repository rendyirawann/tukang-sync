---
name: deployment
description: Checklist dan perintah rilis Tukangsync ke server produksi (transaksi.dormansyah.com) dengan skrip di deploy/. Pakai saat menyiapkan server, merilis perubahan, rollback, atau memeriksa produksi.
---

# Deployment

Panduan lengkap: `docs/DEPLOYMENT.md`. Skrip: `deploy/`.

## Sebelum rilis
1. `php artisan test` hijau; `./vendor/bin/pint --test`.
2. Migrasi baru aman dijalankan di data yang ada (tidak menghapus kolom berisi data tanpa rencana).
3. Merge ke branch `saas`, `git push origin saas`.

## Server baru (sekali)
```bash
DOMAIN=transaksi.dormansyah.com EMAIL=... bash deploy/setup-server.sh
sudo -u www-data /var/www/tukangsync/repo/deploy/deploy.sh
systemctl enable --now tukangsync-octane tukangsync-queue tukangsync-scheduler
cd /var/www/tukangsync/current
sudo -u www-data php artisan db:seed --class=RolePermissionSeeder --force
sudo -u www-data php artisan db:seed --class=SuperAdminSeeder --force
sudo -u www-data php artisan db:seed --class=SaasSeeder --force
```
Lalu ganti sandi superadmin, rekening & kontak WA contoh, nyalakan `SECURITY_HSTS=true` setelah HTTPS valid,
pasang cron `deploy/backup-db.sh`.

## Rilis rutin
```bash
sudo -u www-data /var/www/tukangsync/repo/deploy/deploy.sh
```

## Rollback
```bash
ln -sfn /var/www/tukangsync/releases/<SEBELUMNYA> /var/www/tukangsync/current
cd /var/www/tukangsync/current && sudo -u www-data php artisan octane:reload
```

## Checklist produksi
- [ ] `APP_ENV=production`, `APP_DEBUG=false`, `LOG_LEVEL=error`
- [ ] `TRUSTED_PROXIES=127.0.0.1`, `SESSION_SECURE_COOKIE=true`, HTTPS valid, HSTS
- [ ] Octane port 8010, queue, scheduler aktif
- [ ] `curl https://transaksi.dormansyah.com/api/health` → 200
- [ ] Cadangan harian berjalan

## Hindari
- `db:seed` tanpa `--class` di produksi; `DemoTimSeeder` di produksi.
- Header keamanan di nginx; mengubah `.env` tanpa `config:cache` + `octane:reload`.
