# Stage 1: Base image with PHP and Composer
FROM php:8.2-cli

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    zip \
    libzip-dev \
    libonig-dev \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite zip mbstring bcmath

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Set permissions for Laravel
RUN chmod -R 775 storage bootstrap/cache

# Expose port
EXPOSE 8000

# Start Laravel development server
CMD php artisan serve --host=0.0.0.0 --port=8000
