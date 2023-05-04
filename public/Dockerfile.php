FROM php:8.0-fpm

RUN apt-get update && \
    apt-get install -y libpq-dev && \
    docker-php-ext-install pdo_pgsql

RUN apt-get install -y postgresql-client

WORKDIR /var/www/html
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update
RUN apt-get install -y zip unzip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY composer.json ./
COPY composer.lock ./
COPY phinx.yml ./
COPY configdb.php ./
COPY DB/. /var/www/html/db/

RUN composer update
RUN composer dump-autoload

RUN composer install