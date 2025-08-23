FROM php:8.2-fpm

# Sistemde gereken paketler (intl, zip, gd için dev paketleri) ve cleanup
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libpq-dev \
        libicu-dev \
        libzip-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libwebp-dev \
        unzip git curl; \
    docker-php-ext-configure gd --with-jpeg --with-webp; \
    docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        intl \
        zip \
        gd \
        bcmath; \
    docker-php-ext-enable opcache; \
    # Çalışma klasörlerini hazırla (varsa sorun yok; yoksa oluşturur)
    mkdir -p /var/www/html/storage/framework/{cache,data,sessions,testing,views} \
             /var/www/html/bootstrap/cache; \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache; \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache; \
    # İmajı küçült
    apt-get clean; \
    rm -rf /var/lib/apt/lists/*

    #timezoneyi istanbula çektik
RUN ln -snf /usr/share/zoneinfo/Europe/Istanbul /etc/localtime && echo "Europe/Istanbul" > /etc/timezone

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1


# Çalışma dizini
WORKDIR /var/www/html

# İsteğe bağlı: php.ini yükle (dosya gerçekten bu path’te olmalı)
# Prod için opcache ayarlarını da buraya koyabilirsin.
COPY docker/php.ini /usr/local/etc/php/conf.d/php-custom.ini

# Port ve komut
EXPOSE 9000
CMD ["php-fpm"]
