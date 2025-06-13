# Use base image nginx Alpine
FROM nginx:alpine


# Salin konfigurasi nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Salin source code ke direktori root nginx
COPY html/ /usr/share/nginx/html

# Expose port 80
EXPOSE 80

# Jalankan PHP-FPM dan Nginx secara bersamaan
CMD php8.0-fpm & nginx -g 'daemon off;'
