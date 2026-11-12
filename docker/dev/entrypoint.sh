#!/bin/sh
set -eu

cd /var/www/html
mkdir -p var/cache var/log public/uploads/files documents
chown -R www-data:www-data var public/uploads documents

composer install --no-interaction --prefer-dist
php bin/console assets:install public --no-interaction

if [ ! -x node_modules/.bin/gulp ]; then
    npm ci --no-audit --no-fund
fi

if [ ! -d bower_components ]; then
    bower install --allow-root --config.interactive=false
fi

./node_modules/.bin/gulp
php bin/console app:database-backup:import --no-interaction
exec "$@"
