#syntax=docker/dockerfile:1.7-labs

# Versions
FROM silarhi/php-apache:8.4-frankenphp-alpine as php_upstream
FROM node:20-alpine as node_upstream

FROM php_upstream as php_builder
WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1

# Composer install before sources
COPY composer.json composer.lock symfony.lock ./
RUN composer install --no-interaction --no-dev --no-scripts --prefer-dist

# 1st stage : build js & css
FROM node_upstream as node_builder

ENV NODE_ENV=production
WORKDIR /app

COPY --from=php_builder --link /app/vendor ./vendor
COPY --link package.json yarn.lock webpack.config.js ./
COPY --link assets ./assets

RUN mkdir -p public && \
    NODE_ENV=development yarn install && \
    yarn run build

FROM php_upstream

# 2nd stage : build the real app container
EXPOSE 80
WORKDIR /app

# Default APP_VERSION, real version will be given by the CD server
ARG APP_VERSION=dev
ARG GIT_COMMIT=master
ENV APP_VERSION="${APP_VERSION}"
ENV GIT_COMMIT="${GIT_COMMIT}"

RUN install-php-extensions exif gd imagick

COPY --from=php_builder --link /app/vendor ./vendor
COPY --from=node_builder --link /app/public/build /app/public/build
COPY --link --exclude=assets --exclude=docker . .

# Config
COPY --link docker/php.ini $PHP_INI_DIR/conf.d/app.ini

RUN mkdir -p var var/storage && \
    composer dump-autoload --optimize --classmap-authoritative --no-dev --no-interaction && \
    APP_ENV=prod bin/console cache:clear --no-warmup && \
    APP_ENV=prod bin/console cache:warmup && \
    # We don't use DotEnv component as docker-compose will provide real environment variables
    echo "<?php return [];" > .env.local.php && \
    chown -R www-data:www-data var && \
    rm -rf /root/.cache
