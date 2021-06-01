FROM php:7.3-apache

LABEL iyadk <iyadk@skitsc.com>

ENV TIMEZONE America/Toronto
RUN  DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get upgrade -y
RUN apt-get install git -y

RUN a2enmod rewrite
RUN a2enmod ssl

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_PID_FILE /var/run/apache2.pid

#config
ADD .env /var/www
ADD config.php /var/www

#source
ADD src /var/www/html
ADD config/apache-config.conf /etc/apache2/sites-enabled/000-default.conf

RUN chmod -R 755 /var/www/html
RUN chown -R www-data:www-data /var/www/html
RUN ls -la /var/www/html

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin --filename=composer

ADD composer.json /var/www
WORKDIR /var/www
RUN composer install

EXPOSE 80

CMD /usr/sbin/apache2ctl -D FOREGROUND