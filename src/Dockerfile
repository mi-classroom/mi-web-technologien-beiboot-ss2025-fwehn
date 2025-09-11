# Dockerfile
FROM php:8.2-cli

# System-Tools + PHP-Extensions installieren
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

# Composer installieren
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY .docker/php.ini /usr/local/etc/php/conf.d/laravel.ini

# Arbeitsverzeichnis
WORKDIR /app

# Code rein
COPY . .

# PHP-Dependencies installieren
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Node-Dependencies installieren
RUN npm install && npm run build

# SQLite-DB vorbereiten
RUN mkdir -p database && touch database/database.sqlite

# Port freigeben
EXPOSE 8000

# Start-Befehl
CMD [ "sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000" ]
