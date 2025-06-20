FROM alpine:latest

# Install PHP-FPM dan Nginx
RUN apk add --no-cache nginx php8.1 php8.1-fpm php8.1-mysqli php8.1-mbstring php8.1-xml php8.1-opcache supervisor

# Salin konfigurasi Nginx dan supervisord
COPY nginx.conf /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisord.conf

# Salin source code
COPY html/ /var/www/html/

# Buat direktori yang dibutuhkan
RUN mkdir -p /run/nginx

EXPOSE 80

# Jalankan supervisord untuk mengatur kedua service
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
