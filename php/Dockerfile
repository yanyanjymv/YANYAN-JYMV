# Gunakan base image PHP
FROM php:8.0.30-fpm 

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libxml2-dev \
    libmbstring-dev \
    zlib1g-dev \
    libicu-dev \
    libmcrypt-dev \
    && rm -rf /var/lib/apt/lists/*

# Install ekstensi PHP yang dibutuhkan
RUN docker-php-ext-install mysqli php-xml php-mbstring

# Salin source code aplikasi PHP ke direktori PHP-FPM
COPY php/html/ /var/www/html/

# Expose port PHP-FPM (biasanya 9000)
EXPOSE 9000

# Jalankan PHP-FPM
CMD ["php-fpm"]
