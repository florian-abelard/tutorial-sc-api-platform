FROM php:7.4-fpm-alpine

RUN apk update

RUN apk add zlib-dev postgresql-dev git icu-dev libxml2-dev libzip-dev unzip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && docker-php-ext-install zip xml

# Xdebug
RUN apk add --no-cache $PHPIZE_DEPS \
    && pecl install xdebug-2.8.0 \
    && docker-php-ext-enable xdebug

# Set timezone
RUN apk add tzdata
RUN ln -s /usr/share/zoneinfo/Europe/Paris /etc/localtime
RUN "date"
