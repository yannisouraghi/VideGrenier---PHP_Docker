FROM php:8.3.7-fpm-alpine

WORKDIR /app
 
# RUN rm /etc/apt/preferences.d/no-debian-php
#     && apt-get update && apt-get install -y \
#         libfreetype6-dev \
#         libjpeg62-turbo-dev \
#         libpng-dev \
#         libmcrypt-dev \
#         libmagickwand-dev \
#         libxml2-dev \
#         zlib1g-dev \
#         unzip \
#         git \
#         msmtp \
#         libzip-dev \
#     && docker-php-ext-install -j$(nproc) iconv \
RUN docker-php-ext-install -j$(nproc) pdo_mysql 
#     && docker-php-ext-install -j$(nproc) soap \
#     && docker-php-ext-install -j$(nproc) bcmath \
#     && docker-php-ext-install -j$(nproc) intl \
#     && docker-php-ext-install -j$(nproc) zip

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/bin/ --filename=composer \
    && php -r "unlink('composer-setup.php');"

# COPY ./php.ini /usr/local/etc/php/

# RUN usermod -u 1000 www-data