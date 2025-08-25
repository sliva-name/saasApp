# FAQ - Theme System

Часто задаваемые вопросы и ответы по системе тем "Theme as a Package".

## 📋 Содержание

- [Общие вопросы](#общие-вопросы)
- [Установка и настройка](#установка-и-настройка)
- [Разработка тем](#разработка-тем)
- [Производительность](#производительность)
- [Безопасность](#безопасность)
- [Troubleshooting](#troubleshooting)
- [Миграция](#миграция)

## 🔍 Общие вопросы

### Q: Что такое "Theme as a Package"?

**A:** Это архитектурный подход, где каждая тема представляет собой изолированный пакет со своими компонентами, стилями, конфигурацией и логикой. Основные преимущества:

- ✅ **Полная изоляция** - темы не влияют друг на друга
- ✅ **Динамическая загрузка** - компоненты загружаются по требованию
- ✅ **Гибкая конфигурация** - каждая тема может иметь свои настройки
- ✅ **Простая разработка** - создание новых тем не требует изменения основного кода

### Q: Чем эта система отличается от обычных тем?

**A:** Сравнение подходов:

| Критерий | Обычные темы | Theme as a Package |
|----------|--------------|-------------------|
| **Изоляция** | Минимальная | Полная |
| **Загрузка** | Статическая | Динамическая |
| **Конфигурация** | Жесткая | Гибкая |
| **Мультитенантность** | Сложная | Встроенная |
| **Разработка** | Монолитная | Модульная |

### Q: Подходит ли это для больших проектов?

**A:** Да, система специально разработана для масштабируемых SaaS платформ:

- 🏢 **Enterprise ready** - поддержка тысяч tenant'ов
- ⚡ **Высокая производительность** - lazy loading и кэширование
- 🔧 **DevOps friendly** - простое развертывание и обновление
- 🧪 **Тестируемость** - изолированное тестирование тем

## ⚙️ Установка и настройка

### Q: Какие системные требования?

**A:** Минимальные требования:

```bash
# Backend
PHP: 8.2+
Laravel: 11+
MySQL: 8.0+ или PostgreSQL: 13+
Redis: 6.0+ (рекомендуется)

# Frontend
Node.js: 18+
Vue.js: 3.0+
Vite: 4.0+
```

### Q: Как установить систему тем в существующий проект?

**A:** Пошаговая установка:

```bash
# 1. Выполните миграции
php artisan migrate:fresh --seed

# 2. Опубликуйте конфигурации
php artisan vendor:publish --provider="App\Providers\ThemeServiceProvider"

# 3. Создайте базовую структуру
php artisan theme:manage scan

# 4. Пересоберите фронтенд
npm install && npm run build
```

Подробнее: [Migration Guide](MIGRATION_GUIDE.md)

### Q: Можно ли использовать с другими CSS фреймворками?

**A:** Да, система не привязана к конкретному CSS фреймворку:

```css
/* Tailwind CSS */
.btn-primary {
  @apply bg-blue-500 text-white px-4 py-2 rounded;
}

/* Bootstrap */
.btn-primary {
  background-color: var(--color-primary);
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 0.25rem;
}

/* Custom CSS */
.btn-primary {
  background: var(--color-primary);
  color: var(--color-white);
  padding: var(--spacing-2) var(--spacing-4);
}
```

### Q: Поддерживается ли TypeScript?

**A:** Да, полная поддержка TypeScript:

```typescript
// themes/my-theme/resources/js/types.ts
export interface ThemeConfig {
  colors: {
    primary: string
    secondary: string
  }
  layout: {
    headerFixed: boolean
  }
}

// themes/my-theme/resources/js/composables/useThemeConfig.ts
import { reactive, Ref } from 'vue'
import type { ThemeConfig } from '../types'

export function useThemeConfig(): {
  themeConfig: Ref<ThemeConfig>
  updateConfig: (path: string, value: any) => void
} {
  // ...
}
```

## 🎨 Разработка тем

### Q: Как создать новую тему?

**A:** Используйте Artisan команду:

```bash
# Создание новой темы
php artisan make:theme "My Awesome Theme"

# Результат:
# themes/themes/my-awesome-theme/
# ├── theme.json
# ├── resources/js/
# │   ├── components/
# │   ├── pages/
# │   ├── composables/
# │   └── index.js
# └── README.md
```

### Q: Как организовать компоненты в теме?

**A:** Рекомендуемая структура:

```
themes/themes/my-theme/resources/js/
├── components/           # UI компоненты
│   ├── base/            # Базовые компоненты
│   │   ├── Button.vue
│   │   ├── Input.vue
│   │   └── Modal.vue
│   ├── layout/          # Layout компоненты
│   │   ├── Header.vue
│   │   ├── Footer.vue
│   │   └── Sidebar.vue
│   └── features/        # Функциональные компоненты
│       ├── ProductCard.vue
│       ├── CartButton.vue
│       └── SearchBar.vue
├── pages/               # Страницы
│   ├── Home.vue
│   ├── Products.vue
│   └── Cart.vue
├── composables/         # Vue composables
│   ├── useThemeConfig.js
│   ├── useCart.js
│   └── useAuth.js
└── styles/              # Стили
    ├── theme.css
    ├── components.css
    └── utilities.css
```

### Q: Как сделать тему конфигурируемой?

**A:** Используйте schema в `theme.json`:

```json
{
  "config_schema": {
    "colors": {
      "primary": {
        "type": "color",
        "default": "#3b82f6",
        "label": "Основной цвет",
        "description": "Используется для кнопок и ссылок"
      },
      "secondary": {
        "type": "color", 
        "default": "#64748b",
        "label": "Вторичный цвет"
      }
    },
    "layout": {
      "header_fixed": {
        "type": "boolean",
        "default": true,
        "label": "Фиксированный заголовок"
      },
      "max_width": {
        "type": "select",
        "options": ["1200px", "1400px", "1600px"],
        "default": "1200px",
        "label": "Максимальная ширина"
      }
    },
    "features": {
      "dark_mode": {
        "type": "boolean",
        "default": false,
        "label": "Темная тема"
      }
    }
  }
}
```

### Q: Как добавить custom CSS переменные?

**A:** Определите переменные в `theme.css`:

```css
:root {
  /* Цвета */
  --color-primary: #3b82f6;
  --color-secondary: #64748b;
  --color-success: #10b981;
  --color-warning: #f59e0b;
  --color-error: #ef4444;
  
  /* Размеры */
  --spacing-xs: 0.25rem;
  --spacing-sm: 0.5rem;
  --spacing-md: 1rem;
  --spacing-lg: 1.5rem;
  --spacing-xl: 3rem;
  
  /* Типографика */
  --font-family-sans: 'Inter', sans-serif;
  --font-family-serif: 'Georgia', serif;
  --font-size-xs: 0.75rem;
  --font-size-sm: 0.875rem;
  --font-size-base: 1rem;
  --font-size-lg: 1.125rem;
  --font-size-xl: 1.25rem;
  
  /* Переходы */
  --transition-fast: 0.15s ease;
  --transition-base: 0.3s ease;
  --transition-slow: 0.5s ease;
  
  /* Тени */
  --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
  --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}

/* Темная тема */
.theme-dark {
  --color-primary: #818cf8;
  --color-background: #0f172a;
  --color-surface: #1e293b;
  --color-text: #f1f5f9;
}
```

### Q: Как протестировать тему локально?

**A:** Несколько способов тестирования:

```bash
# 1. Через Artisan команды
php artisan theme:manage list
php artisan theme:test my-theme

# 2. Создание тестового tenant'а
php artisan tinker
>>> $store = Store::first()
>>> $theme = Theme::where('package_name', 'themes/my-theme')->first()
>>> $store->theme_id = $theme->id
>>> $store->save()

# 3. Через API
curl -X PUT http://localhost:8000/api/tenant/theme \
  -H "Content-Type: application/json" \
  -d '{"theme_id": 2}'
```

## ⚡ Производительность

### Q: Как оптимизировать загрузку тем?

**A:** Несколько стратегий оптимизации:

#### 1. Lazy Loading компонентов

```javascript
// Плохо - загружает все сразу
import ProductCard from './components/ProductCard.vue'
import ProductGrid from './components/ProductGrid.vue'

// Хорошо - lazy loading
export const components = {
  ProductCard: () => import(/* webpackChunkName: "product-card" */ './components/ProductCard.vue'),
  ProductGrid: () => import(/* webpackChunkName: "product-grid" */ './components/ProductGrid.vue')
}
```

#### 2. Предзагрузка критических компонентов

```javascript
export const hooks = {
  beforeMount() {
    // Предзагружаем критические компоненты
    Promise.all([
      import('./components/Header.vue'),
      import('./components/Footer.vue'),
      import('./components/Navigation.vue')
    ])
  }
}
```

#### 3. Кэширование

```javascript
class ThemeLoader {
  constructor() {
    this.componentCache = new Map()
    this.themeCache = new Map()
  }
  
  async loadComponent(name, path) {
    if (this.componentCache.has(name)) {
      return this.componentCache.get(name)
    }
    
    const component = await import(path)
    this.componentCache.set(name, component)
    return component
  }
}
```

### Q: Влияет ли количество тем на производительность?

**A:** Нет, система загружает только активную тему для каждого tenant'а:

- 🚀 **Lazy loading** - компоненты загружаются по требованию
- 💾 **Кэширование** - загруженные компоненты кэшируются
- 🎯 **Изоляция** - неиспользуемые темы не влияют на производительность
- 📦 **Code splitting** - Vite автоматически разделяет код

### Q: Как мониторить производительность тем?

**A:** Используйте встроенные инструменты:

```javascript
// В hooks темы
export const hooks = {
  beforeMount() {
    performance.mark('theme-load-start')
  },
  
  mounted() {
    performance.mark('theme-load-end')
    performance.measure('theme-load-time', 'theme-load-start', 'theme-load-end')
    
    const measure = performance.getEntriesByName('theme-load-time')[0]
    console.log(`Theme loaded in ${measure.duration}ms`)
  }
}
```

## 🔒 Безопасность

### Q: Безопасно ли позволять пользователям создавать темы?

**A:** При правильной настройке - да. Рекомендации:

#### 1. Валидация тем

```php
// app/Services/ThemeValidator.php
class ThemeValidator
{
    public function validateTheme(string $themePath): array
    {
        $errors = [];
        
        // Проверка структуры файлов
        if (!file_exists($themePath . '/theme.json')) {
            $errors[] = 'theme.json не найден';
        }
        
        // Проверка на запрещенные файлы
        $dangerousFiles = ['.php', '.exe', '.sh'];
        foreach ($dangerousFiles as $ext) {
            if (glob($themePath . '/**/*' . $ext)) {
                $errors[] = "Запрещенные файлы: {$ext}";
            }
        }
        
        return $errors;
    }
}
```

#### 2. Sandbox для выполнения

```javascript
// Ограничение доступа к глобальным объектам
const createSafeEnvironment = () => {
  const sandbox = {
    console: {
      log: (...args) => console.log('[Theme]', ...args),
      error: (...args) => console.error('[Theme]', ...args)
    },
    // Разрешенные API
    fetch: window.fetch.bind(window),
    localStorage: window.localStorage
    // window, document и другие глобальные объекты недоступны
  }
  
  return sandbox
}
```

#### 3. CSP заголовки

```php
// app/Http/Middleware/ThemeSecurityMiddleware.php
public function handle($request, Closure $next)
{
    $response = $next($request);
    
    $response->headers->set('Content-Security-Policy', 
        "default-src 'self'; " .
        "script-src 'self' 'unsafe-inline' /themes/; " .
        "style-src 'self' 'unsafe-inline' /themes/; " .
        "img-src 'self' data: /themes/;"
    );
    
    return $response;
}
```

### Q: Как защитить темы от XSS атак?

**A:** Несколько уровней защиты:

#### 1. Санитизация входных данных

```vue
<template>
  <!-- Плохо - может выполнить JS -->
  <div v-html="userContent"></div>
  
  <!-- Хорошо - безопасный вывод -->
  <div>{{ sanitizeHtml(userContent) }}</div>
</template>

<script>
import DOMPurify from 'dompurify'

export default {
  methods: {
    sanitizeHtml(html) {
      return DOMPurify.sanitize(html)
    }
  }
}
</script>
```

#### 2. Валидация конфигурации

```php
// app/Http/Requests/UpdateThemeConfigRequest.php
class UpdateThemeConfigRequest extends FormRequest
{
    public function rules()
    {
        return [
            'colors.primary' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
            'branding.site_name' => 'required|string|max:100|alpha_num_spaces',
            'layout.max_width' => 'required|in:1200px,1400px,1600px'
        ];
    }
}
```

### Q: Можно ли ограничить доступ к определенным API?

**A:** Да, используйте middleware и permissions:

```php
// routes/tenant.php
Route::middleware(['theme.access'])->group(function () {
    Route::get('/api/products', [ProductController::class, 'index']);
    Route::get('/api/categories', [CategoryController::class, 'index']);
});

// app/Http/Middleware/ThemeAccessMiddleware.php
public function handle($request, Closure $next)
{
    $store = tenant();
    $theme = $store->theme;
    
    // Проверяем разрешения темы
    $allowedFeatures = $theme->config_schema['features'] ?? [];
    $requestedFeature = $this->getFeatureFromRoute($request->route());
    
    if (!in_array($requestedFeature, $allowedFeatures)) {
        return response()->json(['error' => 'Feature not allowed'], 403);
    }
    
    return $next($request);
}
```

## 🛠️ Troubleshooting

### Q: Тема не загружается, что делать?

**A:** Пошаговая диагностика:

#### 1. Проверьте консоль браузера

```javascript
// Откройте DevTools → Console
// Ищите ошибки типа:
[ThemeLoader] Failed to load theme: Error: Theme not found
GET /api/themes/my-theme 404 (Not Found)
```

#### 2. Проверьте API эндпоинты

```bash
# Проверьте активную тему
curl http://your-domain.com/api/tenant/active-theme

# Проверьте компоненты
curl http://your-domain.com/api/theme/components

# Проверьте конкретную тему
curl http://your-domain.com/api/themes/themes%2Fmy-theme
```

#### 3. Проверьте файловую систему

```bash
# Убедитесь что файлы темы существуют
ls -la themes/themes/my-theme/
ls -la themes/themes/my-theme/theme.json
ls -la themes/themes/my-theme/resources/js/index.js
```

#### 4. Проверьте базу данных

```sql
-- Проверьте что тема зарегистрирована
SELECT * FROM themes WHERE package_name = 'themes/my-theme';

-- Проверьте привязку к store
SELECT s.id, s.name, s.theme_id, t.name as theme_name 
FROM stores s 
LEFT JOIN themes t ON s.theme_id = t.id 
WHERE s.id = 'your-tenant-id';
```

### Q: Компоненты загружаются, но стили не применяются

**A:** Проверьте загрузку CSS:

#### 1. В Network tab браузера

```
DevTools → Network → CSS
Должен быть запрос к theme.css
```

#### 2. Проверьте CSS переменные

```javascript
// В консоли браузера
getComputedStyle(document.documentElement).getPropertyValue('--color-primary')
// Должно вернуть цвет, например: "#3b82f6"
```

#### 3. Проверьте класс темы

```javascript
// В консоли браузера
document.body.classList
// Должен содержать что-то вроде "theme-my-theme"
```

### Q: API возвращает 404 для тем

**A:** Несколько возможных причин:

#### 1. Тема не зарегистрирована в БД

```bash
php artisan theme:manage scan
php artisan theme:manage list
```

#### 2. Неправильный package_name

```bash
# Проверьте theme.json
cat themes/themes/my-theme/theme.json | grep package_name

# Должно быть: "package_name": "themes/my-theme"
```

#### 3. Проблемы с URL encoding

```javascript
// В JavaScript используйте encodeURIComponent
const packageName = encodeURIComponent('themes/my-theme')
fetch(`/api/themes/${packageName}`)
```

### Q: Ошибки при сборке Vite

**A:** Часто встречающиеся проблемы:

#### 1. Dynamic imports

```javascript
// Добавьте комментарий @vite-ignore
const component = await import(/* @vite-ignore */ `/themes/${packageName}/component.vue`)
```

#### 2. Настройка Vite

```javascript
// vite.config.js
export default defineConfig({
  server: {
    fs: {
      allow: ['..', './themes']  // Разрешить доступ к themes
    }
  },
  build: {
    rollupOptions: {
      output: {
        manualChunks: undefined  // Отключить автоматическое разделение
      }
    }
  }
})
```

## 🔄 Миграция

### Q: Как перенести существующую тему?

**A:** Используйте [Migration Guide](MIGRATION_GUIDE.md) или автоматический инструмент:

```bash
# Создайте скрипт миграции
php artisan make:command MigrateTheme

# Или используйте готовый
php artisan theme:migrate --from=resources/js --to=themes/my-theme
```

### Q: Можно ли использовать старые и новые темы одновременно?

**A:** Да, поддерживается постепенная миграция:

```javascript
// tenant.js - fallback система
const initializeApp = async () => {
  try {
    // Попробовать новую систему тем
    const themeLoader = new ThemeLoader()
    const theme = await themeLoader.loadTheme(themeName)
    
    // Создать приложение с новой темой
    const app = createApp(ThemeComponent, { theme })
    app.mount('#app')
    
  } catch (error) {
    console.warn('[TenantApp] New theme system failed, falling back to legacy')
    
    // Fallback на старую систему
    const { createApp } = await import('vue')
    const LegacyApp = await import('./LegacyApp.vue')
    
    createApp(LegacyApp.default).mount('#app')
  }
}
```

### Q: Сохранятся ли данные при миграции?

**A:** Да, при правильном выполнении миграции:

- ✅ **База данных** - структура данных не изменяется
- ✅ **Файлы** - старые файлы копируются в новое место
- ✅ **Настройки** - конфигурации переносятся в новый формат
- ✅ **API** - обратная совместимость поддерживается

---

## 📞 Дополнительная помощь

Если ваш вопрос не освещен в FAQ:

1. **📚 Документация:** [THEMES.md](THEMES.md)
2. **🔧 API Reference:** [API_REFERENCE.md](API_REFERENCE.md)
3. **🚀 Migration Guide:** [MIGRATION_GUIDE.md](MIGRATION_GUIDE.md)
4. **🎨 Creation Guide:** [THEME_CREATION_GUIDE.md](THEME_CREATION_GUIDE.md)
5. **💬 Support:** support@yourcompany.com

**Удачной работы с системой тем!** 🎉
