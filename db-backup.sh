#!/usr/bin/env bash
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKUP_DIR="${BACKUP_DIR:-/var/www/backups/banyastore}"

if [[ "${1:-}" == "--help" || "${1:-}" == "-h" ]]; then
    cat <<'EOF'
Usage: ./db-backup.sh

Environment:
  BACKUP_DIR=/custom/path   Override the backup target directory
EOF
    exit 0
fi

cd "$APP_DIR"

if [ ! -f artisan ] || [ ! -d vendor ]; then
    echo "Backup stopped: artisan or vendor directory is missing."
    exit 1
fi

mapfile -t DB_CONFIG < <(
php <<'PHP'
<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$default = config('database.default');
$config = config("database.connections.$default");

foreach (['driver', 'host', 'port', 'database', 'username', 'password', 'unix_socket'] as $key) {
    echo ($config[$key] ?? '') . PHP_EOL;
}
PHP
)

DB_DRIVER="${DB_CONFIG[0]:-}"
DB_HOST="${DB_CONFIG[1]:-127.0.0.1}"
DB_PORT="${DB_CONFIG[2]:-3306}"
DB_DATABASE="${DB_CONFIG[3]:-}"
DB_USERNAME="${DB_CONFIG[4]:-}"
DB_PASSWORD="${DB_CONFIG[5]:-}"
DB_SOCKET="${DB_CONFIG[6]:-}"

if [ "$DB_DRIVER" != "mysql" ]; then
    echo "Backup stopped: expected mysql driver, got '${DB_DRIVER:-unknown}'."
    exit 1
fi

if [ -z "$DB_DATABASE" ] || [ -z "$DB_USERNAME" ]; then
    echo "Backup stopped: database connection settings are incomplete."
    exit 1
fi

mkdir -p "$BACKUP_DIR"
chmod 700 "$(dirname "$BACKUP_DIR")" "$BACKUP_DIR" 2>/dev/null || true

TIMESTAMP="$(date +%Y%m%d_%H%M%S)"
OUT_FILE="$BACKUP_DIR/banyastore_db_${TIMESTAMP}.sql.gz"
TMP_FILE="${OUT_FILE}.tmp"
trap 'rm -f "$TMP_FILE"' EXIT

MYSQL_ARGS=(
    --no-tablespaces
    --single-transaction
    --quick
    --default-character-set=utf8mb4
    -u "$DB_USERNAME"
)

if [ -n "$DB_SOCKET" ]; then
    MYSQL_ARGS+=(--socket="$DB_SOCKET")
else
    MYSQL_ARGS+=(-h "$DB_HOST" -P "$DB_PORT")
fi

if [ -n "$DB_PASSWORD" ]; then
    MYSQL_PWD="$DB_PASSWORD" mysqldump "${MYSQL_ARGS[@]}" "$DB_DATABASE" | gzip > "$TMP_FILE"
else
    mysqldump "${MYSQL_ARGS[@]}" "$DB_DATABASE" | gzip > "$TMP_FILE"
fi

gzip -t "$TMP_FILE"
chmod 600 "$TMP_FILE"
mv "$TMP_FILE" "$OUT_FILE"
trap - EXIT

echo "Backup completed: $OUT_FILE"
