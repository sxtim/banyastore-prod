#!/usr/bin/env bash
set -euo pipefail

APP_DIR="${APP_DIR:-$(cd "$(dirname "${BASH_SOURCE[0]:-$PWD/deploy.sh}")" && pwd)}"
TARGET="${1:-${DEPLOY_REF:-}}"

cd "$APP_DIR"

usage() {
    cat <<'EOF'
Usage: ./deploy.sh <commit-hash-or-tag>

Environment flags:
  FIRST_GIT_DEPLOY=1   Allow first deploy when HEAD is not checked out yet
  RUN_COMPOSER=1       Run composer install when composer.lock changed or vendor is missing
  RUN_MIGRATIONS=1     Run php artisan migrate --force after code deploy
  SKIP_RELEASE_BACKUP=1
                       Skip the automatic database and public-files backup
EOF
}

if [[ "${1:-}" == "--help" || "${1:-}" == "-h" ]]; then
    usage
    exit 0
fi

if [ -z "$TARGET" ]; then
    usage
    exit 1
fi

if [ ! -d .git ]; then
    echo "Deploy stopped: $APP_DIR is not a git repository."
    echo "Set up git deploy on the server first, then run this script again."
    exit 1
fi

HAS_HEAD=0
if git rev-parse --verify HEAD >/dev/null 2>&1; then
    HAS_HEAD=1
fi

if [ "$HAS_HEAD" = "1" ]; then
    if [ -n "$(git status --porcelain --untracked-files=normal)" ]; then
        echo "Deploy stopped: working tree has local or untracked changes."
        git status --short --untracked-files=normal
        exit 1
    fi
elif [ "${FIRST_GIT_DEPLOY:-0}" != "1" ]; then
    echo "Deploy stopped: this repository has no checked out commit yet."
    echo "Run the first deploy explicitly: FIRST_GIT_DEPLOY=1 ./deploy.sh <commit-hash-or-tag>"
    exit 1
fi

PREVIOUS_COMMIT="$(git rev-parse --short HEAD 2>/dev/null || true)"

echo "Fetch git refs"
git fetch --all --tags --prune

COMMIT="$(git rev-parse --verify "$TARGET^{commit}")"
echo "Deploy commit: $COMMIT"
if [ -n "$PREVIOUS_COMMIT" ]; then
    echo "Previous commit: $PREVIOUS_COMMIT"
fi

if ! git merge-base --is-ancestor "$COMMIT" origin/main; then
    echo "Deploy stopped: target commit is not part of origin/main."
    exit 1
fi

CURRENT_LOCK_HASH=""
if [ -f composer.lock ]; then
    CURRENT_LOCK_HASH="$(sha256sum composer.lock | awk '{print $1}')"
fi

TARGET_LOCK_HASH=""
if git cat-file -e "$COMMIT:composer.lock" 2>/dev/null; then
    TARGET_LOCK_HASH="$(git show "$COMMIT:composer.lock" | sha256sum | awk '{print $1}')"
fi

INSTALL_DEPENDENCIES=0
if [ ! -f vendor/autoload.php ] || [ "$CURRENT_LOCK_HASH" != "$TARGET_LOCK_HASH" ]; then
    if [ "${RUN_COMPOSER:-0}" != "1" ]; then
        echo "Deploy stopped before reset: Composer dependencies differ or vendor is missing."
        echo "Verify Composer is available on the server, then run with RUN_COMPOSER=1."
        exit 1
    fi

    INSTALL_DEPENDENCIES=1
fi

REQUIRED_TARGET_ASSETS=(
    public/mix-manifest.json
    public/js/manifest.js
    public/js/vendor.js
    public/js/app.js
    public/js/backend/manifest.js
    public/js/backend/vendor.js
    public/js/backend/app.js
    public/css/main.css
)

MISSING_TARGET_ASSETS=()
for asset in "${REQUIRED_TARGET_ASSETS[@]}"; do
    if ! git cat-file -e "$COMMIT:$asset" 2>/dev/null; then
        MISSING_TARGET_ASSETS+=("$asset")
    fi
done

if [ "${#MISSING_TARGET_ASSETS[@]}" -gt 0 ]; then
    echo "Deploy stopped before reset: target commit is missing compiled frontend assets."
    printf ' - %s\n' "${MISSING_TARGET_ASSETS[@]}"
    echo "Run npm ci && npm run prod-all locally, commit the updated public assets, then deploy again."
    exit 1
fi

SITE_IS_DOWN=0
DEPLOY_MUTATED=0
RELEASE_BACKUP_SCRIPT=""
DB_BACKUP_SCRIPT=""
deploy_cleanup() {
    rm -f "$RELEASE_BACKUP_SCRIPT" "$DB_BACKUP_SCRIPT"
    if [ "$SITE_IS_DOWN" = "1" ]; then
        if [ "$DEPLOY_MUTATED" = "0" ]; then
            php artisan up >/dev/null 2>&1 || true
        else
            echo "Deploy failed after changing the release. The site remains in maintenance mode."
            echo "Restore the printed release backup before enabling the site."
        fi
    fi
}
trap deploy_cleanup EXIT

echo "Enable maintenance mode"
php artisan down --retry=60
SITE_IS_DOWN=1

if [ "${SKIP_RELEASE_BACKUP:-0}" != "1" ]; then
    echo "Create release backup before changing code"
    RELEASE_BACKUP_SCRIPT="$(mktemp)"
    DB_BACKUP_SCRIPT="$(mktemp)"
    if git cat-file -e "$COMMIT:release-backup.sh" 2>/dev/null; then
        git show "$COMMIT:release-backup.sh" > "$RELEASE_BACKUP_SCRIPT"
    else
        cp "$APP_DIR/release-backup.sh" "$RELEASE_BACKUP_SCRIPT"
    fi
    if git cat-file -e "$COMMIT:db-backup.sh" 2>/dev/null; then
        git show "$COMMIT:db-backup.sh" > "$DB_BACKUP_SCRIPT"
    else
        cp "$APP_DIR/db-backup.sh" "$DB_BACKUP_SCRIPT"
    fi
    APP_DIR="$APP_DIR" DB_BACKUP_SCRIPT="$DB_BACKUP_SCRIPT" bash "$RELEASE_BACKUP_SCRIPT"
    rm -f "$RELEASE_BACKUP_SCRIPT" "$DB_BACKUP_SCRIPT"
    RELEASE_BACKUP_SCRIPT=""
    DB_BACKUP_SCRIPT=""
else
    echo "WARNING: release backup explicitly skipped"
fi

git reset --hard "$COMMIT"
DEPLOY_MUTATED=1

if [ "$INSTALL_DEPENDENCIES" = "1" ]; then
    echo "Install PHP dependencies"
    composer install --no-dev --optimize-autoloader --no-interaction
else
    echo "Skip Composer: composer.lock is unchanged and vendor is present"
fi

MISSING_ASSETS=()
for asset in "${REQUIRED_TARGET_ASSETS[@]}"; do
    if [ ! -f "$asset" ]; then
        MISSING_ASSETS+=("$asset")
    fi
done

if [ "${#MISSING_ASSETS[@]}" -gt 0 ]; then
    echo "Deploy stopped: required frontend assets are missing."
    printf ' - %s\n' "${MISSING_ASSETS[@]}"
    echo "Rebuild and commit the public assets, then run deploy again."
    exit 1
fi

if [ -e public/storage ] && [ ! -L public/storage ]; then
    echo "Deploy stopped: public/storage exists and is not a symlink."
    exit 1
fi

if [ ! -L public/storage ]; then
    echo "Create storage symlink"
    php artisan storage:link
fi

if [ "${RUN_MIGRATIONS:-0}" = "1" ]; then
    echo "Run migrations"
    php artisan migrate --force
else
    echo "Skip migrations. Run with RUN_MIGRATIONS=1 when the deploy includes DB migrations."
fi

echo "Clear and warm Laravel cache"
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart

echo "Restore Laravel writable directory permissions"
chown -R www-data:www-data storage bootstrap/cache
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;

php artisan up
SITE_IS_DOWN=0
trap - EXIT

echo "Deploy completed"
