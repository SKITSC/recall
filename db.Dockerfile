FROM mysql:8.0.25
ENV MYSQL_ROOT_PASSWORD $DB_PASSWORD
COPY ./database/ /docker-entrypoint-initdb.d/
RUN ["/usr/local/bin/docker-entrypoint.sh", "mysqld", "--datadir", "/plivo-data", "--aria-log-dir-path", "/plivo-data"]