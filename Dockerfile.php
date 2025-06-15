FROM php:fpm

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql json

# Copy custom php.ini file
COPY custom-php.ini /usr/local/etc/php/conf.d/

# Set working directory
WORKDIR /var/www

# Start php-fpm
CMD ["php-fpm"]
