# Use base image nginx Alpine
FROM nginx:alpine

# Install PHP-FPM dan ekstensi yang dibutuhkan di Alpine
RUN apk add php8 php8-fpm php8-mysqli

# Salin konfigurasi nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Salin source code ke direktori root nginx
COPY html/ /usr/share/nginx/html

# Expose port 80
EXPOSE 80

# Jalankan PHP-FPM dan Nginx secara bersamaan
CMD php8-fpm && nginx -g 'daemon off;'
