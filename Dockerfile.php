FROM php:fpm

# Install ekstensi yang diperlukan
RUN docker-php-ext-install mysqli pdo pdo_mysql json

COPY custom-php.ini /usr/local/etc/php/conf.d/

CMD ["php-fpm"]
