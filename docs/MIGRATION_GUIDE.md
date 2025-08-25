# Migration Guide - Theme System

Руководство по миграции существующих проектов на новую систему тем "Theme as a Package".

## 📋 Содержание

- [Обзор изменений](#обзор-изменений)
- [Подготовка к миграции](#подготовка-к-миграции)
- [Пошаговая миграция](#пошаговая-миграция)
- [Миграция тем](#миграция-тем)
- [Обновление кода](#обновление-кода)
- [Тестирование](#тестирование)
- [Откат изменений](#откат-изменений)
- [FAQ](#faq)

## 🔄 Обзор изменений

### Что изменилось

#### ✅ Новая архитектура
- **До:** Монолитные темы в `resources/views`
- **После:** Модульные темы в `themes/themes/{name}/`

#### ✅ Динамическая загрузка
- **До:** Статические компоненты в `resources/js`
- **После:** Динамическая загрузка через `ThemeLoader`

#### ✅ API-driven конфигурация
- **До:** Жестко заданные настройки
- **После:** Гибкая конфигурация через API

#### ✅ Мультитенантность
- **До:** Одна тема для всех
- **После:** Индивидуальные темы для каждого tenant'а

### Breaking Changes

⚠️ **Критические изменения:**

1. **Структура файлов** - темы перенесены в новую директорию
2. **API эндпоинты** - новые маршруты для управления темами
3. **Vue компоненты** - изменился способ загрузки и регистрации
4. **База данных** - новые таблицы и связи
5. **Конфигурация** - новый формат настроек

## 🛠️ Подготовка к миграции

### 1. Резервное копирование

```bash
# Создайте резервную копию базы данных
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# Создайте резервную копию файлов
tar -czf project_backup_$(date +%Y%m%d_%H%M%S).tar.gz \
  --exclude=node_modules \
  --exclude=vendor \
  --exclude=storage/logs \
  ./
```

### 2. Анализ текущих тем

```bash
# Найдите все Vue компоненты
find resources/js -name "*.vue" -type f

# Найдите все CSS файлы тем
find resources/css -name "*.css" -type f

# Проверьте текущие маршруты
grep -r "Route::" routes/
```

### 3. Документирование зависимостей

Создайте список:
- Используемых Vue компонентов
- CSS классов и переменных
- JavaScript модулей
- Внешних зависимостей

## 🚀 Пошаговая миграция

### Шаг 1: Обновление базы данных

```bash
# Выполните новые миграции
php artisan migrate:fresh --seed

# Или пошагово
php artisan migrate
php artisan db:seed --class=ThemeSeeder
```

### Шаг 2: Установка новых зависимостей

```bash
# Обновите Composer зависимости
composer install

# Обновите NPM зависимости
npm install

# Перестройте ассеты
npm run build
```

### Шаг 3: Создание новой структуры тем

```bash
# Создайте базовую структуру
mkdir -p themes/themes/default/resources/js/{components,pages,composables,styles}
mkdir -p themes/themes/default/resources/assets/{images,fonts}
```

### Шаг 4: Перенос существующих компонентов

#### Автоматический перенос (скрипт)

Создайте `migrate-theme.php`:

```php
<?php
/**
 * Скрипт для автоматического переноса существующих Vue компонентов
 */

$sourceDir = 'resources/js/components';
$targetDir = 'themes/themes/default/resources/js/components';

if (!is_dir($sourceDir)) {
    echo "Source directory not found: $sourceDir\n";
    exit(1);
}

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0755, true);
}

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($sourceDir)
);

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'vue') {
        $relativePath = str_replace($sourceDir . '/', '', $file->getPathname());
        $targetPath = $targetDir . '/' . $relativePath;
        
        // Создаем директорию если не существует
        $targetDirPath = dirname($targetPath);
        if (!is_dir($targetDirPath)) {
            mkdir($targetDirPath, 0755, true);
        }
        
        // Копируем файл
        copy($file->getPathname(), $targetPath);
        echo "Copied: {$file->getPathname()} -> $targetPath\n";
    }
}

echo "Migration completed!\n";
```

Запустите:
```bash
php migrate-theme.php
```

#### Ручной перенос

```bash
# Копируйте компоненты
cp -r resources/js/components/* themes/themes/default/resources/js/components/

# Копируйте страницы (если есть)
cp -r resources/js/pages/* themes/themes/default/resources/js/pages/

# Копируйте стили
cp resources/css/app.css themes/themes/default/resources/js/styles/theme.css
```

### Шаг 5: Обновление компонентов

#### Пример: миграция ProductCard.vue

**Старый компонент (`resources/js/components/ProductCard.vue`):**

```vue
<template>
  <div class="product-card">
    <img :src="product.image" :alt="product.name" />
    <h3>{{ product.name }}</h3>
    <p>${{ product.price }}</p>
  </div>
</template>

<script>
export default {
  name: 'ProductCard',
  props: ['product']
}
</script>

<style scoped>
.product-card {
  border: 1px solid #ddd;
  padding: 1rem;
}
</style>
```

**Новый компонент (`themes/themes/default/resources/js/components/ProductCard.vue`):**

```vue
<template>
  <div class="product-card">
    <div class="product-card__image">
      <router-link :to="`/products/${product.id}`">
        <img 
          :src="product.image || '/placeholder.jpg'" 
          :alt="product.name"
          class="product-card__img"
          loading="lazy"
        />
      </router-link>
    </div>
    
    <div class="product-card__content">
      <h3 class="product-card__title">
        <router-link :to="`/products/${product.id}`">
          {{ product.name }}
        </router-link>
      </h3>
      
      <div class="product-card__price">
        {{ formatPrice(product.price) }}
      </div>
    </div>
  </div>
</template>

<script>
import { useThemeConfig } from '../composables/useThemeConfig.js'

export default {
  name: 'ProductCard',
  props: {
    product: {
      type: Object,
      required: true
    }
  },
  setup() {
    const { themeConfig } = useThemeConfig()
    
    const formatPrice = (price) => {
      return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB'
      }).format(price)
    }
    
    return {
      themeConfig,
      formatPrice
    }
  }
}
</script>

<style scoped>
.product-card {
  background: var(--color-surface);
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s ease;
}

.product-card:hover {
  transform: translateY(-4px);
}

.product-card__image {
  width: 100%;
  height: 200px;
  overflow: hidden;
}

.product-card__img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-card__content {
  padding: 1rem;
}

.product-card__title a {
  color: var(--color-text);
  text-decoration: none;
  font-weight: 600;
}

.product-card__title a:hover {
  color: var(--color-primary);
}

.product-card__price {
  font-weight: 700;
  color: var(--color-primary);
  margin-top: 0.5rem;
}
</style>
```

#### Ключевые изменения:

1. **Добавлена поддержка роутинга** - `router-link`
2. **Использование theme config** - `useThemeConfig()`
3. **CSS переменные** - `var(--color-primary)`
4. **Улучшенная доступность** - `alt`, `loading="lazy"`
5. **Форматирование цены** - `formatPrice()`

### Шаг 6: Создание манифеста темы

Создайте `themes/themes/default/theme.json`:

```json
{
  "name": "Default Theme",
  "package_name": "themes/default",
  "version": "1.0.0",
  "description": "Migrated default theme",
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
      "accent": "#ec4899",
      "background": "#ffffff",
      "surface": "#f8fafc",
      "text": "#1e293b"
    },
    "layout": {
      "header_fixed": true,
      "sidebar_enabled": true,
      "footer_enabled": true
    },
    "typography": {
      "font_family": "Inter, sans-serif",
      "font_size_base": "16px"
    },
    "branding": {
      "site_name": "Demo Store",
      "description": "Your amazing online store"
    }
  }
}
```

### Шаг 7: Создание точки входа

Создайте `themes/themes/default/resources/js/index.js`:

```javascript
// Импорт стилей
import './styles/theme.css'

// Компоненты (автогенерация из существующих)
export const components = {
  TenantApp: () => import('./components/TenantApp.vue'),
  ProductCard: () => import('./components/ProductCard.vue'),
  Header: () => import('./components/Header.vue'),
  Footer: () => import('./components/Footer.vue'),
  Navigation: () => import('./components/Navigation.vue'),
  // ... добавьте все ваши компоненты
}

// Страницы
export const pages = {
  Home: () => import('./pages/Home.vue'),
  Products: () => import('./pages/Products.vue'),
  // ... добавьте все ваши страницы
}

// Маршруты (мигрируйте из routes/web.php)
export const routes = [
  { path: '/', component: pages.Home, name: 'home' },
  { path: '/products', component: pages.Products, name: 'products' },
  // ... добавьте все ваши маршруты
]

// Хуки
export const hooks = {
  beforeMount() {
    console.log('[Default Theme] Migrated theme initializing...')
  },
  mounted() {
    console.log('[Default Theme] Migration completed successfully')
  }
}
```

### Шаг 8: Обновление главного приложения

Обновите `resources/js/tenant.js` для использования новой системы:

```javascript
// Старый код - удалите
// import { createApp } from 'vue'
// import App from './components/App.vue'

// Новый код
import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import ThemeLoader from './themeLoader.js'
import ThemeComponent from './components/ThemeComponent.vue'

const initializeApp = async () => {
  try {
    const themeLoader = new ThemeLoader()
    
    // Определяем тему (может прийти от сервера)
    const themeName = window.tenantConfig?.theme || 'themes/default'
    
    // Загружаем тему
    const theme = await themeLoader.loadTheme(themeName)
    
    // Создаем приложение
    const app = createApp(ThemeComponent, { 
      componentName: 'TenantApp',
      theme: theme
    })
    
    // Создаем роутер с маршрутами темы
    const router = createRouter({
      history: createWebHistory(),
      routes: theme.routes || []
    })
    
    app.use(router)
    app.mount('#app')
    
    console.log('[TenantApp] Migration successful - new theme system active')
    
  } catch (error) {
    console.error('[TenantApp] Migration failed:', error)
    
    // Fallback на старый способ
    const { createApp } = await import('vue')
    const App = await import('./components/LegacyApp.vue')
    
    createApp(App.default).mount('#app')
  }
}

// Запускаем приложение
initializeApp()
```

## 🎨 Миграция тем

### Конвертация CSS в CSS переменные

**Старый CSS:**
```css
.header {
  background: #3b82f6;
  color: white;
  padding: 1rem;
}

.button {
  background: #3b82f6;
  border-radius: 4px;
}
```

**Новый CSS с переменными:**
```css
:root {
  --color-primary: #3b82f6;
  --color-white: #ffffff;
  --padding-base: 1rem;
  --border-radius: 4px;
}

.header {
  background: var(--color-primary);
  color: var(--color-white);
  padding: var(--padding-base);
}

.button {
  background: var(--color-primary);
  border-radius: var(--border-radius);
}
```

### Скрипт для автоконвертации

Создайте `convert-css-variables.php`:

```php
<?php
/**
 * Конвертирует фиксированные значения CSS в переменные
 */

$cssFile = 'themes/themes/default/resources/js/styles/theme.css';
$content = file_get_contents($cssFile);

// Маппинг цветов
$colorMappings = [
    '#3b82f6' => 'var(--color-primary)',
    '#64748b' => 'var(--color-secondary)',
    '#ffffff' => 'var(--color-white)',
    '#000000' => 'var(--color-black)',
];

// Маппинг размеров
$sizeMappings = [
    '1rem' => 'var(--spacing-4)',
    '0.5rem' => 'var(--spacing-2)',
    '2rem' => 'var(--spacing-8)',
];

// Применяем замены
foreach ($colorMappings as $oldValue => $newValue) {
    $content = str_replace($oldValue, $newValue, $content);
}

foreach ($sizeMappings as $oldValue => $newValue) {
    $content = str_replace($oldValue, $newValue, $content);
}

// Добавляем переменные в начало файла
$variables = "
:root {
  --color-primary: #3b82f6;
  --color-secondary: #64748b;
  --color-white: #ffffff;
  --color-black: #000000;
  --spacing-2: 0.5rem;
  --spacing-4: 1rem;
  --spacing-8: 2rem;
}

";

$content = $variables . $content;

file_put_contents($cssFile, $content);
echo "CSS variables migration completed!\n";
```

## 🔧 Обновление кода

### Обновление Vue компонентов

#### Добавление useThemeConfig

В каждый компонент, который использует стили:

```vue
<script>
// Добавьте этот импорт
import { useThemeConfig } from '../composables/useThemeConfig.js'

export default {
  setup() {
    // Добавьте эту строку
    const { themeConfig } = useThemeConfig()
    
    return {
      themeConfig
    }
  }
}
</script>
```

#### Обновление роутинга

Замените старые ссылки:

```vue
<!-- Старый способ -->
<a href="/products">Товары</a>

<!-- Новый способ -->
<router-link to="/products">Товары</router-link>
```

### Обновление API вызовов

**Старый код:**
```javascript
// Прямой вызов к Laravel route
fetch('/products')
```

**Новый код:**
```javascript
// Вызов через новое API
fetch('/api/products')
```

### Обновление состояния

**Старый Vuex:**
```javascript
import { createStore } from 'vuex'

const store = createStore({
  state: {
    products: []
  }
})
```

**Новый Pinia/Composables:**
```javascript
import { ref, reactive } from 'vue'

export function useProducts() {
  const products = ref([])
  const loading = ref(false)
  
  const loadProducts = async () => {
    loading.value = true
    try {
      const response = await fetch('/api/products')
      products.value = await response.json()
    } finally {
      loading.value = false
    }
  }
  
  return {
    products,
    loading,
    loadProducts
  }
}
```

## 🧪 Тестирование

### Чек-лист тестирования

#### ✅ Функциональность
- [ ] Загрузка главной страницы
- [ ] Навигация между страницами
- [ ] Отображение товаров
- [ ] Поиск товаров
- [ ] Фильтрация категорий
- [ ] Корзина покупок
- [ ] Регистрация/вход

#### ✅ Внешний вид
- [ ] CSS стили применяются корректно
- [ ] Адаптивность на разных экранах
- [ ] Цвета соответствуют теме
- [ ] Шрифты загружаются
- [ ] Иконки отображаются

#### ✅ Производительность
- [ ] Быстрая загрузка компонентов
- [ ] Нет ошибок в консоли
- [ ] API запросы работают
- [ ] Кэширование функционирует

### Автоматическое тестирование

Создайте `tests/Feature/ThemeMigrationTest.php`:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Theme;
use App\Models\Store;

class ThemeMigrationTest extends TestCase
{
    public function test_theme_loads_correctly()
    {
        $response = $this->get('/api/tenant/active-theme');
        
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'package_name',
                     'config' => [
                         'colors',
                         'layout',
                         'branding'
                     ]
                 ]);
    }
    
    public function test_components_load_correctly()
    {
        $response = $this->get('/api/theme/components');
        
        $response->assertStatus(200)
                 ->assertJsonHasKey('TenantApp')
                 ->assertJsonHasKey('ProductCard')
                 ->assertJsonHasKey('Header');
    }
    
    public function test_legacy_routes_redirect()
    {
        // Тест редиректов со старых маршрутов
        $response = $this->get('/products');
        $response->assertStatus(200);
    }
}
```

Запустите тесты:
```bash
php artisan test --filter=ThemeMigrationTest
```

### Ручное тестирование

```bash
# Тест 1: Проверьте загрузку темы
curl -s http://localhost:8000/api/tenant/active-theme | jq .

# Тест 2: Проверьте компоненты
curl -s http://localhost:8000/api/theme/components | jq .

# Тест 3: Проверьте API товаров
curl -s http://localhost:8000/api/products | jq '.data[0]'
```

## ⏪ Откат изменений

Если миграция не удалась, выполните откат:

### 1. Восстановление базы данных

```bash
# Восстановите из резервной копии
mysql -u username -p database_name < backup_YYYYMMDD_HHMMSS.sql

# Или откатите миграции
php artisan migrate:rollback --step=5
```

### 2. Восстановление файлов

```bash
# Восстановите файлы из архива
tar -xzf project_backup_YYYYMMDD_HHMMSS.tar.gz

# Или вручную
git checkout HEAD~1 -- resources/js/
git checkout HEAD~1 -- routes/
```

### 3. Очистка кэша

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
npm run build
```

## ❓ FAQ

### Q: Можно ли выполнить частичную миграцию?

**A:** Да, система поддерживает gradual migration:

1. Мигрируйте по одному компоненту за раз
2. Используйте fallback на старые компоненты
3. Постепенно переносите функциональность

### Q: Что делать с кастомными плагинами?

**A:** Кастомные плагины нужно адаптировать:

```javascript
// Старый плагин
Vue.use(MyPlugin)

// Новый подход
export const plugins = {
  MyPlugin: () => import('./plugins/MyPlugin.js')
}
```

### Q: Как мигрировать Vuex store?

**A:** Рекомендуется переход на Composition API:

```javascript
// Старый Vuex
const store = useStore()
const products = store.state.products

// Новый composable
const { products } = useProducts()
```

### Q: Сохранятся ли SEO настройки?

**A:** Да, при правильной миграции:

1. Маршруты остаются теми же
2. Meta теги переносятся в новые компоненты
3. Структурированные данные сохраняются

### Q: Что делать с performance проблемами?

**A:** Оптимизируйте загрузку:

```javascript
// Lazy loading компонентов
export const components = {
  ProductCard: () => import(/* webpackChunkName: "product-card" */ './components/ProductCard.vue')
}

// Предзагрузка критических компонентов
export const hooks = {
  beforeMount() {
    // Предзагрузить важные компоненты
    import('./components/Header.vue')
    import('./components/Footer.vue')
  }
}
```

---

## 📞 Поддержка

Если у вас возникли проблемы с миграцией:

1. **Проверьте логи:** `storage/logs/laravel.log`
2. **Консоль браузера:** Developer Tools → Console
3. **Документация:** [THEMES.md](THEMES.md)
4. **Обратитесь к команде:** support@yourcompany.com

**Удачной миграции!** 🚀
