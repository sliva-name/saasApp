# Руководство по развертыванию

## 🚀 Обзор развертывания

Данное руководство описывает процесс развертывания SaaS E-commerce Platform в различных средах: локальной разработке, staging и production.

## 🐳 Docker развертывание

### Требования

- Docker 20.10+
- Docker Compose 2.0+
- Минимум 4GB RAM
- 10GB свободного места

### Быстрый старт

1. **Клонирование репозитория**
```bash
git clone <repository-url>
cd saasApp
```

2. **Настройка переменных окружения**
```bash
cp .env.example .env
# Отредактируйте .env файл под ваши нужды
```

3. **Запуск через Makefile**
```bash
make up
make migrate
make seed
```

4. **Или ручной запуск**
```bash
docker-compose up -d
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

### Docker Compose конфигурация

```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    entrypoint: ["/bin/bash", "/docker/entrypoint.sh"]
    env_file:
      - .env
    ports:
      - "5173:5173" # Vite dev server
    environment:
      - DB_HOST=db
      - DB_PORT=3306
      - MYSQL_ROOT_HOST='%'
    volumes:
      - .:/var/www
      - ./docker/php.ini:/usr/local/etc/php/conf.d/custom.ini
    networks:
      - app-network
    depends_on:
      db:
        condition: service_healthy
    restart: unless-stopped

  webserver:
    image: nginx:alpine
    ports:
      - "8000:80"
    volumes:
      - ./docker/nginx.conf:/etc/nginx/conf.d/default.conf
      - .:/var/www
    networks:
      - app-network
    depends_on:
      - app
    restart: unless-stopped

  meilisearch:
    image: getmeili/meilisearch:v1.8
    ports:
      - "7700:7700"
    environment:
      MEILI_MASTER_KEY: masterKey
    volumes:
      - meili_data:/meili_data
    networks:
      - app-network
    restart: unless-stopped

  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_ROOT_HOST: '%'
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql
    networks:
      - app-network
    healthcheck:
      test: ["CMD", "mysqladmin", "ping", "-h", "localhost"]
      interval: 5s
      timeout: 10s
      retries: 10
    restart: unless-stopped
    command: --default-authentication-plugin=mysql_native_password

volumes:
  mysql_data:
    driver: local
  meili_data:
    driver: local

networks:
  app-network:
    driver: bridge
    name: saasapp-network
```

### Dockerfile

```dockerfile
FROM php:8.2-fpm

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Установка PHP расширений
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка рабочей директории
WORKDIR /var/www

# Копирование файлов проекта
COPY . /var/www

# Установка зависимостей PHP
RUN composer install --no-dev --optimize-autoloader

# Установка зависимостей Node.js
RUN npm install
RUN npm run build

# Настройка прав доступа
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www/storage

# Копирование entrypoint скрипта
COPY docker/entrypoint.sh /docker/entrypoint.sh
RUN chmod +x /docker/entrypoint.sh

EXPOSE 9000

CMD ["php-fpm"]
```

## 🔧 Переменные окружения

### Основные переменные

```env
# Приложение
APP_NAME="SaaS E-commerce"
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=https://your-domain.com

# База данных
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=saasapp
DB_USERNAME=root
DB_PASSWORD=your-secure-password

# Поиск
MEILISEARCH_HOST=http://meilisearch:7700
MEILISEARCH_KEY=your-secure-master-key

# Кеш
CACHE_DRIVER=redis
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=your-redis-password

# Сессии
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Очереди
QUEUE_CONNECTION=redis

# Почта
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"

# Файлы
FILESYSTEM_DISK=public

# Логи
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=warning
```

### Переменные для разных сред

#### Локальная разработка
```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
DB_PASSWORD=password
MEILISEARCH_KEY=masterKey
```

#### Staging
```env
APP_ENV=staging
APP_DEBUG=false
APP_URL=https://staging.your-domain.com
DB_PASSWORD=staging-password
MEILISEARCH_KEY=staging-master-key
```

#### Production
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
DB_PASSWORD=production-secure-password
MEILISEARCH_KEY=production-secure-master-key
```

## 🌐 Nginx конфигурация

### Основная конфигурация

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    # Обработка Laravel маршрутов
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Обработка PHP файлов
    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Запрет доступа к скрытым файлам
    location ~ /\. {
        deny all;
    }

    # Статические файлы
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Gzip сжатие
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types
        text/plain
        text/css
        text/xml
        text/javascript
        application/javascript
        application/xml+rss
        application/json;
}
```

### SSL конфигурация (Production)

```nginx
server {
    listen 443 ssl http2;
    server_name your-domain.com;

    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;

    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;

    # HSTS
    add_header Strict-Transport-Security "max-age=63072000" always;

    # Остальная конфигурация аналогична основной
    root /var/www/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass app:9000;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}

# Редирект с HTTP на HTTPS
server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}
```

## 🔄 CI/CD Pipeline

### GitHub Actions

```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        extensions: mbstring, xml, ctype, iconv, intl, pdo_mysql, gd
        
    - name: Setup Node.js
      uses: actions/setup-node@v3
      with:
        node-version: '18'
        
    - name: Install Composer dependencies
      run: composer install --no-dev --optimize-autoloader
      
    - name: Install NPM dependencies
      run: npm ci
      
    - name: Build assets
      run: npm run build
      
    - name: Run tests
      run: php artisan test
      
    - name: Deploy to server
      uses: appleboy/ssh-action@v0.1.5
      with:
        host: ${{ secrets.HOST }}
        username: ${{ secrets.USERNAME }}
        key: ${{ secrets.SSH_KEY }}
        script: |
          cd /var/www/saasApp
          git pull origin main
          composer install --no-dev --optimize-autoloader
          npm ci
          npm run build
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          sudo systemctl reload nginx
          sudo systemctl reload php8.2-fpm
```

### GitLab CI

```yaml
stages:
  - test
  - build
  - deploy

test:
  stage: test
  image: php:8.2
  services:
    - mysql:8.0
  variables:
    DB_HOST: mysql
    DB_DATABASE: test_db
    DB_USERNAME: root
    DB_PASSWORD: password
  before_script:
    - apt-get update && apt-get install -y git unzip
    - curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
    - composer install
  script:
    - php artisan test

build:
  stage: build
  image: node:18
  script:
    - npm ci
    - npm run build
  artifacts:
    paths:
      - public/build/

deploy:
  stage: deploy
  script:
    - echo "Deploying to production..."
    - ssh user@server "cd /var/www/saasApp && git pull && composer install --no-dev && npm ci && npm run build && php artisan migrate --force"
  only:
    - main
```

## 📊 Мониторинг

### Health Checks

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HealthController extends Controller
{
    public function check(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'search' => $this->checkSearch(),
            'storage' => $this->checkStorage(),
        ];

        $healthy = !in_array(false, $checks, true);

        return response()->json([
            'status' => $healthy ? 'healthy' : 'unhealthy',
            'checks' => $checks,
            'timestamp' => now()->toISOString(),
        ], $healthy ? 200 : 503);
    }

    private function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkCache(): bool
    {
        try {
            Cache::put('health_check', 'ok', 1);
            return Cache::get('health_check') === 'ok';
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkSearch(): bool
    {
        try {
            $client = new \MeiliSearch\Client(config('scout.meilisearch.host'));
            $client->health();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkStorage(): bool
    {
        try {
            return Storage::disk('public')->exists('health.txt') || 
                   Storage::disk('public')->put('health.txt', 'ok');
        } catch (\Exception $e) {
            return false;
        }
    }
}
```

### Логирование

```php
// config/logging.php
return [
    'default' => env('LOG_CHANNEL', 'stack'),

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily', 'slack'],
            'ignore_exceptions' => false,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
        ],
    ],
];
```

## 🔒 Безопасность

### SSL сертификаты

```bash
# Установка Certbot
sudo apt-get update
sudo apt-get install certbot python3-certbot-nginx

# Получение SSL сертификата
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Автоматическое обновление
sudo crontab -e
# Добавить строку:
0 12 * * * /usr/bin/certbot renew --quiet
```

### Firewall настройки

```bash
# UFW настройки
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### Безопасность приложения

```php
// config/session.php
return [
    'driver' => env('SESSION_DRIVER', 'redis'),
    'lifetime' => env('SESSION_LIFETIME', 120),
    'expire_on_close' => false,
    'encrypt' => false,
    'secure' => env('SESSION_SECURE_COOKIE', true),
    'http_only' => true,
    'same_site' => 'lax',
];

// config/cors.php
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['https://your-domain.com'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
```

## 📈 Масштабирование

### Горизонтальное масштабирование

```yaml
# docker-compose.prod.yml
version: '3.8'

services:
  app:
    build: .
    deploy:
      replicas: 3
    environment:
      - DB_HOST=db-cluster
      - REDIS_HOST=redis-cluster
    depends_on:
      - db-cluster
      - redis-cluster

  db-cluster:
    image: mysql:8.0
    deploy:
      replicas: 2
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
    volumes:
      - db_data:/var/lib/mysql

  redis-cluster:
    image: redis:7-alpine
    deploy:
      replicas: 2
    volumes:
      - redis_data:/data

  load-balancer:
    image: nginx:alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx/load-balancer.conf:/etc/nginx/nginx.conf
    depends_on:
      - app

volumes:
  db_data:
  redis_data:
```

### Load Balancer конфигурация

```nginx
# nginx/load-balancer.conf
upstream app_servers {
    least_conn;
    server app1:9000;
    server app2:9000;
    server app3:9000;
}

server {
    listen 80;
    server_name your-domain.com;

    location / {
        proxy_pass http://app_servers;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

## 🔄 Резервное копирование

### Автоматические бэкапы

```bash
#!/bin/bash
# backup.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups"
DB_NAME="saasapp"

# Бэкап базы данных
mysqldump -u root -p$DB_PASSWORD $DB_NAME > $BACKUP_DIR/db_$DATE.sql

# Бэкап файлов
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/storage

# Удаление старых бэкапов (старше 30 дней)
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete

# Отправка уведомления
echo "Backup completed: $DATE" | mail -s "Backup Status" admin@your-domain.com
```

### Cron задача для бэкапов

```bash
# Добавить в crontab
0 2 * * * /var/www/backup.sh
```

## 🚨 Troubleshooting

### Частые проблемы

1. **Ошибка подключения к базе данных**
```bash
# Проверка статуса MySQL
docker-compose exec db mysqladmin ping

# Проверка логов
docker-compose logs db
```

2. **Проблемы с правами доступа**
```bash
# Исправление прав
docker-compose exec app chown -R www-data:www-data /var/www/storage
docker-compose exec app chmod -R 755 /var/www/storage
```

3. **Проблемы с поиском**
```bash
# Перезапуск MeiliSearch
docker-compose restart meilisearch

# Переиндексация
docker-compose exec app php artisan scout:import
```

4. **Проблемы с кешем**
```bash
# Очистка кеша
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
```

### Полезные команды

```bash
# Просмотр логов
make logs

# Вход в контейнер
make shell

# Перезапуск сервисов
make restart

# Проверка статуса
docker-compose ps

# Мониторинг ресурсов
docker stats
```

## 📚 Дополнительные ресурсы

- [Docker Documentation](https://docs.docker.com/)
- [Nginx Documentation](https://nginx.org/en/docs/)
- [Laravel Deployment](https://laravel.com/docs/deployment)
- [Let's Encrypt](https://letsencrypt.org/docs/)
- [GitHub Actions](https://docs.github.com/en/actions)
- [GitLab CI/CD](https://docs.gitlab.com/ee/ci/)
