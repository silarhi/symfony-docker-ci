# Dockerfile

FROM silarhi/php-apache:8.3-frankenphp-bookworm as php_builder
WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER=1

# Composer install before sources
COPY composer.json composer.lock symfony.lock ./
RUN APP_ENV=prod composer install --no-interaction --no-dev --no-scripts --prefer-dist

# 1st stage : build js & css
FROM node:20-alpine as builder

ENV NODE_ENV=production
WORKDIR /app

COPY --from=php_builder --link /app/vendor ./vendor
COPY package.json yarn.lock webpack.config.js ./
COPY assets ./assets

RUN mkdir -p public && \
    NODE_ENV=development yarn install && \
    yarn run build

FROM php_builder

# 2nd stage : build the real app container
EXPOSE 80
WORKDIR /app

# Default APP_VERSION, real version will be given by the CD server
ARG APP_VERSION=dev
ARG GIT_COMMIT=master
ENV APP_VERSION="${APP_VERSION}"
ENV GIT_COMMIT="${GIT_COMMIT}"

RUN install-php-extensions exif gd imagick/imagick@master

# Enable PHP production settings
COPY docker/php.ini $PHP_INI_DIR/conf.d/app.ini

COPY . /app
COPY --from=builder /app/public/build /app/public/build

RUN mkdir -p var var/storage && \
    APP_ENV=prod composer install --prefer-dist --optimize-autoloader --classmap-authoritative --no-interaction --no-ansi --no-dev && \
    APP_ENV=prod bin/console cache:clear --no-warmup && \
    APP_ENV=prod bin/console cache:warmup && \
    # We don't use DotEnv component as docker-compose will provide real environment variables
    echo "<?php return [];" > .env.local.php && \
    chown -R www-data:www-data var && \
    # Reduce container size
    rm -rf .git assets /root/.composer /tmp/*
