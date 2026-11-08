#!/usr/bin/env bash
#
# Menyiapkan server dari nol untuk Tukangsync (SaaS).
# Diuji pada Ubuntu 24.04. Dijalankan SEKALI, sebagai root:
#
#   sudo DOMAIN=transaksi.dormansyah.com EMAIL=anda@contoh.com bash setup-server.sh
#
# Setelah selesai, deploy berikutnya cukup:
#   sudo -u www-data /var/www/tukangsync/repo/deploy/deploy.sh
#
# Panduan lengkap: docs/DEPLOYMENT.md

set -euo pipefail

DOMAIN="${DOMAIN:-transaksi.dormansyah.com}"
EMAIL="${EMAIL:-}"
REPO="${REPO:-https://github.com/rendyirawann/0-apps.git}"
BRANCH="${BRANCH:-saas}"
APP_DIR="${APP_DIR:-/var/www/tukangsync}"
DB_NAME="${DB_NAME:-tukangsync}"
DB_USER="${DB_USER:-tukangsync}"
PHP_VER="${PHP_VER:-8.3}"

# Ikut menerbitkan sertifikat untuk www.<domain>? Hanya bila DNS www juga
# menunjuk ke server ini, kalau tidak certbot gagal.
WITH_WWW="${WITH_WWW:-0}"

info() { printf '\n\033[1;34m==>\033[0m %s\n' "$1"; }

[ "$(id -u)" -eq 0 ] || { echo "Jalankan sebagai root (sudo)."; exit 1; }
[ -n "$EMAIL" ] || { echo "Isi EMAIL=... (dipakai Let's Encrypt untuk pemberitahuan sertifikat)."; exit 1; }

# ------------------------------------------------------------------- 1. paket
info "Memasang paket dasar"
apt-get update
apt-get install -y software-properties-common curl git unzip ca-certificates openssl

info "Memasang PHP ${PHP_VER} dan ekstensi"
add-apt-repository -y ppa:ondrej/php
apt-get update
# pgsql        -> PostgreSQL
# mbstring, xml, bcmath, curl, zip -> Laravel, dompdf, datatables
# gd           -> dompdf (gambar pada PDF)
# intl         -> format angka & tanggal Indonesia
# redis        -> cache, session, antrean
# fileinfo     -> deteksi jenis berkas bukti transfer (PNG/JPG/PDF)
apt-get install -y \
    "php${PHP_VER}-cli" "php${PHP_VER}-common" "php${PHP_VER}-pgsql" "php${PHP_VER}-mbstring" \
    "php${PHP_VER}-xml" "php${PHP_VER}-bcmath" "php${PHP_VER}-curl" "php${PHP_VER}-zip" \
    "php${PHP_VER}-gd" "php${PHP_VER}-intl" "php${PHP_VER}-redis"

info "Memasang nginx, PostgreSQL, Redis, certbot"
apt-get install -y nginx postgresql redis-server certbot

info "Memasang Composer"
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm -f /tmp/composer-setup.php

# ---------------------------------------------------------------- 2. batas PHP
# Foto struk sampai 8 MB, bukti transfer sampai 5 MB. Bawaan PHP (2M) menolak
# lebih dulu, jadi dinaikkan di sisi PHP juga, bukan hanya di nginx.
info "Mengatur batas PHP"
cat > "/etc/php/${PHP_VER}/cli/conf.d/99-tukangsync.ini" <<'INI'
upload_max_filesize = 12M
post_max_size = 14M
memory_limit = 512M
max_execution_time = 120

; Versi PHP tidak perlu diumumkan di setiap balasan.
expose_php = Off
INI

# ------------------------------------------------------------------ 3. Redis
# Cache boleh dibuang saat memori penuh, antrean TIDAK. Keduanya dipisah per
# database Redis (REDIS_DB / REDIS_CACHE_DB) dan kebijakannya noeviction.
info "Mengatur Redis"
sed -i 's/^# *maxmemory .*/maxmemory 256mb/'                    /etc/redis/redis.conf
sed -i 's/^# *maxmemory-policy .*/maxmemory-policy noeviction/' /etc/redis/redis.conf
sed -i 's/^supervised .*/supervised systemd/'                   /etc/redis/redis.conf
systemctl enable --now redis-server
systemctl restart redis-server

# --------------------------------------------------------------- 4. PostgreSQL
info "Membuat database dan pengguna PostgreSQL"
SANDI="$(openssl rand -base64 24 | tr -d '/+=' | head -c 24)"

sudo -u postgres psql <<SQL
DO \$\$ BEGIN
  IF NOT EXISTS (SELECT FROM pg_roles WHERE rolname = '${DB_USER}') THEN
    CREATE ROLE ${DB_USER} LOGIN PASSWORD '${SANDI}';
  ELSE
    ALTER ROLE ${DB_USER} PASSWORD '${SANDI}';
  END IF;
END \$\$;
SQL

sudo -u postgres psql -tc "SELECT 1 FROM pg_database WHERE datname='${DB_NAME}'" \
  | grep -q 1 || sudo -u postgres createdb -O "${DB_USER}" "${DB_NAME}"

# --------------------------------------------------------------- 5. FrankenPHP
# Dipasang SISTEM-WIDE ke /usr/local/bin. Kalau dibiarkan diunduh oleh
# octane:install, binernya (~150 MB) mendarat di folder rilis dan ikut
# terhapus setiap deploy.
info "Memasang biner FrankenPHP"
ARCH="$(uname -m)"
if getconf GNU_LIBC_VERSION >/dev/null 2>&1; then GNU="-gnu"; else GNU=""; fi

case "$ARCH" in
    x86_64)  ASET="frankenphp-linux-x86_64${GNU}" ;;
    aarch64) ASET="frankenphp-linux-aarch64${GNU}" ;;
    *) echo "FrankenPHP hanya tersedia untuk x86_64 dan aarch64 (arsitektur: $ARCH)."; exit 1 ;;
esac

URL="$(curl -fsSL https://api.github.com/repos/php/frankenphp/releases/latest \
    | grep '"browser_download_url"' | cut -d'"' -f4 | grep -E "/${ASET}$" | head -1)"

[ -n "$URL" ] || { echo "Rilis FrankenPHP untuk $ASET tidak ditemukan."; exit 1; }

curl -fsSL "$URL" -o /usr/local/bin/frankenphp
chmod +x /usr/local/bin/frankenphp
/usr/local/bin/frankenphp version

# ------------------------------------------------------------------- 6. folder
info "Menyiapkan folder aplikasi di $APP_DIR"
mkdir -p "$APP_DIR"/{releases,shared/storage,repo} /var/log/tukangsync /var/www/certbot

if [ -d "$APP_DIR/repo/.git" ]; then
    git -C "$APP_DIR/repo" fetch origin
    git -C "$APP_DIR/repo" checkout "$BRANCH"
    git -C "$APP_DIR/repo" reset --hard "origin/$BRANCH"
else
    rm -rf "$APP_DIR/repo"
    git clone --branch "$BRANCH" "$REPO" "$APP_DIR/repo"
fi

# storage/ berada di shared/ supaya bukti transfer, foto struk, dan log
# bertahan antar rilis. app/private = bukti transfer (TIDAK publik).
mkdir -p "$APP_DIR"/shared/storage/{app/private,app/public,framework/{cache/data,sessions,testing,views},logs,api-docs}

ENV_FILE="$APP_DIR/shared/.env"

setenv() {
    # setenv KUNCI NILAI -- ganti baris yang ada atau tambahkan di akhir.
    if grep -qE "^$1=" "$ENV_FILE"; then
        sed -i "s#^$1=.*#$1=$2#" "$ENV_FILE"
    else
        printf '%s=%s\n' "$1" "$2" >> "$ENV_FILE"
    fi
}

if [ ! -f "$ENV_FILE" ]; then
    cp "$APP_DIR/repo/.env.example" "$ENV_FILE"
    echo "  shared/.env dibuat dari .env.example."
fi

# Nilai produksi diisi otomatis; sisanya (MAIL_*, dll.) bisa diubah manual.
setenv APP_ENV production
setenv APP_DEBUG false
setenv APP_URL "https://$DOMAIN"
setenv LOG_LEVEL error
setenv DB_CONNECTION pgsql
setenv DB_HOST 127.0.0.1
setenv DB_PORT 5432
setenv DB_DATABASE "$DB_NAME"
setenv DB_USERNAME "$DB_USER"
setenv DB_PASSWORD "$SANDI"
setenv CACHE_STORE redis
setenv SESSION_DRIVER redis
setenv QUEUE_CONNECTION redis
setenv REDIS_CLIENT phpredis
setenv SESSION_SECURE_COOKIE true
setenv BROADCAST_CONNECTION log
# Octane hanya menerima koneksi dari nginx lokal; tanpa ini IP pengguna
# terbaca 127.0.0.1 (rate limit jadi satu untuk semua orang) dan skema HTTPS
# tidak dikenali.
setenv TRUSTED_PROXIES 127.0.0.1
setenv L5_SWAGGER_CONST_HOST "https://$DOMAIN"
setenv L5_SWAGGER_GENERATE_ALWAYS false
# HSTS dinyalakan MANUAL setelah HTTPS terbukti jalan (lihat DEPLOYMENT.md).
setenv SECURITY_HSTS false

if ! grep -qE '^APP_KEY=base64:' "$ENV_FILE"; then
    setenv APP_KEY "base64:$(openssl rand -base64 32)"
    echo "  APP_KEY baru dibuat."
fi

chown -R www-data:www-data "$APP_DIR" /var/log/tukangsync
chmod -R 775 "$APP_DIR/shared/storage"
chmod 640 "$APP_DIR/shared/.env"

# ------------------------------------------------------------------ 7. systemd
info "Memasang unit systemd"
cp "$APP_DIR"/repo/deploy/systemd/tukangsync-*.service /etc/systemd/system/
systemctl daemon-reload

# ------------------------------------------------------------------- 8. nginx
# Tahap 1: blok HTTP sementara agar certbot bisa memverifikasi domain.
# Sertifikat lama/salah yang sekarang terpasang di domain ini tidak dipakai.
info "Menerbitkan sertifikat TLS untuk $DOMAIN"
DOMAINS=(-d "$DOMAIN")
[ "$WITH_WWW" = "1" ] && DOMAINS+=(-d "www.$DOMAIN")

cat > /etc/nginx/sites-available/tukangsync-acme.conf <<NGINX
server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN www.$DOMAIN;
    location /.well-known/acme-challenge/ { root /var/www/certbot; }
    location / { return 404; }
}
NGINX

# Situs lain yang memakai server_name yang sama akan bentrok; dinonaktifkan.
for f in /etc/nginx/sites-enabled/*; do
    [ -e "$f" ] || continue
    if grep -qE "server_name[^;]*\b${DOMAIN//./\\.}\b" "$f" 2>/dev/null && [ "$(basename "$f")" != "tukangsync.conf" ]; then
        echo "  menonaktifkan konfigurasi lama yang memakai $DOMAIN: $f"
        rm -f "$f"
    fi
done
rm -f /etc/nginx/sites-enabled/default
ln -sfn /etc/nginx/sites-available/tukangsync-acme.conf /etc/nginx/sites-enabled/tukangsync-acme.conf
nginx -t && systemctl reload nginx

certbot certonly --webroot -w /var/www/certbot "${DOMAINS[@]}" \
    --email "$EMAIL" --agree-tos --non-interactive --keep-until-expiring

# File pendukung TLS yang dirujuk konfigurasi nginx (dibuat oleh plugin nginx
# certbot; dibuat manual di sini bila belum ada).
if [ ! -f /etc/letsencrypt/options-ssl-nginx.conf ]; then
    curl -fsSL https://raw.githubusercontent.com/certbot/certbot/master/certbot-nginx/certbot_nginx/_internal/tls_configs/options-ssl-nginx.conf \
        -o /etc/letsencrypt/options-ssl-nginx.conf
fi
[ -f /etc/letsencrypt/ssl-dhparams.pem ] || openssl dhparam -out /etc/letsencrypt/ssl-dhparams.pem 2048

# Tahap 2: konfigurasi lengkap.
info "Memasang konfigurasi nginx aplikasi"
sed "s/transaksi\.dormansyah\.com/${DOMAIN}/g" \
    "$APP_DIR/repo/deploy/nginx/transaksi.dormansyah.com.conf" \
    > /etc/nginx/sites-available/tukangsync.conf
rm -f /etc/nginx/sites-enabled/tukangsync-acme.conf
ln -sfn /etc/nginx/sites-available/tukangsync.conf /etc/nginx/sites-enabled/tukangsync.conf
nginx -t && systemctl reload nginx

# Pembaruan sertifikat otomatis + reload nginx.
cat > /etc/letsencrypt/renewal-hooks/deploy/reload-nginx.sh <<'HOOK'
#!/bin/sh
systemctl reload nginx
HOOK
chmod +x /etc/letsencrypt/renewal-hooks/deploy/reload-nginx.sh

cat <<PESAN

================================================================
 Server siap. Langkah berikutnya (lihat docs/DEPLOYMENT.md):

 1. Periksa $APP_DIR/shared/.env
      Nilai produksi, kredensial database, dan APP_KEY sudah diisi
      otomatis. Lengkapi MAIL_* bila ingin email reset sandi.

 2. (dilewati: APP_KEY sudah dibuat)

 3. Deploy pertama:
      sudo -u www-data $APP_DIR/repo/deploy/deploy.sh

 4. Isi data awal (SEKALI, tanpa data demo):
      cd $APP_DIR/current
      sudo -u www-data php artisan db:seed --class=RolePermissionSeeder --force
      sudo -u www-data php artisan db:seed --class=SuperAdminSeeder --force
      sudo -u www-data php artisan db:seed --class=SaasSeeder --force

 5. Nyalakan layanan:
      systemctl enable --now tukangsync-octane tukangsync-queue tukangsync-scheduler
================================================================
PESAN
