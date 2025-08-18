# API Documentation

## 🌐 Обзор API

SaaS E-commerce Platform предоставляет RESTful API для взаимодействия с магазинами, товарами, поиском и пользователями. API построен на Laravel с использованием JSON для обмена данными.

## 🔐 Аутентификация

### Bearer Token

Большинство эндпоинтов требуют аутентификации через Bearer Token:

```http
Authorization: Bearer {your-token}
```

### Получение токена

```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password"
}
```

**Ответ:**
```json
{
    "token": "1|abc123...",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com"
    }
}
```

## 📋 Общие принципы

### Формат ответов

Все API ответы имеют единообразную структуру:

```json
{
    "data": {
        // Данные ответа
    },
    "meta": {
        "pagination": {
            "current_page": 1,
            "last_page": 10,
            "per_page": 15,
            "total": 150
        }
    },
    "message": "Success message"
}
```

### Коды ошибок

- `200` - Успешный запрос
- `201` - Ресурс создан
- `400` - Неверный запрос
- `401` - Не авторизован
- `403` - Доступ запрещен
- `404` - Ресурс не найден
- `422` - Ошибка валидации
- `500` - Внутренняя ошибка сервера

### Пагинация

Для эндпоинтов с пагинацией используйте параметры:

- `page` - Номер страницы (по умолчанию: 1)
- `per_page` - Количество элементов на странице (по умолчанию: 15)

## 🏪 Магазины (Stores)

### Получение списка магазинов

```http
GET /api/stores
Authorization: Bearer {token}
```

**Параметры:**
- `page` - Номер страницы
- `per_page` - Элементов на странице
- `search` - Поиск по названию

**Ответ:**
```json
{
    "data": [
        {
            "id": "uuid-1",
            "name": "My Store",
            "slug": "my-store",
            "plan": "Pro",
            "domain": "mystore.com",
            "created_at": "2024-01-15T10:30:00Z",
            "owner": {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com"
            }
        }
    ],
    "meta": {
        "pagination": {
            "current_page": 1,
            "last_page": 5,
            "per_page": 15,
            "total": 75
        }
    }
}
```

### Создание магазина

```http
POST /api/stores
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "New Store",
    "plan": "Basic",
    "domain": "newstore.com",
    "theme_id": 1
}
```

**Валидация:**
- `name` - required, string, max:255
- `plan` - required, in:Free,Basic,Pro
- `domain` - nullable, string, unique, regex:/^[a-z0-9]+([\-]?[a-z0-9]+)*(\.[a-z]{2,})+$/i
- `theme_id` - nullable, integer, exists:themes,id

### Получение магазина

```http
GET /api/stores/{id}
Authorization: Bearer {token}
```

### Обновление магазина

```http
PUT /api/stores/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Updated Store Name",
    "plan": "Pro"
}
```

### Удаление магазина

```http
DELETE /api/stores/{id}
Authorization: Bearer {token}
```

## 🛍️ Товары (Products)

### Получение списка товаров

```http
GET /api/products
```

**Параметры:**
- `page` - Номер страницы
- `per_page` - Элементов на странице
- `search` - Поиск по названию
- `category_id` - Фильтр по категории
- `price_min` - Минимальная цена
- `price_max` - Максимальная цена
- `in_stock` - Только в наличии (true/false)
- `sort` - Сортировка (price_asc, price_desc, name_asc, name_desc, created_at_desc)

**Ответ:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Product Name",
            "slug": "product-name",
            "description": "Product description",
            "price": 99.99,
            "stock": 50,
            "image_url": "https://example.com/images/product.jpg",
            "category": {
                "id": 1,
                "name": "Electronics",
                "slug": "electronics"
            },
            "created_at": "2024-01-15T10:30:00Z"
        }
    ],
    "meta": {
        "pagination": {
            "current_page": 1,
            "last_page": 10,
            "per_page": 15,
            "total": 150
        },
        "filters": {
            "categories": [
                {"id": 1, "name": "Electronics", "count": 45},
                {"id": 2, "name": "Clothing", "count": 30}
            ],
            "price_range": {
                "min": 10.00,
                "max": 999.99
            }
        }
    }
}
```

### Получение товара

```http
GET /api/products/{slug}
```

**Ответ:**
```json
{
    "data": {
        "id": 1,
        "name": "Product Name",
        "slug": "product-name",
        "description": "Detailed product description",
        "price": 99.99,
        "stock": 50,
        "images": [
            "https://example.com/images/product-1.jpg",
            "https://example.com/images/product-2.jpg"
        ],
        "category": {
            "id": 1,
            "name": "Electronics",
            "slug": "electronics"
        },
        "attributes": {
            "color": "Red",
            "size": "Large",
            "weight": "2.5kg"
        },
        "created_at": "2024-01-15T10:30:00Z",
        "updated_at": "2024-01-20T15:45:00Z"
    }
}
```

### Создание товара (Admin)

```http
POST /api/admin/products
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
    "name": "New Product",
    "description": "Product description",
    "price": 99.99,
    "stock": 100,
    "category_id": 1,
    "images[]": [file1, file2],
    "attributes": {
        "color": "Red",
        "size": "Large"
    }
}
```

**Валидация:**
- `name` - required, string, max:255
- `description` - nullable, string
- `price` - required, numeric, min:0
- `stock` - nullable, integer, min:0
- `category_id` - nullable, integer, exists:categories,id
- `images.*` - nullable, image, mimes:jpeg,png,jpg,gif, max:2048
- `attributes` - nullable, array

### Обновление товара (Admin)

```http
PUT /api/admin/products/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Updated Product Name",
    "price": 89.99,
    "stock": 75
}
```

### Удаление товара (Admin)

```http
DELETE /api/admin/products/{id}
Authorization: Bearer {token}
```

## 🔍 Поиск

### Поиск товаров

```http
GET /api/search
```

**Параметры:**
- `q` - Поисковый запрос
- `page` - Номер страницы
- `per_page` - Элементов на странице
- `filters` - JSON объект с фильтрами

**Пример запроса:**
```http
GET /api/search?q=laptop&page=1&per_page=20&filters={"price_min":100,"price_max":1000,"categories":[1,2]}
```

**Ответ:**
```json
{
    "data": {
        "hits": [
            {
                "id": 1,
                "name": "Laptop Pro",
                "slug": "laptop-pro",
                "price": 999.99,
                "image_url": "https://example.com/images/laptop.jpg",
                "_score": 0.95
            }
        ],
        "pagination": {
            "current_page": 1,
            "last_page": 5,
            "per_page": 20,
            "total": 100
        },
        "facets": {
            "category_id": {
                "1": 25,
                "2": 15
            },
            "price": {
                "ranges": [
                    {"min": 0, "max": 100, "count": 10},
                    {"min": 100, "max": 500, "count": 30},
                    {"min": 500, "max": 1000, "count": 40}
                ]
            }
        }
    }
}
```

### Автодополнение

```http
GET /api/search/suggest
```

**Параметры:**
- `q` - Поисковый запрос (минимум 2 символа)

**Ответ:**
```json
{
    "suggestions": [
        "laptop",
        "laptop pro",
        "laptop gaming",
        "laptop dell",
        "laptop hp"
    ]
}
```

## 📂 Категории (Categories)

### Получение списка категорий

```http
GET /api/categories
```

**Ответ:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Electronics",
            "slug": "electronics",
            "parent_id": null,
            "children": [
                {
                    "id": 2,
                    "name": "Laptops",
                    "slug": "laptops",
                    "parent_id": 1
                }
            ]
        }
    ]
}
```

### Получение товаров категории

```http
GET /api/categories/{slug}/products
```

**Параметры:**
- `page` - Номер страницы
- `per_page` - Элементов на странице
- `sort` - Сортировка

## 🛒 Корзина (Cart)

### Получение корзины

```http
GET /api/cart
Authorization: Bearer {token}
```

**Ответ:**
```json
{
    "data": {
        "items": [
            {
                "id": 1,
                "product_id": 1,
                "name": "Product Name",
                "price": 99.99,
                "quantity": 2,
                "image_url": "https://example.com/images/product.jpg",
                "subtotal": 199.98
            }
        ],
        "total": 199.98,
        "item_count": 2
    }
}
```

### Добавление товара в корзину

```http
POST /api/cart
Authorization: Bearer {token}
Content-Type: application/json

{
    "product_id": 1,
    "quantity": 2
}
```

### Обновление количества

```http
PUT /api/cart/{item_id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "quantity": 3
}
```

### Удаление товара из корзины

```http
DELETE /api/cart/{item_id}
Authorization: Bearer {token}
```

### Очистка корзины

```http
DELETE /api/cart
Authorization: Bearer {token}
```

## 👤 Пользователи (Users)

### Получение профиля

```http
GET /api/user
Authorization: Bearer {token}
```

**Ответ:**
```json
{
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "email_verified_at": "2024-01-15T10:30:00Z",
        "created_at": "2024-01-01T00:00:00Z",
        "stores": [
            {
                "id": "uuid-1",
                "name": "My Store",
                "slug": "my-store",
                "plan": "Pro"
            }
        ]
    }
}
```

### Обновление профиля

```http
PUT /api/user
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "John Smith",
    "email": "johnsmith@example.com"
}
```

### Изменение пароля

```http
PUT /api/user/password
Authorization: Bearer {token}
Content-Type: application/json

{
    "current_password": "oldpassword",
    "password": "newpassword",
    "password_confirmation": "newpassword"
}
```

## 🔐 Аутентификация

### Регистрация

```http
POST /api/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

**Валидация:**
- `name` - required, string, max:255
- `email` - required, email, unique:users
- `password` - required, string, min:8, confirmed

### Вход

```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password"
}
```

### Выход

```http
POST /api/auth/logout
Authorization: Bearer {token}
```

### Обновление токена

```http
POST /api/auth/refresh
Authorization: Bearer {token}
```

### Восстановление пароля

```http
POST /api/auth/forgot-password
Content-Type: application/json

{
    "email": "john@example.com"
}
```

### Сброс пароля

```http
POST /api/auth/reset-password
Content-Type: application/json

{
    "token": "reset-token",
    "email": "john@example.com",
    "password": "newpassword",
    "password_confirmation": "newpassword"
}
```

## 📊 Статистика (Admin)

### Статистика магазина

```http
GET /api/admin/stats
Authorization: Bearer {token}
```

**Ответ:**
```json
{
    "data": {
        "total_products": 150,
        "total_orders": 25,
        "total_revenue": 2500.00,
        "products_by_category": [
            {"category": "Electronics", "count": 45},
            {"category": "Clothing", "count": 30}
        ],
        "revenue_by_month": [
            {"month": "2024-01", "revenue": 500.00},
            {"month": "2024-02", "revenue": 750.00}
        ]
    }
}
```

## 🔄 Webhooks

### Настройка webhook

```http
POST /api/webhooks
Authorization: Bearer {token}
Content-Type: application/json

{
    "url": "https://your-domain.com/webhook",
    "events": ["order.created", "product.updated"],
    "secret": "webhook-secret"
}
```

### События webhook

- `order.created` - Заказ создан
- `order.updated` - Заказ обновлен
- `product.created` - Товар создан
- `product.updated` - Товар обновлен
- `product.deleted` - Товар удален

### Формат webhook payload

```json
{
    "event": "order.created",
    "timestamp": "2024-01-15T10:30:00Z",
    "data": {
        "order_id": 1,
        "total": 199.98,
        "customer": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        }
    }
}
```

## 🚨 Обработка ошибок

### Формат ошибки

```json
{
    "error": {
        "message": "Validation failed",
        "code": "VALIDATION_ERROR",
        "details": {
            "email": ["The email field is required."],
            "password": ["The password must be at least 8 characters."]
        }
    }
}
```

### Коды ошибок

- `VALIDATION_ERROR` - Ошибка валидации
- `AUTHENTICATION_FAILED` - Ошибка аутентификации
- `AUTHORIZATION_FAILED` - Ошибка авторизации
- `RESOURCE_NOT_FOUND` - Ресурс не найден
- `RATE_LIMIT_EXCEEDED` - Превышен лимит запросов
- `INTERNAL_ERROR` - Внутренняя ошибка сервера

## 📈 Rate Limiting

API имеет ограничения на количество запросов:

- **Аутентифицированные пользователи**: 1000 запросов в час
- **Неаутентифицированные пользователи**: 100 запросов в час
- **Поисковые запросы**: 500 запросов в час

### Headers для rate limiting

```http
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 999
X-RateLimit-Reset: 1642233600
```

## 🔧 SDK и библиотеки

### PHP SDK

```php
use SaasApp\Api\Client;

$client = new Client('your-api-token');

// Получение товаров
$products = $client->products()->list([
    'page' => 1,
    'per_page' => 20,
    'search' => 'laptop'
]);

// Создание товара
$product = $client->products()->create([
    'name' => 'New Product',
    'price' => 99.99,
    'description' => 'Product description'
]);
```

### JavaScript SDK

```javascript
import { SaasAppClient } from '@saasapp/js-sdk';

const client = new SaasAppClient('your-api-token');

// Получение товаров
const products = await client.products.list({
    page: 1,
    per_page: 20,
    search: 'laptop'
});

// Создание товара
const product = await client.products.create({
    name: 'New Product',
    price: 99.99,
    description: 'Product description'
});
```

## 📚 Дополнительные ресурсы

- [Postman Collection](https://api.saasapp.com/postman-collection.json)
- [OpenAPI Specification](https://api.saasapp.com/openapi.json)
- [SDK Documentation](https://docs.saasapp.com/sdk)
- [Webhook Guide](https://docs.saasapp.com/webhooks)
- [Rate Limiting](https://docs.saasapp.com/rate-limiting)
