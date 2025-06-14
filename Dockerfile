# Gunakan base image Nginx dari Alpine
FROM nginx:alpine

# Install PHP-FPM dan ekstensi PHP yang dibutuhkan
RUN apk add --no-cache php php-fpm php-mysqli && rm -rf /var/cache/apk/*

# Salin file konfigurasi Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Salin source code aplikasi PHP ke direktori yang sesuai di Nginx
COPY html/ /usr/share/nginx/html/

# Expose port untuk Nginx dan PHP-FPM
EXPOSE 80 9000

# Jalankan PHP-FPM dan Nginx secara bersamaan
CMD php-fpm && nginx -g 'daemon off;'
