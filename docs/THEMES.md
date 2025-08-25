# Система тем "Theme as a Package"

Полнофункциональная система тем для SaaS E-commerce платформы с архитектурой "Theme as a Package", обеспечивающая полную изоляцию и динамическую загрузку компонентов.

## 📋 Содержание

- [Обзор архитектуры](#обзор-архитектуры)
- [Структура темы](#структура-темы)
- [Backend компоненты](#backend-компоненты)
- [Frontend компоненты](#frontend-компоненты)
- [API эндпоинты](#api-эндпоинты)
- [Создание новой темы](#создание-новой-темы)
- [Управление темами](#управление-темами)
- [Развертывание](#развертывание)
- [Troubleshooting](#troubleshooting)

## 🏗️ Обзор архитектуры

### Принципы

1. **Полная изоляция** - каждая тема как отдельный пакет
2. **Динамическая загрузка** - компоненты загружаются по требованию
3. **Мультитенантность** - разные темы для разных магазинов
4. **Fallback система** - автоматическое переключение на default при ошибках
5. **API-driven** - управление через REST API

### Компоненты системы

```
┌─ Backend ─────────────────────┐    ┌─ Frontend ─────────────────────┐
│ • ThemeManager                │    │ • ThemeLoader (composable)     │
│ • ThemeServiceProvider        │    │ • ThemeComponent (динамический) │
│ • ThemeController (API)       │    │ • useThemeConfig (composable)  │
│ • ThemeConfig (модель)        │    │ • tenant.js (инициализация)   │
│ • ThemeAssetsMiddleware       │    │                                │
└───────────────────────────────┘    └────────────────────────────────┘
```

## 📁 Структура темы

Каждая тема располагается в директории `themes/themes/{theme_name}/`:

```
themes/themes/default/
├── theme.json              # Манифест темы
├── resources/
│   ├── js/
│   │   ├── components/     # Vue компоненты
│   │   │   ├── TenantApp.vue
│   │   │   ├── Header.vue
│   │   │   ├── Footer.vue
│   │   │   ├── ProductCard.vue
│   │   │   └── ...
│   │   ├── pages/          # Страницы
│   │   │   ├── Home.vue
│   │   │   ├── Products.vue
│   │   │   └── ...
│   │   ├── composables/    # Vue composables
│   │   │   └── useThemeConfig.js
│   │   ├── styles/         # CSS стили
│   │   │   └── theme.css
│   │   └── index.js        # Точка входа темы
│   └── assets/             # Статические ресурсы
│       ├── images/
│       └── fonts/
└── README.md               # Документация темы
```

### Манифест темы (theme.json)

```json
{
  "name": "Default Theme",
  "package_name": "themes/default",
  "version": "1.0.0",
  "description": "Основная тема для интернет-магазина",
  "author": "Your Company",
  "features": [
    "products",
    "categories", 
    "search",
    "cart",
    "checkout",
    "user-account"
  ],
  "config_schema": {
    "colors": {
      "primary": "#3b82f6",
      "secondary": "#64748b",
      "accent": "#ec4899"
    },
    "layout": {
      "header_fixed": true,
      "sidebar_enabled": true
    }
  },
  "dependencies": {
    "vue": "^3.0.0",
    "vue-router": "^4.0.0"
  },
  "preview_image": "/assets/preview.jpg"
}
```

## 🔧 Backend компоненты

### ThemeManager

Центральный сервис для управления темами:

```php
// app/Services/ThemeManager.php
class ThemeManager
{
    public function getActiveTheme(string $tenantId): ?Theme
    public function setThemeForTenant(string $tenantId, int $themeId): bool
    public function scanForThemes(): array
    public function validateTheme(string $themePath): bool
}
```

### ThemeController API

Предоставляет REST API для работы с темами:

```php
// Основные эндпоинты
GET /api/tenant/active-theme      // Получить активную тему тенанта
GET /api/theme/components         // Получить компоненты текущей темы  
GET /api/themes/{packageName}     // Детальная информация о теме
```

### Models

**Theme** (центральная БД):
```php
class Theme extends Model
{
    protected $fillable = [
        'name', 'package_name', 'description', 
        'version', 'author', 'config_schema', 
        'is_active', 'preview_image'
    ];
}
```

**Store** (tenant БД):
```php 
class Store extends Model
{
    // Связь с темой через theme_id
    public function theme(): BelongsTo
    {
        return $this->belongsTo(Theme::class);
    }
}
```

## ⚡ Frontend компоненты

### ThemeLoader

Основной composable для загрузки тем:

```javascript
// resources/js/themeLoader.js
class ThemeLoader {
  async loadTheme(packageName)      // Загрузить тему
  async loadComponent(name, path)   // Загрузить компонент
  async loadThemeStyles(theme)      // Загрузить стили
  async loadThemeRoutes(theme)      // Загрузить маршруты
}
```

### ThemeComponent

Динамический компонент для рендера theme-specific компонентов:

```vue
<!-- resources/js/components/ThemeComponent.vue -->
<template>
  <component 
    :is="themeComponent" 
    v-bind="$attrs" 
    v-on="$listeners"
  />
</template>
```

### useThemeConfig

Composable для работы с конфигурацией темы:

```javascript
// themes/themes/default/resources/js/composables/useThemeConfig.js
export function useThemeConfig() {
  const themeConfig = reactive({
    colors: { primary: '#3b82f6', ... },
    layout: { header_fixed: true, ... },
    branding: { site_name: 'Demo Store', ... }
  })
  
  return { themeConfig, updateConfig, resetConfig }
}
```

## 🌐 API эндпоинты

### Получение активной темы

```http
GET /api/tenant/active-theme
```

**Ответ:**
```json
{
  "package_name": "themes/default",
  "config": {
    "colors": {
      "primary": "#3b82f6",
      "secondary": "#64748b"
    },
    "layout": {
      "header_fixed": true,
      "sidebar_enabled": true
    },
    "branding": {
      "site_name": "Demo Store",
      "description": "Your amazing online store"
    }
  }
}
```

### Получение компонентов темы

```http
GET /api/theme/components
```

**Ответ:**
```json
{
  "TenantApp": "/themes/themes/default/resources/js/components/TenantApp.vue",
  "ProductCard": "/themes/themes/default/resources/js/components/ProductCard.vue",
  "Header": "/themes/themes/default/resources/js/components/Header.vue",
  "Home": "/themes/themes/default/resources/js/pages/Home.vue",
  "Products": "/themes/themes/default/resources/js/pages/Products.vue"
}
```

### Информация о теме

```http
GET /api/themes/{packageName}
```

**Ответ:**
```json
{
  "id": 1,
  "name": "Default Theme", 
  "package_name": "themes/default",
  "description": "Основная тема для интернет-магазина",
  "version": "1.0.0",
  "author": "Your Company",
  "config_schema": {...},
  "is_active": true,
  "preview_image": "/themes/default/assets/preview.jpg"
}
```

## 🎨 Создание новой темы

### 1. Через Artisan команду

```bash
php artisan make:theme "My Custom Theme"
```

Эта команда создаст:
- Базовую структуру папок
- Файл `theme.json` с настройками
- Заготовки Vue компонентов
- Базовые стили CSS

### 2. Ручное создание

1. **Создайте директорию темы:**
```bash
mkdir -p themes/themes/my-theme/resources/js/{components,pages,composables,styles}
```

2. **Создайте манифест `theme.json`:**
```json
{
  "name": "My Custom Theme",
  "package_name": "themes/my-theme", 
  "version": "1.0.0",
  "description": "Кастомная тема",
  "features": ["products", "cart"],
  "config_schema": {
    "colors": {
      "primary": "#ff6b6b"
    }
  }
}
```

3. **Создайте точку входа `index.js`:**
```javascript
export const components = {
  TenantApp: () => import('./components/TenantApp.vue'),
  Header: () => import('./components/Header.vue')
}

export const routes = [
  { path: '/', component: () => import('./pages/Home.vue') }
]

export const hooks = {
  beforeMount() {
    console.log('[My Theme] Initializing...')
  },
  mounted() {
    console.log('[My Theme] Mounted successfully')
  }
}
```

4. **Зарегистрируйте тему в БД:**
```php
Theme::create([
    'name' => 'My Custom Theme',
    'package_name' => 'themes/my-theme',
    'description' => 'Кастомная тема',
    'version' => '1.0.0',
    'is_active' => true
]);
```

## 🛠️ Управление темами

### Artisan команды

```bash
# Сканирование новых тем в файловой системе
php artisan theme:manage scan

# Список всех тем
php artisan theme:manage list  

# Создание новой темы
php artisan make:theme "Theme Name"
```

### Через админ-панель (MoonShine)

1. Перейдите в **Admin → Themes**
2. **Создать тему** - добавить новую запись
3. **Редактировать** - изменить настройки
4. **Активировать/Деактивировать** - включить/выключить тему

### Привязка темы к магазину

```php
// В коде
$store = Store::find($storeId);
$store->theme_id = $themeId;
$store->save();

// Через API
PUT /api/stores/{id}
{
  "theme_id": 2
}
```

## 🚀 Развертывание

### 1. Установка системы тем

```bash
# Выполнить миграции
php artisan migrate:fresh --seed

# Сканировать темы
php artisan theme:manage scan
```

### 2. Настройка Vite

Убедитесь что `vite.config.js` настроен для обработки динамических импортов:

```javascript
export default defineConfig({
  build: {
    rollupOptions: {
      external: [], 
      output: {
        manualChunks: undefined
      }
    }
  },
  server: {
    fs: {
      allow: ['..', './themes']
    }
  }
})
```

### 3. Настройка веб-сервера

Для production убедитесь что статические файлы тем доступны:

```nginx
# Nginx конфигурация
location /themes/ {
    alias /path/to/your/app/themes/;
    expires 1y;
    add_header Cache-Control "public, immutable";
}
```

## 🔍 Troubleshooting

### Проблема: Тема не загружается (404 ошибки)

**Симптомы:**
```
GET /api/themes/themes%2Fminimal 404 (Not Found)
[ThemeLoader] Failed to load theme: Error: Theme not found
```

**Решение:**
1. Проверьте что тема существует в БД:
```sql
SELECT * FROM themes WHERE package_name = 'themes/minimal';
```

2. Проверьте что файлы темы существуют:
```bash
ls -la themes/themes/minimal/
```

3. Перерегистрируйте тему:
```bash
php artisan theme:manage scan
```

### Проблема: Компоненты не найдены

**Симптомы:**
```
[ThemeLoader] Failed to load component: TypeError: Failed to fetch
```

**Решение:**
1. Проверьте пути компонентов в API:
```bash
curl http://your-domain.com/api/theme/components
```

2. Убедитесь что файлы компонентов существуют по указанным путям

3. Проверьте права доступа к файлам

### Проблема: Стили не применяются

**Симптомы:** Тема загружается, но выглядит неправильно

**Решение:**
1. Проверьте что CSS файл подключается:
```javascript
// В браузере DevTools → Network
// Должен быть запрос к theme.css
```

2. Убедитесь что CSS правила не перекрываются:
```css
/* Используйте специфичные селекторы */
.theme-minimal .header { ... }
```

3. Проверьте что класс темы добавлен к body:
```html
<body class="theme-minimal">
```

### Проблема: Ошибки в консоли браузера

**Debugging:**

1. Включите детальные логи в `tenant.js`:
```javascript
const DEBUG = true; // добавьте эту переменную
```

2. Проверьте Laravel логи:
```bash
tail -f storage/logs/laravel.log
```

3. Используйте Vue DevTools для отладки компонентов

### Проблема: Tenant не может найти свою тему

**Решение:**
1. Проверьте связь Store → Theme:
```php
$store = tenant();
dd($store->theme_id, $store->theme);
```

2. Убедитесь что theme_id установлен правильно в таблице stores

3. Проверьте что тема активна:
```sql
SELECT * FROM themes WHERE id = ? AND is_active = 1;
```

## 📝 Лучшие практики

### 1. Именование

- **Темы:** используйте kebab-case (`my-awesome-theme`)
- **Компоненты:** PascalCase (`ProductCard.vue`)
- **CSS классы:** BEM методология (`.product-card__title`)

### 2. Производительность

- Используйте **lazy loading** для компонентов
- **Кэшируйте** загруженные компоненты
- **Минифицируйте** CSS и JS в production

### 3. Совместимость

- Следуйте **семантическому версионированию**
- Документируйте **breaking changes**
- Тестируйте темы на **разных браузерах**

### 4. Безопасность

- **Валидируйте** пользовательский ввод в темах
- **Экранируйте** динамический контент
- **Ограничивайте** доступ к чувствительным API

---

## 📚 Дополнительные ресурсы

- [Руководство по созданию темы](THEME_CREATION_GUIDE.md)
- [API Reference](API_REFERENCE.md)  
- [Migration Guide](MIGRATION_GUIDE.md)
- [FAQ](FAQ.md)