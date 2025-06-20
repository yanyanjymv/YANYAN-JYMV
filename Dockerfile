# Stage 1: PHP-FPM
FROM php:8.1-fpm-alpine 

# Install ekstensi PHP yang dibutuhkan
RUN docker-php-ext-install mysqli php-xml php-mbstring

# Stage 2: Nginx + PHP-FPM
FROM nginx:alpine

# Install PHP-FPM di dalam Nginx container
RUN apk --no-cache add php8-fpm php8-mysqli php8-mbstring php8-xml php8-opcache

# Salin file konfigurasi Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Salin source code aplikasi PHP ke direktori yang sesuai di container Nginx
COPY html/ /var/www/html/

# Expose port untuk Nginx
EXPOSE 80

# Menjalankan PHP-FPM dan Nginx dalam satu container
CMD sh -c "php-fpm8 -D && nginx -g 'daemon off;'"
