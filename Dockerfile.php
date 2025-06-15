FROM php:fpm-alpine

RUN apk update && apk add --no-cache php php-fpm php-mysqli && rm -rf /var/cache/apk/*

EXPOSE 9000

# Start php-fpm
CMD ["php-fpm-alpine"]
