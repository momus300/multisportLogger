FROM webdevops/php-apache-dev:7.2
ADD . /app
WORKDIR /app

RUN apt-get update && apt-get -y install nano

COPY .dockerConf/php.ini /opt/docker/etc/php/php.ini
COPY .dockerConf/momusSite.crt /opt/docker/etc/httpd/ssl/server.crt
COPY .dockerConf/momusSite.key /opt/docker/etc/httpd/ssl/server.key
COPY .dockerConf/666-kartymultisport.conf /opt/docker/etc/httpd/vhost.common.d/

RUN composer install

RUN cd /app && echo "0 */6 * * * root /usr/local/bin/php /app/bin/console app:logger > /dev/null 2>&1" > /etc/cron.d/multisportCron
