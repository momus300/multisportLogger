FROM webdevops/php-apache-dev:7.2

ARG login
ARG pass

RUN echo "0 */6 * * * root cd /var/www/html && /usr/local/bin/php /var/www/html/logger.php $login $pass > /dev/null 2>&1" > /etc/cron.d/multisportCron
RUN chmod 644 /etc/cron.d/multisportCron
RUN apt-get update && apt-get -y install nano
RUN service cron restart
