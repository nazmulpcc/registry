FROM composer:2 AS composer
WORKDIR /app

COPY composer.json composer.lock /app/
RUN composer install  \
    --ignore-platform-reqs \
    --no-ansi \
    --no-autoloader \
    --no-dev \
    --no-interaction \
    --no-scripts

COPY . /app/
RUN composer dump-autoload --optimize --classmap-authoritative


FROM nazmulpcc/php:8.4-cli

LABEL maintainer="Nazmul Alam <n@zmul.dev>"
WORKDIR /app
COPY --from=composer /app .
COPY --from=composer /usr/bin/composer /usr/bin/composer

CMD php artisan octane:start --host=0.0.0.0 --port=8000
