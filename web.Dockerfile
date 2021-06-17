FROM php:7.3-apache

LABEL iyadk <iyadk@skitsc.com>

ENV TIMEZONE America/Toronto
RUN  DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get upgrade -y
RUN apt-get install git -y
RUN apt-get install cron -y

RUN docker-php-ext-install pdo pdo_mysql

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
ADD templates /var/www/templates
ADD config/apache-config.conf /etc/apache2/sites-enabled/000-default.conf

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin --filename=composer

ADD composer.json /var/www
WORKDIR /var/www
RUN composer install

RUN service apache2 restart

EXPOSE 80

#cron
ADD utils /var/www/utils
RUN chmod -R 755 /var/www/utils
RUN mkdir logs
RUN touch logs/fetch_logs
RUN touch logs/download_logs

RUN chmod -R 755 /var/www/html
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www
RUN chown -R www-data:www-data /var/www
RUN ls -la /var/www/html

#cron
ADD crontab /etc/cron.d/tasks
RUN chmod 0755 /etc/cron.d/tasks
RUN cron

RUN php /var/www/utils/fetch_recordings.php?fetch=all
CMD /usr/sbin/apache2ctl -D FOREGROUND