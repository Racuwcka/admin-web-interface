# Используем официальный образ PHP 8.2.4 FPM
FROM php:8.2.4-fpm

# Устанавливаем зависимости и PECL
RUN apt-get update && apt-get install -y \
    libssl-dev \
    libmagickwand-dev \
    libzip-dev \
    zip \
    unzip \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && docker-php-ext-install pdo_mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Копируем файлы приложения в контейнер
COPY . /var/www/html

# Устанавливаем права для файлов
RUN chown -R www-data:www-data /var/www/html

# Указываем рабочую директорию
WORKDIR /var/www/html

# Открываем порт 9000
EXPOSE 9000

# Запускаем PHP-FPM
CMD ["php-fpm"]
