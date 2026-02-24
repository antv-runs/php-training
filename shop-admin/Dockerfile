FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libicu-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl intl gd

COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
