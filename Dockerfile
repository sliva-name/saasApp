FROM php:8.2-fpm

# Установка зависимостей и bash
RUN apt-get update && apt-get install -y \
    bash \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Установка PHP-расширений
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        mbstring \
        pdo_mysql \
        pdo_pgsql \
        zip \
        exif \
        pcntl \
        bcmath \
        opcache

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка Node.js + npm
COPY --from=node:18 /usr/local/bin/node /usr/local/bin/
COPY --from=node:18 /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

# Рабочая директория
WORKDIR /var/www

# Копируем entrypoint внутрь контейнера
COPY docker/entrypoint.sh /docker/entrypoint.sh
RUN chmod +x /docker/entrypoint.sh

# Открываем порт PHP-FPM
EXPOSE 9000

# Запуск через entrypoint из docker-compose.yml
CMD ["php-fpm"]
