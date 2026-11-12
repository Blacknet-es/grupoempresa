#!/bin/sh
set -eu

cd /var/www/html
mkdir -p var/cache var/log public/uploads/files documents
chown -R www-data:www-data var public/uploads documents

php bin/console app:database-backup:import --env=prod --no-interaction
php bin/console cache:warmup --env=prod

exec sh -c 'php-fpm -D && exec nginx -g "daemon off;"'
