FROM php:8.1-apache

RUN apt-get update && apt-get dist-upgrade
RUN apt-get install -y git zip unzip vim default-mysql-client libfreetype6-dev libjpeg62-turbo-dev libpng-dev
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd
RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer && chmod +x /usr/local/bin/composer
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"