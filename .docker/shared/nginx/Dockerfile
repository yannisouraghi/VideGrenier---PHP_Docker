# Stage for installing composer dependencies
FROM php:8.3.7-fpm-alpine AS composer

WORKDIR /app

COPY . .

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/bin/ --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && composer install

# Stage to compile .scss files to .css
FROM node:20-alpine AS scss-compiler

WORKDIR /app

COPY . .

RUN npm install sass@^1.77.2 -g && \
    npx sass /app/style:/app/public/style

# Runtime stage
FROM nginx:1.27.1-alpine AS runtime

WORKDIR /app

RUN usermod -u 1000 nginx

COPY --from=composer --chown=nginx:nginx /app /app
COPY --from=scss-compiler --chown=nginx:nginx /app/public/style /app/public/style
COPY --chown=nginx:nginx . /etc/nginx/templates

