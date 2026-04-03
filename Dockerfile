#syntax=docker/dockerfile:1.23-labs

# Versions
FROM silarhi/php-apache:8.5-frankenphp-alpine AS php_upstream
FROM node:24-alpine AS node_upstream

# Base with extensions
FROM php_upstream AS php_base
RUN install-php-extensions exif gd imagick

# Composer deps (cached)
FROM php_base AS php_builder
WORKDIR /app
COPY --link composer.json composer.lock symfony.lock ./
RUN --mount=type=cache,target=/root/.composer \
    APP_ENV=prod composer install --no-interaction --no-dev --no-scripts --prefer-dist

# Node deps (cached separately from build)
FROM node_upstream AS node_deps
WORKDIR /app
COPY --from=php_builder --link /app/vendor ./vendor
COPY --link package.json yarn.lock ./
RUN --mount=type=cache,target=/root/.yarn \
    YARN_CACHE_FOLDER=/root/.yarn yarn install --frozen-lockfile

# Asset build
FROM node_upstream AS node_builder
WORKDIR /app
COPY --from=node_deps --link /app/node_modules ./node_modules
COPY --from=node_deps --link /app/vendor ./vendor
COPY --link package.json webpack.config.js yarn.lock ./
COPY --link assets ./assets
RUN mkdir -p public && yarn build

# Final
FROM php_base
EXPOSE 80
WORKDIR /app

ARG APP_VERSION=dev
ARG GIT_COMMIT=master
ENV APP_VERSION="${APP_VERSION}"
ENV GIT_COMMIT="${GIT_COMMIT}"

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
