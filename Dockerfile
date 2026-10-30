FROM composer:2 AS composer

FROM php:8.5.12-fpm

COPY --from=composer /usr/bin/composer /usr/bin/composer

# System dependencies
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
    git \
    curl \
    unzip \
    zip \
    libicu-dev \
    libzip-dev; \
    rm -rf /var/lib/apt/lists/*

# PHP extensions
RUN set -eux; \
    docker-php-ext-install -j"$(nproc)" \
    pdo_mysql \
    bcmath \
    intl \
    zip \
    pcntl

# Redis extension
RUN set -eux; \
    pecl install redis; \
    docker-php-ext-enable redis

WORKDIR /var/www/html

COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

EXPOSE 9000

CMD ["php-fpm"]