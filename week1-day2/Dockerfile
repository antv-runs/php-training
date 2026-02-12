FROM php:8.2-apache

# Cài extension
RUN docker-php-ext-install pdo pdo_mysql

# Cài Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Copy cấu hình PHP
COPY php.ini /usr/local/etc/php/

WORKDIR /var/www/html
