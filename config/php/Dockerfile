FROM php:8.3.3RC1-fpm-alpine3.18

RUN apk --no-cache add \
    git \
    postgresql-dev

RUN docker-php-ext-install pgsql pdo_pgsql pdo

RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
COPY symfony /var/www/symfony
WORKDIR /var/www/symfony