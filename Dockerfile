# Stage 1: PHP-FPM
FROM php:8.1-fpm as php-fpm

# Install ekstensi PHP yang dibutuhkan
RUN docker-php-ext-install mysqli

# Salin source code aplikasi PHP ke direktori yang sesuai di PHP-FPM
COPY html/ /var/www/html/

# Stage 2: Nginx
FROM nginx:alpine

# Salin konfigurasi Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Salin source code dari stage php-fpm ke folder yang sesuai di Nginx
COPY --from=php-fpm /var/www/html/ /usr/share/nginx/html/

# Expose port untuk Nginx
EXPOSE 80

# Jalankan Nginx di foreground
CMD ["nginx", "-g", "daemon off;"]
