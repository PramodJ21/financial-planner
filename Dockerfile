# Stage 1: Build
FROM composer:2 AS build

WORKDIR /app

# Copy and install dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader

# Copy rest of the application
COPY . .

# Stage 2: Production image
FROM php:8.3-fpm-alpine

# Install needed PHP extensions
RUN docker-php-ext-install pdo pdo_mysql bcmath

# Install Nginx and supervisor for process management
RUN apk add --no-cache nginx supervisor bash

# Copy application code
WORKDIR /var/www/html
COPY --from=build /app ./

# Set up Nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

# Copy supervisor config
COPY docker/supervisord.conf /etc/supervisord.conf

# Permissions for Laravel storage and bootstrap
RUN chown -R www-data:www-data storage bootstrap/cache

# Environment variables
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV APP_KEY=
ENV APP_URL=${RENDER_EXTERNAL_URL}

EXPOSE 8080

# Start both PHP-FPM and Nginx
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
