FROM php:8.1-fpm

RUN docker-php-ext-install mysqli

EXPOSE 9000

COPY html/ /var/www/html