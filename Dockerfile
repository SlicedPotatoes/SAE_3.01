# ---------------------------------------
# Stage 1 - Installation des dépendances
# ---------------------------------------
FROM composer:2 AS composer

WORKDIR /app

COPY ./composer.json ./composer.lock ./

RUN composer install --no-scripts --prefer-dist --no-interaction

# ---------------------------------------
# Stage 2 - PHP Server
# ---------------------------------------
FROM php:8.4-cli

WORKDIR /app

# Installation des dépendances
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    curl

RUN docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    zip

RUN docker-php-ext-enable \
    opcache

# Supprimer les listes apt (image moins lourde)
RUN rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY . .
COPY --from=composer /app/vendor ./vendor

RUN php copy_bootstrap.php
RUN php get_chartjs.php


CMD ["php", "-S", "0.0.0.0:8000", "-t", "public", "public/router.php"]