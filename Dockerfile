# Gunakan base image nginx Alpine
FROM nginx:alpine

# Install PHP-FPM dan ekstensi yang dibutuhkan di Alpine
RUN apk add --no-cache php php-fpm php-mysqli php-pdo php-json supervisor

# Salin konfigurasi nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Salin source code ke direktori root nginx
COPY html/ /usr/share/nginx/html

# Salin file konfigurasi supervisor untuk menjalankan PHP-FPM dan Nginx
COPY supervisord.conf /etc/supervisord.conf

# Expose port 80
EXPOSE 80

# Jalankan supervisord untuk menjalankan PHP-FPM dan Nginx secara bersamaan
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
