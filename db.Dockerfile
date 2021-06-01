FROM mysql:5.7.34

LABEL iyadk <iyadk@skitsc.com>

ENV MYSQL_ROOT_PASSWORD $DB_PASSWORD

COPY ./database/ /docker-entrypoint-initdb.d/

EXPOSE 3306