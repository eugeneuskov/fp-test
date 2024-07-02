FROM php:8.3-fpm-alpine

RUN apk update && apk add --no-cache \
    bash \
    git \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    mariadb-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    mysqli \
    pdo \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    opcache \
    && apk add --no-cache $PHPIZE_DEPS

COPY . /var/www

WORKDIR /var/www

RUN chown -R www-data:www-data /var/www
