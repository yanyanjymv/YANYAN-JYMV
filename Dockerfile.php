FROM php:8-fpm

RUN docker-php8-ext-install mysqli
