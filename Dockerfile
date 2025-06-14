# Gunakan base image Nginx dari Alpine
FROM nginx:alpine

# Install PHP-FPM dan ekstensi PHP yang dibutuhkan
RUN apt-get update && apt-get install -y php8 php8-fpm php8-mysqli && rm -rf /var/cache/apk/*

# Salin file konfigurasi Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Salin source code aplikasi PHP ke direktori yang sesuai di Nginx
COPY html/ /usr/share/nginx/html/

# Expose port untuk Nginx dan PHP-FPM
EXPOSE 80 9000 

# Jalankan PHP-FPM dan Nginx secara bersamaan
CMD php8-fpm & nginx -g 'daemon off;'