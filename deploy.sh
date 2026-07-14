#!/usr/bin/env bash
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TARGET="${1:-${DEPLOY_REF:-}}"

cd "$APP_DIR"

usage() {
    cat <<'EOF'
Usage: ./deploy.sh <commit-hash-or-tag>

Environment flags:
  FIRST_GIT_DEPLOY=1   Allow first deploy when HEAD is not checked out yet
  RUN_COMPOSER=1       Run composer install when composer.lock changed or vendor is missing
  RUN_MIGRATIONS=1     Run php artisan migrate --force after code deploy
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
    if ! git diff --quiet || ! git diff --cached --quiet; then
        echo "Deploy stopped: working tree has local changes."
        git status --short
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

git reset --hard "$COMMIT"

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

echo "Deploy completed"
