# Stage 1: Build
FROM composer:2 AS build
WORKDIR /app

# Copy everything (so artisan exists)
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Stage 2: Production
FROM php:8.3-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql bcmath
RUN apk add --no-cache nginx supervisor bash

WORKDIR /var/www/html
COPY --from=build /app ./

COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf

RUN chown -R www-data:www-data storage bootstrap/cache

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV APP_URL=${RENDER_EXTERNAL_URL}

EXPOSE 8080

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
