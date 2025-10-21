FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www

# Install system dependencies (Alpine Linux uses apk instead of apt)
RUN apk update && apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy composer files first (for better caching)
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --no-autoloader --no-scripts

# Copy the rest of the application
COPY . .

# Generate autoload and optimize
RUN composer dump-autoload --optimize

# Set proper permissions
RUN chown -R www-data:www-data /var/www

# Change to www-data user
USER www-data

# Expose port
EXPOSE 9000

CMD ["php-fpm"]