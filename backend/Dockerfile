# Versi dasar dipatok (bukan latest)
FROM php:8.3-cli-alpine3.20

ENV COMPOSER_ALLOW_SUPERUSER=1

# unzip dibutuhkan composer untuk mengekstrak paket; pdo_mysql untuk koneksi MySQL
RUN apk add --no-cache unzip \
 && docker-php-ext-install pdo_mysql

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Lapisan dependensi: hanya composer.json + composer.lock, supaya cache tetap dipakai
# selama dependensi tidak berubah (kode aplikasi disalin SETELAH ini)
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --no-scripts --no-autoloader

# Kode aplikasi
COPY . .

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh \
 && chmod +x /usr/local/bin/docker-entrypoint.sh \
 && composer dump-autoload --optimize --no-dev --no-scripts \
 && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
 && touch database/database.sqlite \
 && chown -R www-data:www-data storage bootstrap/cache database

# Tidak ada .env, APP_KEY, atau password di dalam image.
# Semua nilai diberikan saat container dijalankan (lihat docker-compose.yml).
USER www-data
EXPOSE 8000
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
