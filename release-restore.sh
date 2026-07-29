#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${APP_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]:-$PWD/release-restore.sh}")" && pwd)}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/www/backups/banyastore}"
BACKUP_PATH="${1:-}"

usage() {
    cat <<'EOF'
Usage:
  ./release-restore.sh --verify /path/to/release_backup
  CONFIRM_RESTORE=YES ./release-restore.sh /path/to/release_backup

The restore command:
  - verifies SHA-256 checksums
  - creates an emergency backup of the current state
  - restores the recorded Git commit
  - restores MySQL and storage/app/public
  - rebuilds Laravel caches and restarts queue workers

Environment:
  APP_DIR=/path/to/app
  BACKUP_ROOT=/path/to/backups
  CONFIRM_RESTORE=YES
  SKIP_SAFETY_BACKUP=1
  RUN_COMPOSER=0
EOF
}

VERIFY_ONLY=0
if [[ "${1:-}" == "--help" || "${1:-}" == "-h" ]]; then
    usage
    exit 0
fi
if [[ "${1:-}" == "--verify" ]]; then
    VERIFY_ONLY=1
    BACKUP_PATH="${2:-}"
fi
if [ -z "$BACKUP_PATH" ]; then
    usage
    exit 1
fi

cd "$APP_DIR"

BACKUP_PATH="$(realpath "$BACKUP_PATH")"
for required in MANIFEST SHA256SUMS; do
    if [ ! -f "$BACKUP_PATH/$required" ]; then
        echo "Restore stopped: $required is missing in $BACKUP_PATH."
        exit 1
    fi
done

manifest_value() {
    local key="$1"
    sed -n "s/^${key}=//p" "$BACKUP_PATH/MANIFEST" | head -n 1
}

FORMAT="$(manifest_value format)"
COMMIT="$(manifest_value git_commit)"
DB_FILE="$(manifest_value database_file)"
STORAGE_FILE="$(manifest_value storage_file)"

if [ "$FORMAT" != "1" ] || [ -z "$COMMIT" ] || [ -z "$DB_FILE" ] || [ -z "$STORAGE_FILE" ]; then
    echo "Restore stopped: backup manifest is invalid."
    exit 1
fi

echo "Verify release backup"
(
    cd "$BACKUP_PATH"
    sha256sum -c SHA256SUMS
)
gzip -t "$BACKUP_PATH/$DB_FILE"
tar -tzf "$BACKUP_PATH/$STORAGE_FILE" >/dev/null
git cat-file -e "$COMMIT^{commit}"

if [ "$VERIFY_ONLY" = "1" ]; then
    echo "Release backup is valid: $BACKUP_PATH"
    exit 0
fi

if [ "${CONFIRM_RESTORE:-}" != "YES" ]; then
    echo "Restore stopped: set CONFIRM_RESTORE=YES after checking the backup path."
    exit 1
fi

if [ -n "$(git status --porcelain --untracked-files=normal)" ]; then
    echo "Restore stopped: working tree has local or untracked changes."
    git status --short --untracked-files=normal
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

if [ "$DB_DRIVER" != "mysql" ] || [ -z "$DB_DATABASE" ] || [ -z "$DB_USERNAME" ]; then
    echo "Restore stopped: MySQL connection settings are incomplete."
    exit 1
fi

RESTORE_TEMP="$(mktemp -d "$APP_DIR/storage/app/.restore-public.XXXXXX")"
OLD_PUBLIC="$APP_DIR/storage/app/.public-before-restore-$(date +%Y%m%d_%H%M%S)"
SITE_IS_DOWN=0
RESTORE_MUTATED=0
restore_cleanup() {
    rm -rf "$RESTORE_TEMP"
    if [ "$SITE_IS_DOWN" = "1" ]; then
        if [ "$RESTORE_MUTATED" = "0" ]; then
            php artisan up >/dev/null 2>&1 || true
        else
            echo "Restore failed after changing the release. The site remains in maintenance mode."
            echo "Fix the error or rerun the restore before enabling the site."
        fi
    fi
}
trap restore_cleanup EXIT

echo "Enable maintenance mode"
php artisan down --retry=60
SITE_IS_DOWN=1

if [ "${SKIP_SAFETY_BACKUP:-0}" != "1" ]; then
    echo "Create emergency backup of the current state"
    APP_DIR="$APP_DIR" BACKUP_ROOT="$BACKUP_ROOT" "$APP_DIR/release-backup.sh"
fi

echo "Restore code commit: $COMMIT"
git reset --hard "$COMMIT"
RESTORE_MUTATED=1

if [ "${RUN_COMPOSER:-1}" = "1" ]; then
    composer install --no-dev --optimize-autoloader --no-interaction
fi

echo "Prepare public storage"
tar -xzf "$BACKUP_PATH/$STORAGE_FILE" -C "$RESTORE_TEMP"
if [ ! -d "$RESTORE_TEMP/public" ]; then
    echo "Restore stopped: public storage directory is missing in archive."
    exit 1
fi

echo "Restore database"
MYSQL_ARGS=(
    --default-character-set=utf8mb4
    --binary-mode=1
    -u "$DB_USERNAME"
)
if [ -n "$DB_SOCKET" ]; then
    MYSQL_ARGS+=(--socket="$DB_SOCKET")
else
    MYSQL_ARGS+=(-h "$DB_HOST" -P "$DB_PORT")
fi

if [ -n "$DB_PASSWORD" ]; then
    MYSQL_PWD="$DB_PASSWORD" gunzip -c "$BACKUP_PATH/$DB_FILE" \
        | MYSQL_PWD="$DB_PASSWORD" mysql "${MYSQL_ARGS[@]}" "$DB_DATABASE"
else
    gunzip -c "$BACKUP_PATH/$DB_FILE" | mysql "${MYSQL_ARGS[@]}" "$DB_DATABASE"
fi

echo "Restore public storage"
mv "$APP_DIR/storage/app/public" "$OLD_PUBLIC"
mv "$RESTORE_TEMP/public" "$APP_DIR/storage/app/public"
rm -rf "$OLD_PUBLIC"

echo "Rebuild Laravel caches"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
php artisan up
SITE_IS_DOWN=0

trap - EXIT
rm -rf "$RESTORE_TEMP"

echo "Release restore completed from: $BACKUP_PATH"
