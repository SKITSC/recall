FROM mysql:8.0.25

ENV MYSQL_ROOT_PASSWORD $DB_PASSWORD

COPY ./database/ /docker-entrypoint-initdb.d/

EXPOSE 3306