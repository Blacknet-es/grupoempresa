FROM php:7.4-fpm-bullseye AS php-build

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
    && docker-php-ext-install -j"$(nproc)" bcmath gd intl mbstring opcache pdo_mysql zip \
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

FROM node:10-buster AS node-tools

RUN npm install --global bower@1.8.14

FROM php:7.4-fpm-bullseye AS production

RUN sed -i \
        's|deb.debian.org/debian-security|archive.debian.org/debian-security|g' \
        /etc/apt/sources.list \
    && printf 'Acquire::Check-Valid-Until "false";\n' \
        > /etc/apt/apt.conf.d/99no-check-valid-until \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        libfontconfig1 \
        libfreetype6 \
        libicu67 \
        libjpeg62-turbo \
        libonig5 \
        libpng16-16 \
        libssl1.1 \
        libx11-6 \
        libxext6 \
        libxrender1 \
        libzip4 \
        mariadb-client \
        zlib1g \
    && rm -rf /var/lib/apt/lists/*

COPY --from=php-build /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=php-build /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --from=php-build /var/www/html/ /var/www/html/
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/99-opcache.ini
COPY docker/prod/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN composer config --no-plugins --global allow-plugins.symfony/flex true \
    && composer config --no-plugins --global allow-plugins.composer/package-versions-deprecated true \
    && ln -s /var/www/html/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64 /usr/local/bin/wkhtmltopdf \
    && ln -s /var/www/html/vendor/h4cc/wkhtmltoimage-amd64/bin/wkhtmltoimage-amd64 /usr/local/bin/wkhtmltoimage \
    && wkhtmltopdf --version \
    && wkhtmltoimage --version \
    && mkdir -p /var/www/html/var/cache /var/www/html/var/log \
        /var/www/html/public/uploads/files /var/www/html/documents \
    && chown -R www-data:www-data /var/www/html/var \
        /var/www/html/public/uploads /var/www/html/documents \
    && chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm", "-F"]

FROM node-tools AS node-assets

FROM production AS development

COPY --from=node-assets /usr/local/bin/node /usr/local/bin/node
COPY --from=node-assets /usr/local/lib/node_modules/ /usr/local/lib/node_modules/
COPY docker/php/dev-pool.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/dev/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN apt-get update \
    && apt-get install -y --no-install-recommends git ca-certificates \
    && rm -rf /var/lib/apt/lists/* \
    && ln -s ../lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s ../lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx \
    && ln -s ../lib/node_modules/bower/bin/bower /usr/local/bin/bower \
    && rm -f /usr/local/etc/php/conf.d/99-opcache.ini \
    && chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm", "-F"]
