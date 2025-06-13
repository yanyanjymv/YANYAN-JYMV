FROM php:8.0-fpm

RUN docker-php8.0-ext-install mysqli
