#!/bin/bash
set -e

echo "📦 Проверка прав..."
mkdir -p /var/www/storage/logs /var/www/bootstrap/cache
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache
chown -R www-data:www-data /var/www/public/storage || true

# Только если ключ не установлен
if ! grep -q 'APP_KEY=base64' .env && [ -f artisan ]; then
    echo "🔐 Генерация ключа..."
    php artisan key:generate
fi

echo "📦 Установка composer-пакетов..."
composer install --no-dev --optimize-autoloader

if [ "$APP_ENV" = "local" ]; then
    echo "🎨 Запуск Vite dev-сервера..."
    npm install
    npm run dev -- --host &  # Запускаем в фоне
else
    echo "🎨 Сборка фронтенда для продакшена..."
    npm install
    npm run build
fi

echo "🚀 Запуск PHP-FPM..."
exec php-fpm
