#!/bin/sh
set -eu

cd /var/www/html
php bin/console app:database-backup:import --no-interaction
exec "$@"
