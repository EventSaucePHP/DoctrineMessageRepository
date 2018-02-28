#!/usr/bin/env bash

docker-compose -f ./tests/docker-compose.yml up -d
php tests/wait-for-connection.php mysql
php tests/wait-for-connection.php pgsql
php tests/setup-postgres-schema.php
php tests/setup-mysql-schema.php