# php-fs-dashboard

	docker-compose up -d
	docker exec phpfsdashboard_db_1 /sql_restore.sh

#### ENV

	‘host’     => ‘PGSQL_HOST’,
	‘database’ => ‘switch’,
	‘username’ => ‘PGSQL_USER’,
	‘password’ => ‘PGSQL_PASS’,  