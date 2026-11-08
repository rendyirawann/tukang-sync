#!/usr/bin/env bash
#
# Deploy Tukangsync. Dijalankan DI SERVER:
#
#   sudo -u www-data /var/www/tukang-sync/repo/deploy/deploy.sh
#
# Tata letak rilis bersimbol tautan -- pergantian versi terjadi dalam satu
# operasi dan bisa dikembalikan seketika:
#
#   /var/www/tukang-sync/
#   ├── repo/              klon git
#   ├── releases/2026…/    satu folder per rilis
#   ├── shared/            BERTAHAN antar rilis
#   │   ├── .env
#   │   └── storage/       bukti transfer, foto struk, log
#   └── current -> releases/2026…

set -euo pipefail

APP_DIR="${APP_DIR:-/var/www/tukang-sync}"
REPO_DIR="$APP_DIR/repo"
RELEASES_DIR="$APP_DIR/releases"
SHARED_DIR="$APP_DIR/shared"
CURRENT_LINK="$APP_DIR/current"
BRANCH="${BRANCH:-main}"
KEEP_RELEASES="${KEEP_RELEASES:-5}"
PHP="${PHP:-/usr/bin/php}"
PORT="${PORT:-8010}"

RELEASE="$RELEASES_DIR/$(date +%Y%m%d%H%M%S)"

info() { printf '\n\033[1;34m==>\033[0m %s\n' "$1"; }
gagal() { printf '\n\033[1;31mGAGAL:\033[0m %s\n' "$1" >&2; exit 1; }

[ -d "$REPO_DIR/.git" ] || gagal "Repo belum ada di $REPO_DIR. Jalankan deploy/setup-server.sh dulu."
[ -f "$SHARED_DIR/.env" ] || gagal ".env belum ada di $SHARED_DIR."
grep -qE '^APP_KEY=base64:' "$SHARED_DIR/.env" || gagal "APP_KEY di shared/.env masih kosong."

# ---------------------------------------------------------------- 1. ambil kode
info "Mengambil kode terbaru ($BRANCH)"
git -C "$REPO_DIR" fetch --prune origin
git -C "$REPO_DIR" checkout -q "$BRANCH" 2>/dev/null || git -C "$REPO_DIR" checkout -q -b "$BRANCH" "origin/$BRANCH"
git -C "$REPO_DIR" reset --hard "origin/$BRANCH"
SHA_PENDEK="$(git -C "$REPO_DIR" rev-parse --short HEAD)"
info "Rilis $RELEASE (commit $SHA_PENDEK)"

mkdir -p "$RELEASES_DIR"
cp -a "$REPO_DIR" "$RELEASE"
rm -rf "$RELEASE/.git"

# ------------------------------------------------- 2. tautkan yang harus tetap
info "Menautkan .env dan storage dari shared/"
rm -rf "$RELEASE/storage"
ln -s "$SHARED_DIR/storage" "$RELEASE/storage"
ln -s "$SHARED_DIR/.env" "$RELEASE/.env"

# --------------------------------------------------------------- 3. dependensi
# Aset tampilan (Metronic) sudah ada di public/assets; tidak perlu npm build.
info "Memasang dependensi (tanpa dev)"
cd "$RELEASE"
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# --------------------------------------------------------------------- 4. Octane
# Setiap rilis: menulis public/frankenphp-worker.php (dan config/octane.php
# bila belum ada). Biner FrankenPHP sudah di PATH, jadi tidak mengunduh ulang.
info "Menyiapkan worker Octane"
$PHP artisan octane:install --server=frankenphp --no-interaction

# ------------------------------------------------------------------ 5. migrasi
# Sebelum current dialihkan, supaya skema siap ketika kode baru melayani.
info "Menjalankan migrasi"
$PHP artisan migrate --force

# --------------------------------------------------------------------- 6. cache
# config:cache WAJIB untuk Octane: env() di luar config/ bernilai null tanpanya.
info "Membangun cache konfigurasi, rute, view, event"
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache
$PHP artisan event:cache

info "Membangun dokumentasi Swagger"
$PHP artisan l5-swagger:generate

# Tautan public/storage (avatar). Bukti transfer ada di storage/app/private
# dan TIDAK ikut terjangkau lewat tautan ini.
$PHP artisan storage:link || true

# ------------------------------------------------------------------ 7. alihkan
info "Mengalihkan current -> rilis baru"
ln -sfn "$RELEASE" "${CURRENT_LINK}.tmp"
mv -Tf "${CURRENT_LINK}.tmp" "$CURRENT_LINK"

# ------------------------------------------------------------- 8. muat ulang
# reload, bukan restart: permintaan yang sedang berjalan tidak terputus.
info "Memuat ulang Octane dan pekerja antrean"
$PHP artisan octane:reload || sudo systemctl restart tukangsync-octane || true
$PHP artisan queue:restart

# ------------------------------------------------------------ 9. bersih-bersih
info "Menyisakan $KEEP_RELEASES rilis terakhir"
cd "$RELEASES_DIR"
AKTIF="$(readlink -f "$CURRENT_LINK" 2>/dev/null || echo '')"

ls -1dt */ 2>/dev/null | tail -n "+$((KEEP_RELEASES + 1))" | while read -r LAMA; do
    NAMA="${LAMA%/}"
    case "$NAMA" in
        [0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]) ;;
        *) printf '    dilewati (bukan folder rilis): %s\n' "$NAMA"; continue ;;
    esac
    if [ -n "$AKTIF" ] && [ "$(readlink -f "$NAMA")" = "$AKTIF" ]; then
        printf '    dilewati (sedang aktif): %s\n' "$NAMA"
        continue
    fi
    rm -rf -- "${RELEASES_DIR:?}/$NAMA"
    printf '    dihapus: %s\n' "$NAMA"
done

# ----------------------------------------------------------------- 10. periksa
info "Memeriksa kesehatan"
sleep 2
KODE="$(curl -s -o /dev/null -w '%{http_code}' "http://127.0.0.1:${PORT}/api/health" || echo 000)"

if [ "$KODE" != "200" ]; then
    gagal "Health check membalas $KODE. Rilis sudah aktif -- periksa log:
  journalctl -u tukangsync-octane -n 50
  tail -n 50 $SHARED_DIR/storage/logs/laravel.log
Kembalikan ke rilis sebelumnya: lihat 'Rollback' di docs/DEPLOYMENT.md"
fi

printf '\n\033[1;32mSelesai.\033[0m Rilis %s (commit %s) aktif.\n' "$(basename "$RELEASE")" "$SHA_PENDEK"
