# Dockerfile

# 1st stage : build js & css
FROM node:16-alpine as builder

ENV NODE_ENV=production
WORKDIR /app

ADD package.json yarn.lock webpack.config.js ./
ADD assets ./assets

RUN mkdir -p public && \
    NODE_ENV=development yarn install && \
    yarn run build

FROM silarhi/php-apache:8.1-symfony

# 2nd stage : build the real app container
EXPOSE 80
WORKDIR /app

# Default APP_VERSION, real version will be given by the CD server
ARG APP_VERSION=dev
ARG GIT_COMMIT=master
ENV APP_VERSION="${APP_VERSION}"
ENV GIT_COMMIT="${GIT_COMMIT}"

RUN apt-get update -qq && \
    apt-get install -qy \
    libmagickwand-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev

RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ && \
    git clone https://github.com/Imagick/imagick && \
    cd imagick && \
    phpize && ./configure && make && make install && \
    cd .. && rm -Rf imagick && \
    docker-php-ext-install gd exif && \
    docker-php-ext-enable imagick

COPY . /app
COPY --from=builder /app/public/build /app/public/build

RUN mkdir -p var && \
    APP_ENV=prod composer install --prefer-dist --optimize-autoloader --classmap-authoritative --no-interaction --no-ansi --no-dev && \
    APP_ENV=prod bin/console cache:clear --no-warmup && \
    APP_ENV=prod bin/console cache:warmup && \
    # We don't use DotEnv component as docker-compose will provide real environment variables
    echo "<?php return [];" > .env.local.php && \
    mkdir -p var/storage && \
    chown -R www-data:www-data var && \
    # Reduce container size
    rm -rf .git assets /root/.composer /tmp/*
