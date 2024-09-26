#!/usr/bin/env bash

mysql --user=root --password="$MYSQL_ROOT_PASSWORD" <<-EOSQL
    CREATE DATABASE IF NOT EXISTS app_test;
    GRANT ALL PRIVILEGES ON \`app_test%\`.* TO '$MYSQL_USER'@'%';
EOSQL
