#!/bin/sh
set -eu

cd /var/www/html
mkdir -p var/cache var/log public/uploads/files documents
chown -R www-data:www-data var public/uploads documents

php bin/console app:database-backup:import --env="${APP_ENV:-prod}" --no-interaction

if [ "${APP_ENV:-prod}" = "prod" ]; then
    php bin/console cache:clear --env=prod --no-warmup
    php bin/console cache:warmup --env=prod
fi

exec "$@"
