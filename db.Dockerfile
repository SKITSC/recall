FROM mariadb:latest
COPY ./database/ /docker-entrypoint-initdb.d/
RUN ["/usr/local/bin/docker-entrypoint.sh", "mysqld", "--datadir", "/plivo-data", "--aria-log-dir-path", "/plivo-data"]