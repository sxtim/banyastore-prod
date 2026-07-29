#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${APP_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]:-$PWD/release-backup.sh}")" && pwd)}"
BACKUP_ROOT="${BACKUP_ROOT:-/var/www/backups/banyastore}"
DB_BACKUP_SCRIPT="${DB_BACKUP_SCRIPT:-$APP_DIR/db-backup.sh}"

usage() {
    cat <<'EOF'
Usage: ./release-backup.sh

Creates a restorable release snapshot:
  - MySQL database dump
  - storage/app/public archive
  - current Git commit
  - SHA-256 checksums

Environment:
  APP_DIR=/path/to/app
  BACKUP_ROOT=/path/to/backups
  DB_BACKUP_SCRIPT=/path/to/db-backup.sh
  ALLOW_ACTIVE_OPERATIONS=1
  MANAGE_MAINTENANCE=1
EOF
}

if [[ "${1:-}" == "--help" || "${1:-}" == "-h" ]]; then
    usage
    exit 0
fi

cd "$APP_DIR"

for required in artisan vendor/autoload.php storage/app/public "$DB_BACKUP_SCRIPT"; do
    if [ ! -e "$required" ]; then
        echo "Release backup stopped: required path is missing: $required"
        exit 1
    fi
done

for command in php git tar gzip sha256sum flock; do
    if ! command -v "$command" >/dev/null 2>&1; then
        echo "Release backup stopped: command is unavailable: $command"
        exit 1
    fi
done

mkdir -p "$BACKUP_ROOT"
chmod 700 "$BACKUP_ROOT" 2>/dev/null || true

exec 9>"$BACKUP_ROOT/.release-backup.lock"
if ! flock -n 9; then
    echo "Release backup stopped: another backup is already running."
    exit 1
fi

mapfile -t OPERATION_COUNTS < <(
php <<'PHP'
<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$activeRuns = Illuminate\Support\Facades\Schema::hasTable('feed_import_runs')
    ? Illuminate\Support\Facades\DB::table('feed_import_runs')
        ->whereIn('status', ['preparing', 'running'])
        ->count()
    : 0;
$queuedFeedJobs = Illuminate\Support\Facades\Schema::hasTable('jobs')
    ? Illuminate\Support\Facades\DB::table('jobs')
        ->where('queue', 'feed-imports')
        ->count()
    : 0;

echo $activeRuns . PHP_EOL;
echo $queuedFeedJobs . PHP_EOL;
PHP
)

ACTIVE_RUNS="${OPERATION_COUNTS[0]:-0}"
QUEUED_FEED_JOBS="${OPERATION_COUNTS[1]:-0}"
if [ "${ALLOW_ACTIVE_OPERATIONS:-0}" != "1" ] && {
    [ "$ACTIVE_RUNS" != "0" ] || [ "$QUEUED_FEED_JOBS" != "0" ];
}; then
    echo "Release backup stopped: feed operation is active or queued."
    echo "Active runs: $ACTIVE_RUNS; queued feed jobs: $QUEUED_FEED_JOBS."
    exit 1
fi

TIMESTAMP="$(date +%Y%m%d_%H%M%S)"
COMMIT="$(git rev-parse HEAD 2>/dev/null || true)"
SHORT_COMMIT="$(git rev-parse --short HEAD 2>/dev/null || echo no-git)"
BACKUP_NAME="release_${TIMESTAMP}_${SHORT_COMMIT}"
FINAL_DIR="$BACKUP_ROOT/$BACKUP_NAME"
TEMP_DIR="$BACKUP_ROOT/.${BACKUP_NAME}.tmp"
SITE_IS_DOWN=0

if [ -e "$FINAL_DIR" ] || [ -e "$TEMP_DIR" ]; then
    echo "Release backup stopped: backup path already exists."
    exit 1
fi

mkdir -p "$TEMP_DIR"
chmod 700 "$TEMP_DIR"
backup_cleanup() {
    rm -rf "$TEMP_DIR"
    if [ "$SITE_IS_DOWN" = "1" ]; then
        php artisan up >/dev/null 2>&1 || true
    fi
}
trap backup_cleanup EXIT

if [ "${MANAGE_MAINTENANCE:-0}" = "1" ]; then
    echo "Enable maintenance mode"
    php artisan down --retry=60
    SITE_IS_DOWN=1
fi

echo "Create database backup"
BACKUP_DIR="$TEMP_DIR" APP_DIR="$APP_DIR" bash "$DB_BACKUP_SCRIPT"
DB_FILE="$(find "$TEMP_DIR" -maxdepth 1 -type f -name 'banyastore_db_*.sql.gz' -print -quit)"
if [ -z "$DB_FILE" ]; then
    echo "Release backup stopped: database dump was not created."
    exit 1
fi

echo "Archive public storage"
tar -C "$APP_DIR/storage/app" -czf "$TEMP_DIR/storage-app-public.tar.gz" public
tar -tzf "$TEMP_DIR/storage-app-public.tar.gz" >/dev/null

cat > "$TEMP_DIR/MANIFEST" <<EOF
format=1
created_at=$(date --iso-8601=seconds)
git_commit=$COMMIT
database_file=$(basename "$DB_FILE")
storage_file=storage-app-public.tar.gz
app_dir=$APP_DIR
EOF

(
    cd "$TEMP_DIR"
    sha256sum "$(basename "$DB_FILE")" storage-app-public.tar.gz MANIFEST > SHA256SUMS
)

chmod 600 "$TEMP_DIR"/*
mv "$TEMP_DIR" "$FINAL_DIR"
ln -sfn "$BACKUP_NAME" "$BACKUP_ROOT/latest"

if [ "$SITE_IS_DOWN" = "1" ]; then
    php artisan up
    SITE_IS_DOWN=0
fi

trap - EXIT

echo "Release backup completed: $FINAL_DIR"
