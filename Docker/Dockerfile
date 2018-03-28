FROM php:7.2-fpm

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update \
    && php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod +x /usr/local/bin/composer