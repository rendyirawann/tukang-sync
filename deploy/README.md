# deploy/

Skrip pemasangan Tukangsync di server Ubuntu. Panduan lengkap:
[docs/DEPLOYMENT.md](../docs/DEPLOYMENT.md).

| Berkas | Kapan dipakai |
|---|---|
| `setup-server.sh` | sekali, sebagai root: paket, database, FrankenPHP, nginx, sertifikat SSL, `.env` produksi |
| `deploy.sh` | setiap deploy, sebagai `www-data`: rilis baru tanpa downtime + health check |
| `nginx/tukang.hustlesync.my.id.conf` | nginx (TLS + proxy ke Octane di 127.0.0.1:8010) |
| `systemd/tukangsync-octane.service` | server aplikasi (Laravel Octane / FrankenPHP) |
| `systemd/tukangsync-queue.service` | pekerja antrean |
| `systemd/tukangsync-scheduler.service` | penjadwal (`saas:bersihkan-pendaftaran` harian) |
| `backup-db.sh` | cron harian: dump PostgreSQL + arsip berkas unggahan |

Ringkas:

```bash
# sekali
DOMAIN=tukang.hustlesync.my.id EMAIL=anda@gmail.com bash setup-server.sh
sudo -u www-data /var/www/tukang-sync/repo/deploy/deploy.sh
systemctl enable --now tukangsync-octane tukangsync-queue tukangsync-scheduler

# setiap perubahan
sudo -u www-data /var/www/tukang-sync/repo/deploy/deploy.sh
```
