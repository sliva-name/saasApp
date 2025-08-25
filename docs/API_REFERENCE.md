# API Reference - Theme System

Полный справочник по REST API системы тем для SaaS E-commerce платформы.

## 📋 Содержание

- [Обзор](#обзор)
- [Аутентификация](#аутентификация)
- [Theme API](#theme-api)
- [Tenant API](#tenant-api)
- [Product API](#product-api)
- [Category API](#category-api)
- [Ошибки](#ошибки)
- [Примеры](#примеры)

## 🔍 Обзор

### Base URL

```
https://your-domain.com/api
```

### Формат ответов

Все API возвращают JSON в следующем формате:

**Успешный ответ:**
```json
{
  "data": { ... },
  "message": "Success",
  "status": 200
}
```

**Ошибка:**
```json
{
  "error": "Error message",
  "status": 400,
  "details": { ... }
}
```

### Версионирование

API использует URL versioning:
```
/api/v1/themes
```

## 🔐 Аутентификация

### Bearer Token

Для авторизованных запросов используйте Bearer Token:

```http
Authorization: Bearer YOUR_API_TOKEN
```

### Tenant Context

Все tenant-specific API автоматически определяют контекст по домену:

```
http://tenant1.localhost:8000/api/tenant/active-theme
http://tenant2.localhost:8000/api/tenant/active-theme
```

## 🎨 Theme API

### Получить информацию о теме

```http
GET /api/themes/{packageName}
```

**Параметры:**
- `packageName` (string) - Название пакета темы (например: `themes/default`)

**Ответ:**
```json
{
  "id": 1,
  "name": "Default Theme",
  "package_name": "themes/default",
  "description": "Основная тема для интернет-магазина",
  "version": "1.0.0",
  "author": "Your Company",
  "config_schema": {
    "colors": {
      "primary": "#3b82f6",
      "secondary": "#64748b"
    },
    "layout": {
      "header_fixed": true,
      "sidebar_enabled": true
    }
  },
  "is_active": true,
  "preview_image": "/themes/default/assets/preview.jpg",
  "created_at": "2024-01-01T00:00:00Z",
  "updated_at": "2024-01-01T00:00:00Z"
}
```

**Коды ошибок:**
- `404` - Тема не найдена
- `500` - Ошибка сервера

### Получить компоненты темы

```http
GET /api/theme/components
```

**Описание:** Возвращает пути к компонентам активной темы текущего tenant'а.

**Ответ:**
```json
{
  "TenantApp": "/themes/themes/default/resources/js/components/TenantApp.vue",
  "ProductCard": "/themes/themes/default/resources/js/components/ProductCard.vue",
  "Header": "/themes/themes/default/resources/js/components/Header.vue",
  "Footer": "/themes/themes/default/resources/js/components/Footer.vue",
  "Navigation": "/themes/themes/default/resources/js/components/Navigation.vue",
  "ProductCatalog": "/themes/themes/default/resources/js/components/ProductCatalog.vue",
  "SearchBar": "/themes/themes/default/resources/js/components/SearchBar.vue",
  "Home": "/themes/themes/default/resources/js/pages/Home.vue",
  "Products": "/themes/themes/default/resources/js/pages/Products.vue",
  "About": "/themes/themes/default/resources/js/pages/About.vue",
  "Contact": "/themes/themes/default/resources/js/pages/Contact.vue",
  "LoginPage": "/themes/themes/default/resources/js/pages/LoginPage.vue",
  "RegisterPage": "/themes/themes/default/resources/js/pages/RegisterPage.vue",
  "CartPage": "/themes/themes/default/resources/js/pages/CartPage.vue"
}
```

**Коды ошибок:**
- `500` - Ошибка при получении компонентов

## 🏢 Tenant API

### Получить активную тему tenant'а

```http
GET /api/tenant/active-theme
```

**Описание:** Возвращает информацию об активной теме текущего tenant'а.

**Ответ:**
```json
{
  "package_name": "themes/default",
  "config": {
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
      "description": "Your amazing online store",
      "logo": null
    }
  }
}
```

**Коды ошибок:**
- `500` - Ошибка при получении темы

### Обновить конфигурацию темы

```http
PUT /api/tenant/theme-config
```

**Тело запроса:**
```json
{
  "colors": {
    "primary": "#ff6b6b"
  },
  "branding": {
    "site_name": "My Awesome Store"
  }
}
```

**Ответ:**
```json
{
  "message": "Theme configuration updated successfully",
  "config": {
    "colors": {
      "primary": "#ff6b6b",
      "secondary": "#64748b"
    }
  }
}
```

### Сменить тему tenant'а

```http
PUT /api/tenant/theme
```

**Тело запроса:**
```json
{
  "theme_id": 2
}
```

**Ответ:**
```json
{
  "message": "Theme changed successfully",
  "theme": {
    "id": 2,
    "name": "Minimal Theme",
    "package_name": "themes/minimal"
  }
}
```

## 🛍️ Product API

### Получить список товаров

```http
GET /api/products
```

**Query параметры:**
- `page` (int) - Номер страницы (по умолчанию: 1)
- `per_page` (int) - Товаров на странице (по умолчанию: 12, макс: 100)
- `sort` (string) - Сортировка: `name`, `price`, `created_at`, `popular` (по умолчанию: `created_at`)
- `order` (string) - Направление: `asc`, `desc` (по умолчанию: `desc`)
- `category_id` (int) - Фильтр по категории
- `search` (string) - Поиск по названию и описанию
- `min_price` (float) - Минимальная цена
- `max_price` (float) - Максимальная цена
- `in_stock` (bool) - Только товары в наличии

**Пример запроса:**
```http
GET /api/products?page=1&per_page=12&sort=price&order=asc&category_id=5&search=laptop&min_price=10000&max_price=50000&in_stock=true
```

**Ответ:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Gaming Laptop",
      "description": "High-performance gaming laptop",
      "price": 45000.00,
      "discounted_price": 40500.00,
      "discount": 10,
      "stock": 5,
      "sku": "LAP-001",
      "image": "/storage/products/laptop-1.jpg",
      "images": [
        "/storage/products/laptop-1.jpg",
        "/storage/products/laptop-2.jpg"
      ],
      "category": {
        "id": 5,
        "name": "Laptops",
        "slug": "laptops"
      },
      "attributes": {
        "brand": "ASUS",
        "color": "Black",
        "memory": "16GB"
      },
      "rating": 4.5,
      "reviews_count": 23,
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-01-01T00:00:00Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 12,
    "total": 156,
    "last_page": 13,
    "has_more_pages": true
  },
  "links": {
    "first": "/api/products?page=1",
    "last": "/api/products?page=13",
    "prev": null,
    "next": "/api/products?page=2"
  }
}
```

### Получить товар по ID

```http
GET /api/products/{id}
```

**Параметры:**
- `id` (int) - ID товара

**Ответ:**
```json
{
  "id": 1,
  "name": "Gaming Laptop",
  "description": "High-performance gaming laptop with RGB keyboard",
  "full_description": "Detailed description...",
  "price": 45000.00,
  "discounted_price": 40500.00,
  "discount": 10,
  "stock": 5,
  "sku": "LAP-001",
  "image": "/storage/products/laptop-1.jpg",
  "images": [
    "/storage/products/laptop-1.jpg",
    "/storage/products/laptop-2.jpg",
    "/storage/products/laptop-3.jpg"
  ],
  "category": {
    "id": 5,
    "name": "Laptops",
    "slug": "laptops",
    "breadcrumbs": [
      {"name": "Electronics", "slug": "electronics"},
      {"name": "Computers", "slug": "computers"},
      {"name": "Laptops", "slug": "laptops"}
    ]
  },
  "attributes": {
    "brand": "ASUS",
    "model": "ROG Strix G15",
    "processor": "Intel Core i7",
    "memory": "16GB DDR4",
    "storage": "512GB SSD",
    "graphics": "NVIDIA RTX 3060",
    "display": "15.6\" Full HD 144Hz",
    "color": "Black"
  },
  "specifications": [
    {
      "group": "Performance",
      "items": [
        {"name": "Processor", "value": "Intel Core i7-11800H"},
        {"name": "Memory", "value": "16GB DDR4"},
        {"name": "Storage", "value": "512GB NVMe SSD"}
      ]
    }
  ],
  "rating": 4.5,
  "reviews_count": 23,
  "reviews": [
    {
      "id": 1,
      "user_name": "John Doe",
      "rating": 5,
      "comment": "Excellent laptop!",
      "created_at": "2024-01-01T00:00:00Z"
    }
  ],
  "related_products": [
    {
      "id": 2,
      "name": "Gaming Mouse",
      "price": 2500.00,
      "image": "/storage/products/mouse-1.jpg"
    }
  ],
  "created_at": "2024-01-01T00:00:00Z",
  "updated_at": "2024-01-01T00:00:00Z"
}
```

### Поиск товаров

```http
GET /api/products/search
```

**Query параметры:**
- `q` (string, обязательный) - Поисковый запрос
- `limit` (int) - Максимум результатов (по умолчанию: 10)

**Ответ:**
```json
{
  "query": "laptop",
  "suggestions": [
    {
      "id": 1,
      "name": "Gaming Laptop ASUS",
      "price": 45000.00,
      "image": "/storage/products/laptop-1.jpg",
      "category": "Laptops"
    },
    {
      "id": 15,
      "name": "MacBook Pro",
      "price": 120000.00,
      "image": "/storage/products/macbook-1.jpg",
      "category": "Laptops"
    }
  ],
  "total": 23
}
```

### Популярные товары

```http
GET /api/products/popular
```

**Query параметры:**
- `limit` (int) - Количество товаров (по умолчанию: 8, макс: 20)
- `category_id` (int) - Фильтр по категории

**Ответ:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Gaming Laptop",
      "price": 45000.00,
      "discounted_price": 40500.00,
      "image": "/storage/products/laptop-1.jpg",
      "rating": 4.5,
      "sales_count": 156
    }
  ]
}
```

### Фильтры для товаров

```http
GET /api/products/filters
```

**Query параметры:**
- `category_id` (int) - Фильтры для конкретной категории

**Ответ:**
```json
{
  "price_range": {
    "min": 1000,
    "max": 150000
  },
  "brands": [
    {"name": "ASUS", "count": 23},
    {"name": "Apple", "count": 15},
    {"name": "HP", "count": 18}
  ],
  "attributes": {
    "color": [
      {"value": "Black", "count": 45},
      {"value": "White", "count": 23},
      {"value": "Silver", "count": 34}
    ],
    "memory": [
      {"value": "8GB", "count": 67},
      {"value": "16GB", "count": 45},
      {"value": "32GB", "count": 12}
    ]
  },
  "categories": [
    {
      "id": 5,
      "name": "Laptops",
      "count": 89,
      "children": [
        {"id": 15, "name": "Gaming Laptops", "count": 34},
        {"id": 16, "name": "Business Laptops", "count": 55}
      ]
    }
  ]
}
```

### Похожие товары

```http
GET /api/products/{id}/similar
```

**Параметры:**
- `id` (int) - ID товара
- `limit` (int) - Количество товаров (по умолчанию: 4)

**Ответ:**
```json
{
  "data": [
    {
      "id": 2,
      "name": "Business Laptop",
      "price": 35000.00,
      "image": "/storage/products/laptop-2.jpg",
      "similarity_score": 0.85
    }
  ]
}
```

## 📂 Category API

### Получить список категорий

```http
GET /api/categories
```

**Query параметры:**
- `parent_id` (int) - Категории определенного родителя (null для корневых)
- `with_products_count` (bool) - Включить счетчик товаров

**Ответ:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Electronics",
      "slug": "electronics",
      "description": "Electronic devices and accessories",
      "image": "/storage/categories/electronics.jpg",
      "parent_id": null,
      "products_count": 156,
      "children_count": 5,
      "created_at": "2024-01-01T00:00:00Z"
    }
  ]
}
```

### Получить категорию по ID/slug

```http
GET /api/categories/{id}
```

**Параметры:**
- `id` (int|string) - ID или slug категории

**Ответ:**
```json
{
  "id": 5,
  "name": "Laptops",
  "slug": "laptops",
  "description": "All types of laptops",
  "image": "/storage/categories/laptops.jpg",
  "parent_id": 2,
  "products_count": 89,
  "parent": {
    "id": 2,
    "name": "Computers",
    "slug": "computers"
  },
  "children": [
    {
      "id": 15,
      "name": "Gaming Laptops",
      "slug": "gaming-laptops",
      "products_count": 34
    }
  ],
  "breadcrumbs": [
    {"id": 1, "name": "Electronics", "slug": "electronics"},
    {"id": 2, "name": "Computers", "slug": "computers"},
    {"id": 5, "name": "Laptops", "slug": "laptops"}
  ],
  "created_at": "2024-01-01T00:00:00Z"
}
```

### Дерево категорий

```http
GET /api/categories/tree
```

**Query параметры:**
- `max_depth` (int) - Максимальная глубина (по умолчанию: 3)

**Ответ:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Electronics",
      "slug": "electronics",
      "products_count": 156,
      "children": [
        {
          "id": 2,
          "name": "Computers",
          "slug": "computers",
          "products_count": 89,
          "children": [
            {
              "id": 5,
              "name": "Laptops",
              "slug": "laptops",
              "products_count": 45
            }
          ]
        }
      ]
    }
  ]
}
```

### Популярные категории

```http
GET /api/categories/popular
```

**Query параметры:**
- `limit` (int) - Количество категорий (по умолчанию: 6)

**Ответ:**
```json
{
  "data": [
    {
      "id": 5,
      "name": "Laptops",
      "slug": "laptops",
      "image": "/storage/categories/laptops.jpg",
      "products_count": 89,
      "popularity_score": 0.95
    }
  ]
}
```

### Хлебные крошки категории

```http
GET /api/categories/{id}/breadcrumbs
```

**Параметры:**
- `id` (int) - ID категории

**Ответ:**
```json
{
  "breadcrumbs": [
    {"id": 1, "name": "Electronics", "slug": "electronics"},
    {"id": 2, "name": "Computers", "slug": "computers"},
    {"id": 5, "name": "Laptops", "slug": "laptops"}
  ]
}
```

## ❌ Ошибки

### Стандартные HTTP коды

- `200` - Успешно
- `201` - Создано
- `400` - Неверный запрос
- `401` - Не авторизован
- `403` - Запрещено
- `404` - Не найдено
- `422` - Ошибка валидации
- `429` - Слишком много запросов
- `500` - Ошибка сервера

### Формат ошибок

```json
{
  "error": "Validation failed",
  "status": 422,
  "details": {
    "field": ["Field is required"],
    "email": ["Invalid email format"]
  }
}
```

### Специфические ошибки тем

**Theme not found (404):**
```json
{
  "error": "Theme not found",
  "status": 404,
  "details": {
    "package_name": "themes/non-existent"
  }
}
```

**Invalid theme configuration (422):**
```json
{
  "error": "Invalid theme configuration",
  "status": 422,
  "details": {
    "colors.primary": ["Must be a valid hex color"],
    "layout.max_width": ["Must be a valid CSS value"]
  }
}
```

## 💡 Примеры использования

### JavaScript/Frontend

```javascript
// Получить активную тему
const themeResponse = await fetch('/api/tenant/active-theme')
const theme = await themeResponse.json()

// Загрузить компоненты темы
const componentsResponse = await fetch('/api/theme/components')
const components = await componentsResponse.json()

// Поиск товаров
const searchProducts = async (query) => {
  const response = await fetch(`/api/products/search?q=${encodeURIComponent(query)}`)
  return await response.json()
}

// Загрузка товаров с фильтрами
const loadProducts = async (filters) => {
  const params = new URLSearchParams(filters)
  const response = await fetch(`/api/products?${params}`)
  return await response.json()
}
```

### PHP/Backend

```php
use Illuminate\Support\Facades\Http;

// Получить товары через внутренний API
$products = Http::get('/api/products', [
    'category_id' => 5,
    'per_page' => 12,
    'sort' => 'popular'
])->json();

// Обновить конфигурацию темы
$config = Http::put('/api/tenant/theme-config', [
    'colors' => [
        'primary' => '#ff6b6b'
    ]
])->json();
```

### cURL

```bash
# Получить информацию о теме
curl -X GET "https://tenant1.localhost:8000/api/themes/themes%2Fdefault" \
  -H "Accept: application/json"

# Поиск товаров
curl -X GET "https://tenant1.localhost:8000/api/products/search?q=laptop&limit=5" \
  -H "Accept: application/json"

# Обновить конфигурацию темы
curl -X PUT "https://tenant1.localhost:8000/api/tenant/theme-config" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"colors":{"primary":"#ff6b6b"}}'
```

## 🔄 Rate Limiting

API имеет ограничения на количество запросов:

- **Гостевые пользователи:** 60 запросов в минуту
- **Авторизованные пользователи:** 120 запросов в минуту
- **Поиск:** 30 запросов в минуту

При превышении лимита возвращается:

```json
{
  "error": "Too many requests",
  "status": 429,
  "retry_after": 60
}
```

## 📝 Заметки

1. **Кэширование:** Большинство GET эндпоинтов кэшируются на 5-15 минут
2. **Валидация:** Все входящие данные валидируются согласно схемам
3. **Версионирование:** API поддерживает версионирование через URL
4. **CORS:** API поддерживает CORS для frontend приложений
5. **Пагинация:** Используется стандартная пагинация Laravel

---

Для получения дополнительной информации обращайтесь к [основной документации](THEMES.md).
