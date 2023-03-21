FROM ghcr.io/roadrunner-server/roadrunner:2.12.3 AS roadrunner
FROM php:8.2-cli

WORKDIR /app

COPY --from=roadrunner /usr/bin/rr /usr/local/bin/rr
COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN apt-get update \
    && apt-get install -y libzip-dev zip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install sockets zip

COPY . .

RUN composer install
