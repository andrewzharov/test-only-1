FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    curl \
    git \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql \
    && apk del --no-cache libpng-dev libjpeg-turbo-dev freetype-dev

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www

RUN echo "session.save_handler = files" >> /usr/local/etc/php/conf.d/app.ini \
    && echo "session.save_path = /tmp" >> /usr/local/etc/php/conf.d/app.ini

COPY composer.json composer.lock* ./

RUN composer install --no-dev --optimize-autoloader

# Теперь копируем весь исходный код проекта
COPY public/ ./public/
COPY src/ ./src/
COPY assets/ ./assets/