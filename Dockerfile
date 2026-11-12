FROM php:7.4-fpm-alpine AS build

RUN apk add --no-cache git unzip \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        freetype-dev \
        icu-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libzip-dev \
        oniguruma-dev \
    && apk add --no-cache \
        freetype \
        icu-libs \
        libjpeg-turbo \
        libpng \
        libzip \
        oniguruma \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" bcmath gd intl mbstring opcache pdo_mysql zip

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY grupoempresa/ ./

RUN composer config --global allow-plugins.symfony/flex true \
    && composer config --global allow-plugins.composer/package-versions-deprecated true \
    && composer install --no-dev --optimize-autoloader --no-scripts \
        --no-interaction --prefer-dist \
    && mkdir -p var/cache var/log public/uploads/files documents \
    && chown -R www-data:www-data var public/uploads documents
RUN APP_ENV=prod \
        APP_DEBUG=0 \
        APP_SECRET=assets-build-only \
        DATABASE_URL='mysql://build:build@127.0.0.1/build' \
        MAILER_URL=null://localhost \
        AWS_KEY=build-only \
        AWS_SECRET=build-only \
        WKHTMLTOPDF_PATH=/usr/bin/false \
        WKHTMLTOIMAGE_PATH=/usr/bin/false \
        php bin/console assets:install --relative public --no-interaction

FROM node:10-buster AS assets

WORKDIR /var/www/html
COPY --from=build /var/www/html/ ./

RUN rm -rf node_modules bower_components public/css public/js public/fonts public/webfonts \
    && npm ci --no-audit --no-fund \
    && npm install --global bower@1.8.14 \
    && bower install --allow-root --config.interactive=false \
    && ./node_modules/.bin/gulp \
    && test -s public/css/front.css \
    && test -s public/js/header-app.js \
    && test -s public/js/app.js \
    && test -s public/webfonts/fa-solid-900.woff2

FROM php:7.4-fpm-alpine AS runtime

RUN apk add --no-cache \
        freetype \
        icu-libs \
        libjpeg-turbo \
        libpng \
        libzip \
        mariadb-client \
        nginx \
        oniguruma

COPY --from=build /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=build /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --from=build /var/www/html/ /var/www/html/
COPY --from=assets /var/www/html/public/ /var/www/html/public/
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/99-opcache.ini
COPY docker/prod/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/prod/default.conf /etc/nginx/http.d/default.conf
COPY docker/prod/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh \
    && mkdir -p /run/nginx \
    && chown -R www-data:www-data /var/www/html/var /var/www/html/public/uploads /var/www/html/documents

WORKDIR /var/www/html
EXPOSE 80
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
