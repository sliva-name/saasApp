# 🛒 SaaS E-commerce Platform with Theme System

Современная мультитенантная E-commerce платформа с архитектурой "Theme as a Package"

## 🎯 О проекте

Платформа предоставляет полнофункциональное решение для создания интернет-магазинов с уникальными темами для каждого арендатора. Каждая тема является изолированным пакетом с собственными компонентами, стилями и конфигурацией.

### ✨ Ключевые возможности

- 🎨 **Система тем "Theme as a Package"** - полная изоляция и кастомизация
- 🏢 **Мультитенантность** - тысячи магазинов на одной платформе  
- ⚡ **Высокая производительность** - lazy loading и кэширование
- 🛍️ **E-commerce функции** - товары, категории, корзина, поиск
- 🔧 **Гибкая конфигурация** - настройка цветов, макета, функций
- 📱 **Адаптивный дизайн** - поддержка всех устройств
- 🛡️ **Безопасность** - изоляция tenant'ов и валидация тем

## 🚀 Быстрый старт

### Требования

- **PHP:** 8.2+
- **Laravel:** 11+
- **Node.js:** 18+
- **MySQL:** 8.0+
- **Redis:** 6.0+ (рекомендуется)

### Установка

```bash
# 1. Клонируйте репозиторий
git clone https://github.com/your-repo/saas-ecommerce.git
cd saas-ecommerce

# 2. Установите зависимости
composer install
npm install

# 3. Настройте окружение
cp .env.example .env
php artisan key:generate

# 4. Выполните миграции и сидеры
php artisan migrate:fresh --seed

# 5. Постройте фронтенд
npm run build

# 6. Запустите сервер
php artisan serve
```

### Первый запуск

```bash
# Сканирование тем
php artisan theme:manage scan

# Список доступных тем
php artisan theme:manage list

# Создание тестового магазина (опционально)
php artisan tinker
>>> $store = Store::create(['name' => 'Test Store', 'theme_id' => 1])
```

## 📚 Документация

Полная документация доступна в папке `/docs`:

| Документ | Описание |
|----------|----------|
| **[📋 docs/README.md](docs/README.md)** | Обзор документации |
| **[🎨 docs/THEMES.md](docs/THEMES.md)** | Полное руководство по системе тем |
| **[🛠️ docs/THEME_CREATION_GUIDE.md](docs/THEME_CREATION_GUIDE.md)** | Создание новых тем |
| **[🌐 docs/API_REFERENCE.md](docs/API_REFERENCE.md)** | Справочник по API |
| **[🔄 docs/MIGRATION_GUIDE.md](docs/MIGRATION_GUIDE.md)** | Миграция с других систем |
| **[❓ docs/FAQ.md](docs/FAQ.md)** | Часто задаваемые вопросы |

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
