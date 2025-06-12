FROM php:php-fpm

RUN docker-php-ext-install mysqli
