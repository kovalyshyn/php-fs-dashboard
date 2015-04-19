#!/bin/bash
set -e

if [ "$PGSQL_HOST" ]; then
	sed -i 's/PGSQL_HOST/'$PGSQL_HOST'/g' /data/www/app/config/database.php
	sed -i 's/PGSQL_HOST/'$PGSQL_HOST'/g' /data/www/public/dialplan.xml.php
	sed -i 's/PGSQL_HOST/'$PGSQL_HOST'/g' /data/www/public/directory.xml.php
fi

if [ "$PGSQL_USER" ]; then
	sed -i 's/PGSQL_USER/'$PGSQL_USER'/g' /data/www/app/config/database.php
	sed -i 's/PGSQL_USER/'$PGSQL_USER'/g' /data/www/public/dialplan.xml.php
	sed -i 's/PGSQL_USER/'$PGSQL_USER'/g' /data/www/public/directory.xml.php
fi

if [ "$PGSQL_PASS" ]; then
	sed -i 's/PGSQL_PASS/'$PGSQL_PASS'/g' /data/www/app/config/database.php
	sed -i 's/PGSQL_PASS/'$PGSQL_PASS'/g' /data/www/public/dialplan.xml.php
	sed -i 's/PGSQL_PASS/'$PGSQL_PASS'/g' /data/www/public/directory.xml.php
fi

if [ "$1" = 'laravel' ]; then
    exec tail -f /data/www/app/storage/logs/laravel.log
fi

exec "$@"
