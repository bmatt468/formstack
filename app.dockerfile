FROM php:7.1.20-fpm

RUN apt-get update && apt-get install -y libmcrypt-dev mysql-client libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring mysqli tokenizer xml ctype json

WORKDIR /var/www


