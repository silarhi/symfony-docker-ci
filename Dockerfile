# Dockerfile

# 1st stage : build js & css
FROM node:18-alpine as builder

ENV NODE_ENV=production
WORKDIR /app

ADD package.json yarn.lock webpack.config.js ./
ADD assets ./assets

RUN mkdir -p public && \
    NODE_ENV=development yarn install && \
    yarn run build

FROM dunglas/frankenphp:1-php8.3-bookworm

# 2nd stage : build the real app container
EXPOSE 80
WORKDIR /app

ENV SERVER_NAME=:80
ENV COMPOSER_ALLOW_SUPERUSER=1

# Default APP_VERSION, real version will be given by the CD server
ARG APP_VERSION=dev
ARG GIT_COMMIT=master
ENV APP_VERSION="${APP_VERSION}"
ENV GIT_COMMIT="${GIT_COMMIT}"

# git, unzip & zip are for composer
RUN apt-get update -qq && \
    apt-get install -qy \
    git \
    gnupg \
    unzip \
    zip && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN install-php-extensions exif gd imagick/imagick@master

# Enable PHP production settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

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
