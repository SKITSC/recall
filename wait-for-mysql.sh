#!/bin/sh
until docker container exec -it plivo_db mysqladmin ping -P 3306 -p | grep "mysqld is alive" ; do
  >&2 echo "MySQL is unavailable - waiting for it... "
  sleep 1
done