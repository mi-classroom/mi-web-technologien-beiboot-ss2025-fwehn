FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip curl \
    libsqlite3-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libzip-dev \
    libwebp-dev libavif-dev \
    nodejs npm \
    libimage-exiftool-perl \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
        --with-avif \
    && docker-php-ext-install pdo_sqlite gd zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY .docker/php.ini /usr/local/etc/php/conf.d/laravel.ini

WORKDIR /app

COPY . .

RUN mkdir -p \
    storage/app \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

RUN composer install --no-interaction --prefer-dist --optimize-autoloader

RUN npm install && npm run build

RUN mkdir -p database && touch database/database.sqlite

EXPOSE 8000

CMD [ "sh", "-c", "php artisan migrate:fresh --seed && php artisan serve --host=0.0.0.0 --port=8000 & php artisan queue:listen --tries=1" ]

