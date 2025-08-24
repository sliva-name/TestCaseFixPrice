FROM php:8.1-fpm

# Установка зависимостей
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql zip \
    && rm -rf /var/lib/apt/lists/*

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Рабочая директория
WORKDIR /var/www/html

# Копирование composer файлов сначала для кэширования слоя
COPY composer.json composer.lock ./

# Установка зависимостей (включая dev для разработки)
RUN composer install --optimize-autoloader --no-scripts

# Копирование и настройка entrypoint скрипта сначала
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Копирование остальных файлов
COPY . .

# Выполнение post-install скриптов и генерация autoload
RUN composer dump-autoload --optimize \
    && composer run-script post-install-cmd || true

# Создание необходимых директорий
RUN mkdir -p runtime web/assets

# Права на запись
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/runtime \
    && chmod -R 775 /var/www/html/web/assets

EXPOSE 9000

ENTRYPOINT ["/bin/bash", "/usr/local/bin/docker-entrypoint.sh"]