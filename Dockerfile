FROM php:8.1-fpm-alpine

# Install dependencies dan PHP ekstensi yang dibutuhkan
RUN apk add --no-cache \
    nginx \
    supervisor \
    bash \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libxpm-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    && docker-php-ext-install mysqli pdo pdo_mysql


COPY nginx.conf /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisord.conf


COPY html/ /var/www/html/


RUN mkdir -p /run/nginx

EXPOSE 80


CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
