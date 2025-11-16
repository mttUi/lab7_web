FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    && docker-php-ext-install sockets

# Установка Composer с повторами при ошибках
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php

WORKDIR /var/www/html

# Копируем только composer.json сначала для кэширования зависимостей
COPY composer.json ./

# Устанавливаем зависимости с повторами
RUN composer install --no-dev --optimize-autoloader || \
    composer install --no-dev --optimize-autoloader || \
    composer install --no-dev --optimize-autoloader

# Копируем остальные файлы
COPY ./www /var/www/html

CMD ["php-fpm"]