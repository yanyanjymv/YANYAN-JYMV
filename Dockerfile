# Gunakan base image Nginx dari Alpine
FROM nginx:alpine

# Menambahkan repositori komunitas PHP
RUN echo "https://dl-cdn.alpinelinux.org/alpine/edge/community" >> /etc/apk/repositories && \
    apk update

# Install PHP-FPM dan ekstensi PHP yang dibutuhkan
RUN apk add --no-cache php php-fpm php-mysqli php-pdo php-pdo_mysql php-json && rm -rf /var/cache/apk/*

# Salin file konfigurasi Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Salin source code aplikasi PHP ke direktori yang sesuai di Nginx
COPY html/ /usr/share/nginx/html/

# Salin file konfigurasi supervisord
COPY supervisord.conf /etc/supervisord.conf

# Expose port untuk Nginx dan PHP-FPM
EXPOSE 80 9000 

# Jalankan PHP-FPM dan Nginx secara bersamaan
CMD ["/usr/bin/supervisord"]
