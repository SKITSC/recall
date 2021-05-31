FROM mariadb:latest as builder
ENV MARIADB_ROOT_PASSWORD=${DB_PASSWORD}
COPY ./database/ /docker-entrypoint-initdb.d/
RUN ["/usr/local/bin/docker-entrypoint.sh", "mysqld", "--datadir", "/plivo-data", "--aria-log-dir-path", "/plivo-data"]

FROM mariadb:latest
COPY --from=builder /plivo-data /var/lib/mysql