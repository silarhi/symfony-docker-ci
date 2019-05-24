# Dockerfile
FROM guystlr/php-apache:7.2-symfony

EXPOSE 80
WORKDIR /app

ARG APP_VERSION=dev
ENV APP_VERSION="${APP_VERSION}"

# Install dependencies
RUN apt-get update -q && \

    #NPM
    curl -sL https://deb.nodesource.com/setup_8.x | bash - && \
    apt-get install -y nodejs && \
    curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - && \
    echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list && \
    npm install -g yarn && \

    # Reduce layer size
    apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY . /app

RUN mkdir -p var && \
    APP_ENV=prod composer install --optimize-autoloader --no-interaction --no-ansi --no-dev && \
    APP_ENV=prod bin/console cache:clear --no-warmup && \
    APP_ENV=prod bin/console cache:warmup && \
    echo "<?php return [];" > .env.local.php && \
    chown -R www-data:www-data var && \
    yarn install && \
    yarn run build && \
    # Reduce container size
    rm -rf .git /root/.composer /root/.npm /tmp/*
