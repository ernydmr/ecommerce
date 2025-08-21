FROM php:8.2-fpm

# Gerekli paketler ve pgsql eklentisi
RUN apt-get update && apt-get install -y \
    libpq-dev unzip git curl \
 && docker-php-ext-install pdo pdo_pgsql

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Çalışma dizini
WORKDIR /var/www/html

# PHP ayarları (varsa)
COPY docker/php.ini /usr/local/etc/php/conf.d/php-custom.ini

EXPOSE 9000
CMD ["php-fpm"]
