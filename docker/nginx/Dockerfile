FROM php:7.4-cli-bullseye AS php-assets

RUN sed -i \
        's|deb.debian.org/debian-security|archive.debian.org/debian-security|g' \
        /etc/apt/sources.list \
    && printf 'Acquire::Check-Valid-Until "false";\n' \
        > /etc/apt/apt.conf.d/99no-check-valid-until \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libfreetype6-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libzip-dev \
        zlib1g-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" bcmath gd intl mbstring pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY grupoempresa/ ./

RUN composer config --global allow-plugins.symfony/flex true \
    && composer config --global allow-plugins.composer/package-versions-deprecated true \
    && composer install --no-dev --optimize-autoloader --no-scripts \
        --no-interaction --prefer-dist

RUN APP_ENV=prod \
        APP_DEBUG=0 \
        APP_SECRET=assets-build-only \
        DATABASE_URL='mysql://build:build@127.0.0.1/build' \
        MAILER_URL=null://localhost \
        AWS_KEY=build-only \
        AWS_SECRET=build-only \
        WKHTMLTOPDF_PATH=/usr/bin/false \
        WKHTMLTOIMAGE_PATH=/usr/bin/false \
        php bin/console assets:install public --no-interaction

FROM node:10-buster AS assets

RUN sed -i \
        -e 's|deb.debian.org/debian|archive.debian.org/debian|g' \
        -e 's|security.debian.org/debian-security|archive.debian.org/debian-security|g' \
        -e '/buster-updates/d' \
        /etc/apt/sources.list \
    && printf 'Acquire::Check-Valid-Until "false";\n' \
        > /etc/apt/apt.conf.d/99no-check-valid-until \
    && apt-get update \
    && apt-get install -y --no-install-recommends git ca-certificates \
    && rm -rf /var/lib/apt/lists/* \
    && npm install --global bower@1.8.14

WORKDIR /var/www/html
COPY --from=php-assets /var/www/html/ ./

RUN rm -rf node_modules bower_components public/css public/js public/fonts public/webfonts \
    && npm ci --no-audit --no-fund \
    && bower install --allow-root --config.interactive=false \
    && ./node_modules/.bin/gulp \
    && test -s public/css/front.css \
    && test -s public/js/header-app.js \
    && test -s public/js/app.js \
    && test -s public/webfonts/fa-solid-900.woff2

FROM nginx:alpine

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=assets /var/www/html/public/ /var/www/html/public/
