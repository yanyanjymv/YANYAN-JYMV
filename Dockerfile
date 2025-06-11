# Use base image nginx Alpine
FROM nginx:alpine

# Install PHP-FPM dan ekstensi yang dibutuhkan di Alpine
RUN apk add --no-cache php php-fpm php-mysqli

# Salin konfigurasi nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Salin source code ke direktori root nginx
COPY . /usr/share/nginx/html

# Expose port 80
EXPOSE 80

# Jalankan PHP-FPM dan Nginx secara bersamaan
CMD ["php-fpm8", "-D", "&&", "nginx", "-g", "daemon off;"]