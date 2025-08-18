# SaaS E-commerce Platform - Документация

## 📋 Обзор проекта

Это многоарендное SaaS приложение для создания интернет-магазинов, построенное на Laravel 12 с использованием мультитенантной архитектуры. Каждый клиент получает собственный интернет-магазин с уникальным доменом и базой данных.

## 🏗️ Архитектура

### Мультитенантность
- **Пакет**: `stancl/tenancy` v3.9
- **Модель тенанта**: `App\Models\Store` (наследует от `BaseTenant`)
- **Изоляция**: Каждый магазин имеет отдельную базу данных
- **Домены**: Поддержка кастомных доменов и субдоменов

### Технологический стек
- **Backend**: Laravel 12, PHP 8.2+
- **Frontend**: Vue.js 3, Tailwind CSS, Alpine.js
- **База данных**: MySQL 8.0
- **Поиск**: MeiliSearch
- **Админ-панель**: MoonShine
- **Медиа**: Spatie Media Library
- **Разрешения**: Spatie Permission
- **Корзина**: Shopping Cart

## 🚀 Быстрый старт

### Требования
- Docker и Docker Compose
- Node.js 18+
- PHP 8.2+

### Установка и запуск

1. **Клонирование и настройка**
```bash
git clone <repository>
cd saasApp
cp .env.example .env
```

2. **Запуск через Docker**
```bash
make up
make migrate
make seed
```

3. **Или ручная установка**
```bash
composer install
npm install
php artisan key:generate
php artisan migrate
php artisan db:seed
npm run dev
```

### Доступные команды Makefile
```bash
make help          # Справка по командам
make up            # Запуск контейнеров
make down          # Остановка контейнеров
make build         # Пересборка контейнеров
make shell         # Вход в контейнер приложения
make migrate       # Выполнение миграций
make seed          # Заполнение тестовыми данными
make npm-dev       # Запуск dev сервера
```

## 📁 Структура проекта

### Основные директории

```
saasApp/
├── app/                    # Основная логика приложения
│   ├── Console/           # Artisan команды
│   ├── Domains/           # Доменная логика
│   ├── Helpers/           # Вспомогательные классы
│   ├── Http/              # HTTP слой (контроллеры, middleware)
│   ├── Listeners/         # Слушатели событий
│   ├── Models/            # Eloquent модели
│   ├── MoonShine/         # Админ-панель
│   ├── Providers/         # Сервис-провайдеры
│   └── Services/          # Бизнес-логика
├── config/                # Конфигурационные файлы
├── database/              # Миграции, сидеры, фабрики
├── resources/             # Frontend ресурсы
│   ├── js/               # Vue.js компоненты
│   └── views/            # Blade шаблоны
├── routes/                # Маршруты
└── storage/               # Файлы приложения
```

## 🏪 Модели данных

### Основные сущности

#### Store (Магазин)
- **Файл**: `app/Models/Store.php`
- **Назначение**: Основная модель тенанта
- **Связи**: `owner()`, `theme()`, `domains()`
- **Поля**: `user_id`, `plan`, `slug`, `theme_id`

#### Product (Товар)
- **Файл**: `app/Models/Product.php`
- **Назначение**: Товары магазина
- **Особенности**: Поиск через Scout, медиа-библиотека
- **Поля**: `name`, `slug`, `description`, `price`, `stock`, `category_id`

#### Category (Категория)
- **Файл**: `app/Models/Category.php`
- **Назначение**: Категории товаров
- **Особенности**: Древовидная структура

#### User (Пользователь)
- **Файл**: `app/Models/User.php`
- **Назначение**: Пользователи системы
- **Особенности**: Может владеть несколькими магазинами

## 🌐 Маршрутизация

### Центральные маршруты (`routes/web.php`)
- Создание магазинов
- Управление аккаунтом
- Админ-панель

### Тенантские маршруты (`routes/tenant.php`)
- Каталог товаров
- Детали товара
- Поиск
- Корзина
- Аутентификация

### API маршруты
- `/api/products` - получение товаров
- `/api/products/{slug}` - детали товара
- `/api/categories/{slug}` - товары категории
- `/api/me` - текущий пользователь

## 🔍 Поиск и фильтрация

### Поисковый движок
- **MeiliSearch** для быстрого поиска
- **Laravel Scout** для интеграции
- **ProductSearchService** для бизнес-логики

### Фильтрация
- По категориям
- По цене (диапазон)
- По наличию на складе
- Фасеточная фильтрация

## 🎨 Frontend

### Vue.js компоненты
- `TenantApp.vue` - основной компонент SPA
- `ProductCatalog.vue` - каталог товаров
- `ProductDetail.vue` - детали товара
- `SearchBar.vue` - поисковая строка
- `FiltersSidebar.vue` - фильтры
- `CartPage.vue` - корзина

### Стилизация
- **Tailwind CSS** с кастомной конфигурацией
- **Адаптивный дизайн**
- **Современные анимации**
- **Компонентный подход**

## 🔧 Администрирование

### MoonShine админ-панель
- **URL**: `/moonshine`
- **Ресурсы**: Products, Categories, Users, Stores
- **Особенности**: Медиа-библиотека, древовидные категории

### Artisan команды
```bash
php artisan tenants:migrate    # Миграции для тенантов
php artisan scout:import       # Импорт в поиск
php artisan setup:tenant       # Настройка тенанта
```

## 🗄️ База данных

### Структура
- **Центральная БД**: Пользователи, магазины, планы
- **Тенантские БД**: Товары, категории, заказы (изолированы)

### Миграции
- `database/migrations/` - центральные миграции
- `database/migrations/tenant/` - тенантские миграции

## 🔐 Безопасность

### Аутентификация
- Laravel Breeze
- Поддержка регистрации/входа
- Восстановление пароля

### Разрешения
- Spatie Permission
- Роли и разрешения
- Защита маршрутов

### Мультитенантность
- Изоляция данных
- Middleware для проверки доступа
- CSRF защита

## 🚀 Развертывание

### Docker
```bash
docker-compose up -d
```

### Переменные окружения
```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=saasapp
DB_USERNAME=root
DB_PASSWORD=password

MEILISEARCH_HOST=http://meilisearch:7700
MEILISEARCH_KEY=masterKey
```

## 📊 Мониторинг и логи

### Логи
- `storage/logs/` - логи приложения
- `docker-compose logs` - логи контейнеров

### Отладка
- Laravel Telescope (если установлен)
- Xdebug в Docker

## 🔄 Разработка

### Рабочий процесс
1. Создание миграций
2. Обновление моделей
3. Создание контроллеров
4. Настройка маршрутов
5. Разработка Vue компонентов
6. Тестирование

### Полезные команды
```bash
make shell              # Вход в контейнер
php artisan tinker      # REPL для тестирования
npm run dev            # Разработка фронтенда
php artisan test       # Запуск тестов
```

## 📚 Дополнительные ресурсы

- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/guide/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [MoonShine Documentation](https://moonshine.cutcode.dev/)
- [Tenancy for Laravel](https://tenancyforlaravel.com/)

## 🤝 Поддержка

При возникновении вопросов или проблем:
1. Проверьте логи в `storage/logs/`
2. Изучите документацию пакетов
3. Создайте issue в репозитории

---

*Документация обновлена: $(date)*
