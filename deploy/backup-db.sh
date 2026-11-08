#!/usr/bin/env bash
#
# Cadangan harian database Tukangsync. Pasang di cron root:
#
#   0 1 * * * /var/www/tukangsync/repo/deploy/backup-db.sh >> /var/log/tukangsync/backup.log 2>&1
#
# Menyimpan 14 cadangan terakhir di /var/backups/tukangsync.

set -euo pipefail

DB_NAME="${DB_NAME:-tukangsync}"
DIR="${DIR:-/var/backups/tukangsync}"
KEEP="${KEEP:-14}"

mkdir -p "$DIR"
chmod 700 "$DIR"

FILE="$DIR/${DB_NAME}-$(date +%Y%m%d-%H%M%S).sql.gz"
sudo -u postgres pg_dump --no-owner "$DB_NAME" | gzip -9 > "$FILE"
chmod 600 "$FILE"

# Bukti transfer & foto struk ikut dicadangkan (berkas, bukan database).
tar -czf "$DIR/storage-$(date +%Y%m%d).tar.gz" -C /var/www/tukangsync/shared storage/app 2>/dev/null || true

ls -1t "$DIR"/"${DB_NAME}"-*.sql.gz | tail -n "+$((KEEP + 1))" | xargs -r rm -f
ls -1t "$DIR"/storage-*.tar.gz 2>/dev/null | tail -n "+$((KEEP + 1))" | xargs -r rm -f

echo "$(date '+%F %T') cadangan: $FILE"
