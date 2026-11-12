FROM php:7.4-fpm-alpine

RUN apk add --no-cache \
        freetype \
        icu-libs \
        libjpeg-turbo \
        libpng \
        libzip \
        oniguruma \
        git \
        unzip \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        freetype-dev \
        icu-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libzip-dev \
        oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" bcmath gd intl mbstring opcache pdo_mysql zip \
    && apk del .build-deps

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer
COPY docker/php/dev-pool.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www/html
COPY grupoempresa/ ./

RUN composer config --global allow-plugins.symfony/flex true \
    && composer config --global allow-plugins.composer/package-versions-deprecated true \
    && composer install --no-interaction --prefer-dist --no-scripts \
    && mkdir -p var/cache var/log public/uploads/files documents \
    && chown -R www-data:www-data var public/uploads documents

CMD ["php-fpm", "-F"]