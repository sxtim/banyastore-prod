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
- перевод сайта в maintenance mode
- полный релизный бэкап базы и `storage/app/public`
- `git reset --hard <commit>`
- `composer install`, только если это явно разрешено и зависимости поменялись
- `php artisan storage:link`, если ссылки нет
- очистку и прогрев Laravel-кэша
- перезапуск Laravel queue worker
- вывод сайта из maintenance mode

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

## Релизный бэкап

Каждый `deploy.sh` до изменения кода автоматически создаёт полный снимок:

- SQL-дамп базы;
- архив `storage/app/public` со всеми публичными файлами;
- commit, который был установлен до деплоя;
- SHA-256 для проверки целостности.

По умолчанию снимки складываются в:

```bash
/var/www/backups/banyastore/release_YYYYMMDD_HHMMSS_COMMIT
```

Ссылка `/var/www/backups/banyastore/latest` указывает на последний успешно
созданный снимок.

Создать такой снимок отдельно, например непосредственно перед первым импортом:

```bash
cd /var/www/html/banyastore
MANAGE_MAINTENANCE=1 ./release-backup.sh
```

Проверить, что снимок читается:

```bash
./release-restore.sh --verify /var/www/backups/banyastore/latest
```

Автоматический релизный бэкап можно пропустить только в аварийной ситуации:

```bash
SKIP_RELEASE_BACKUP=1 ./deploy.sh <commit-hash-or-tag>
```

## Бэкап Только Базы

Для отдельного SQL-дампа:

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

Файловые бэкапы VPS доступны через панель Beget отдельно от этих снимков.

## Worker импорта товаров

Импорт фотографий и товаров выполняется через Laravel database queue. Пример
конфигурации Supervisor находится в:

```bash
deploy/supervisor/banyastore-worker.conf.example
```

На проде установить конфигурацию Supervisor, задать в `.env`:

```bash
QUEUE_CONNECTION=database
IRON_STEEL_FEED_URL=https://prometall.ru/tstore/yml/cf906418b8b8973e2d842ac7987a654f.yml
FEED_ALLOWED_HOSTS=prometall.ru
FEED_IMAGE_ALLOWED_HOSTS=static.tildacdn.com
```

и убедиться, что процесс `banyastore-feed-worker` запущен. Обычный
`deploy.sh` вызывает `php artisan queue:restart`, поэтому worker забирает новый
код без ручной перезагрузки.

Если поставщик перенесёт фид или фотографии на другой домен, сначала проверить
новый адрес, затем добавить его в соответствующий список через запятую. Импорт
намеренно не скачивает данные с неизвестных доменов.

После первой установки миграций один раз проверить и загрузить подтверждённые
сопоставления товаров:

```bash
cd /var/www/html/banyastore
php artisan feed:iron-steel:setup --dry-run
php artisan feed:iron-steel:setup
```

Сначала должна успешно завершиться команда с `--dry-run`. Она проверяет CSV и
существование указанных в нём ID товаров, но не меняет базу. Повторно выполнять
настройку нужно после изменения URL источника, маппинга категорий, свойств или
файла сопоставлений. Команда сохраняет прежний slug `iron-steel`, поэтому
существующие связи по `offer id` не теряются при переходе на полный фид
ProMetall.

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

## Откат Импорта

Если проблема только в применённом фиде, в админке открыть:

```text
Интернет-магазин → Импорт товаров → История
```

Кнопка `Откатить` возвращает состояние товаров перед последним импортом:

- цену, описание и свойства;
- старые фотографии;
- новые товары деактивирует и отсоединяет от фида.

Это основной и самый быстрый откат после импорта.

## Полный Откат Релиза

Если проблема в коде, миграциях или импортный откат не сработал, восстановить
полный релизный снимок:

```bash
cd /var/www/html/banyastore
./release-restore.sh --verify /var/www/backups/banyastore/release_...
CONFIRM_RESTORE=YES ./release-restore.sh /var/www/backups/banyastore/release_...
```

Перед разрушительным восстановлением скрипт автоматически создаёт ещё один
аварийный снимок текущего состояния. Затем возвращает commit, базу, публичные
файлы, Laravel-кэши и worker. Если процесс прервётся после начала изменения
данных, сайт останется в maintenance mode.

Откат только кода:


```bash
cd /var/www/html/banyastore
./deploy.sh <previous-commit-hash>
```

После миграций или импорта одного отката кода недостаточно: использовать полный
релизный снимок, а не ручной `migrate:rollback`.

## Важные замечания

- Не править код руками прямо на проде, если дальше планируется деплой через `deploy.sh`.
- Контент, товары, заказы и другие данные в БД можно создавать через админку как обычно.
- Если на проде появились локальные изменения, сначала разобраться с `git status`, а потом деплоить.
- Для обычного PHP-only релиза не нужен `RUN_COMPOSER=1` и не нужен `RUN_MIGRATIONS=1`.
- Для релиза с frontend-изменениями ассеты нужно сначала собрать локально и включить в тот же commit, который потом уедет на прод.
- Периодически проверять размер `/var/www/backups/banyastore`. Старые снимки удалять вручную только после проверки актуального снимка и оставлять минимум два последних пригодных для восстановления релиза.
