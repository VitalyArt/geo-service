FROM ghcr.io/roadrunner-server/roadrunner:2.12.3 AS roadrunner
FROM php:8.2-cli-alpine

WORKDIR /app

COPY --from=roadrunner /usr/bin/rr       /usr/local/bin/rr
COPY --from=composer   /usr/bin/composer /usr/local/bin/composer

RUN apk add --no-cache linux-headers libzip-dev zip
RUN docker-php-ext-install sockets zip

COPY . .

RUN composer install
