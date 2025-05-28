#use base image nginx
FROM nginx:alpine

# Install PHP dan dependensinya
RUN apk add --no-cache php-fpm php-mysqli php-pdo php-json

# Salin file konfigurasi nginx untuk PHP
COPY nginx.conf /etc/nginx/nginx.conf


#copy from simple app all file source into nginx default public pages
COPY . usr/share/nginx/html


#expose port 80
EXPOSE 80

