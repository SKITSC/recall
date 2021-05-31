FROM httpd:alpine

LABEL iyadk <iyadk@skitsc.com>

ENV TIMEZONE America/Toronto

#httpd conf
RUN rm /usr/local/apache2/conf/httpd.conf
COPY ./config/httpd.conf /usr/local/apache2/conf/

COPY ./src /usr/local/apache2/htdocs
RUN chmod -R 755 /usr/local/apache2/htdocs
RUN chown -R www-data:www-data /usr/local/apache2/htdocs
RUN ls -la /usr/local/apache2/htdocs

RUN apk update && apk upgrade
RUN apk add mariadb mariadb-client \
    curl \
    php7-cli \
    php7-phar \
    php7-zlib \
    php7-zip \
    php7-bz2 \
    php7-ctype \
    php7-curl \
    php7-pdo_mysql \
    php7-mysqli \
    php7-json \
    php7-mcrypt \
    php7-xml \
    php7-dom \
    php7-iconv \
    php7-xdebug \
    php7-session \
    php7-intl \
    php7-gd \
    php7-mbstring \
    php7-apcu \
    php7-opcache \
    php7-tokenizer

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin --filename=composer

WORKDIR /usr/local/apache2/htdocs
RUN composer require

CMD ["httpd-foreground"]