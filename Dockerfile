# Dockerfile
FROM node:8-alpine as builder

ENV NODE_ENV=production
WORKDIR /app

ADD package.json yarn.lock webpack.config.js ./
ADD assets ./assets

RUN mkdir -p public && \
    npm install -g yarn && \
    NODE_ENV=development yarn install && \
    yarn run build

FROM silarhi/php-apache:7.3-symfony

EXPOSE 80
WORKDIR /app

ARG APP_VERSION=dev
ENV APP_VERSION="${APP_VERSION}"

COPY . /app
COPY --from=builder /app/public/build /app/public/build

RUN mkdir -p var && \
    APP_ENV=prod composer install --optimize-autoloader --no-interaction --no-ansi --no-dev && \
    APP_ENV=prod bin/console cache:clear --no-warmup && \
    APP_ENV=prod bin/console cache:warmup && \
    # We don't use DotEnv component as Docker will provide real environment variables
    echo "<?php return [];" > .env.local.php && \
    chown -R www-data:www-data var && \
    # Reduce container size
    rm -rf .git assets /root/.composer /tmp/*
