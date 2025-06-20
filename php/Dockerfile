# Gunakan base image PHP
FROM php:8.1-fpm

# Install ekstensi PHP yang dibutuhkan
RUN docker-php-ext-install mysqli

# Salin source code aplikasi PHP ke direktori PHP-FPM
COPY html/ /var/www/html/

# Expose port PHP-FPM (biasanya 9000)
EXPOSE 9000

# Jalankan PHP-FPM
CMD ["php-fpm"]
