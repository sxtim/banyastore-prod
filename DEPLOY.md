# Deploy

## Прод

Рабочая папка продакшена:

```bash
/var/www/html/banyastore
```

Git remote для продового деплоя:

```bash
git@github.com:sxtim/banyastore-prod.git
```

Основная ветка:

```bash
main
```

## Что деплоится через git

Через git деплоится:

- PHP-код и blade-шаблоны
- миграции и конфиги
- собранные frontend-ассеты:
  - `public/js/`
  - `public/css/`
  - `public/images/`
  - `public/mix-manifest.json`

Идея схемы: **локально собрать релиз целиком, закоммитить код вместе с готовыми ассетами и потом выкатить этот конкретный commit на прод**.

## Первый scripted deploy

Если `deploy.sh` и `db-backup.sh` еще не лежат на проде, сначала получить новые refs и запустить скрипты прямо из target commit:

```bash
cd /var/www/html/banyastore
git fetch origin main
git show origin/main:db-backup.sh | APP_DIR="$PWD" bash
git show origin/main:deploy.sh | APP_DIR="$PWD" RUN_MIGRATIONS=1 bash -s -- <commit-hash>
```

После того как файлы появились на проде, дальнейшие деплои выполнять уже через `deploy.sh`.

## Обычный деплой кода

### 1. Локально

Внести изменения, проверить их и запушить в GitHub:

```bash
git add ...
git commit -m "..."
git push origin main
```

### 2. Если менялся frontend

Перед коммитом собрать ассеты локально:

```bash
npm ci
npm run prod-all
```

После сборки добавить в commit обновлённые файлы из:

- `public/js/`
- `public/css/`
- `public/images/`
- `public/mix-manifest.json`

### 3. На проде

Обычный деплой:

```bash
cd /var/www/html/banyastore
./deploy.sh <commit-hash-or-tag>
```

Скрипт делает:

- `git fetch --all --tags --prune`
- проверку чистоты рабочего дерева, включая незатреканные файлы
- проверку, что target commit входит в `origin/main`
- проверку `composer.lock`
- проверку, что в target commit уже есть собранные frontend-ассеты
- `git reset --hard <commit>`
- `composer install`, только если это явно разрешено и зависимости поменялись
- `php artisan storage:link`, если ссылки нет
- очистку и прогрев Laravel-кэша

Миграции по умолчанию не запускаются.

## Деплой с Composer

Если изменился `composer.lock` или на проде отсутствует `vendor/autoload.php`, скрипт остановится до `reset`.

После проверки окружения запускать так:

```bash
cd /var/www/html/banyastore
RUN_COMPOSER=1 ./deploy.sh <commit-hash-or-tag>
```

## Деплой с миграциями

Если в релизе есть миграции:

```bash
cd /var/www/html/banyastore
./db-backup.sh
RUN_MIGRATIONS=1 ./deploy.sh <commit-hash-or-tag>
```

## Бэкап базы

Скрипт бэкапа:

```bash
cd /var/www/html/banyastore
./db-backup.sh
```

По умолчанию дампы складываются в:

```bash
/var/www/backups/banyastore
```

Путь можно переопределить:

```bash
BACKUP_DIR=/custom/path ./db-backup.sh
```

Формат файла:

```bash
banyastore_db_YYYYMMDD_HHMMSS.sql.gz
```

Файловые бэкапы VPS доступны через панель Beget отдельно от этих SQL-дампов.

## Проверка состояния прода

Перед или после деплоя полезно проверить:

```bash
cd /var/www/html/banyastore
git remote -v
git status -sb
git log --oneline -3
```

Нормальное состояние:

```bash
## main...origin/main
```

## Откат

Откат кода:

```bash
cd /var/www/html/banyastore
./deploy.sh <previous-commit-hash>
```

Если откатывались миграции или данные, сначала проверить свежий SQL-бэкап и только потом выполнять ручной SQL-откат или `php artisan migrate:rollback`.

## Важные замечания

- Не править код руками прямо на проде, если дальше планируется деплой через `deploy.sh`.
- Контент, товары, заказы и другие данные в БД можно создавать через админку как обычно.
- Если на проде появились локальные изменения, сначала разобраться с `git status`, а потом деплоить.
- Для обычного PHP-only релиза не нужен `RUN_COMPOSER=1` и не нужен `RUN_MIGRATIONS=1`.
- Для релиза с frontend-изменениями ассеты нужно сначала собрать локально и включить в тот же commit, который потом уедет на прод.
