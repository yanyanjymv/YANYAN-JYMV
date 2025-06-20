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

# Salin konfigurasi Nginx dan Supervisor
COPY nginx.conf /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisord.conf

# Salin source code
COPY html/ /var/www/html/

# Buat direktori yang dibutuhkan
RUN mkdir -p /run/nginx

EXPOSE 80

# Jalankan Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
