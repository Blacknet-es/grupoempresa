#!/bin/sh
set -eu

php /usr/local/bin/import-database-backup.php
exec "$@"
