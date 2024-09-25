FROM php:8.3.7-fpm-alpine

WORKDIR /app
 
RUN docker-php-ext-install -j$(nproc) pdo_mysql

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/bin/ --filename=composer \
    && php -r "unlink('composer-setup.php');"

