# Устранение проблем с загрузкой данных

## 🔍 Диагностика проблемы

Если товары и категории не загружаются, выполните следующие шаги:

### 1. Проверка данных в базе

```bash
# Проверить данные в базе
make check-data

# Или напрямую через artisan
make artisan cmd="check:data"
```

### 2. Проверка API endpoints

Откройте в браузере: `http://localhost:8000/api-test`

Это покажет:
- Работает ли API
- Есть ли данные в базе
- Какие ошибки возникают

### 3. Проверка консоли браузера

Откройте Developer Tools (F12) и проверьте:
- Ошибки в Console
- Запросы в Network tab
- Статус ответов API

## 🛠️ Решение проблем

### Проблема: Нет данных в базе

**Решение:**
```bash
# Полная переустановка с данными
make migrate-fresh
make seed

# Или пошагово:
make migrate-fresh
make migrate-tenant
make seed
```

### Проблема: API возвращает ошибки

**Проверьте:**
1. Запущены ли контейнеры: `make status`
2. Работает ли база данных: `make db-shell`
3. Правильно ли настроен .env файл

### Проблема: Scout не работает

**Решение:**
```bash
# Очистить и переиндексировать Scout
make scout-flush
make scout-import
```

### Проблема: Неправильные маршруты

**Проверьте файл:** `routes/tenant.php`

Убедитесь, что есть маршруты:
- `/api/products`
- `/api/categories/{slug}`
- `/search/suggest`

## 🔧 Отладка

### Логи приложения

```bash
# Просмотр логов
make logs

# Логи всех контейнеров
make logs-all
```

### Проверка конфигурации

```bash
# Очистить кэш
make cache-clear

# Проверить конфигурацию
make artisan cmd="config:show"
```

### Проверка базы данных

```bash
# Войти в базу данных
make db-shell

# В MySQL выполнить:
SHOW TABLES;
SELECT COUNT(*) FROM categories;
SELECT COUNT(*) FROM products;
```

## 📋 Чек-лист

- [ ] Контейнеры запущены (`make status`)
- [ ] База данных работает (`make db-shell`)
- [ ] Есть данные в базе (`make check-data`)
- [ ] API отвечает (`/api-test`)
- [ ] Scout работает (`make scout-import`)
- [ ] Нет ошибок в консоли браузера
- [ ] Правильные маршруты в `routes/tenant.php`

## 🚨 Частые ошибки

### 1. "No such file or directory"
```bash
# Пересобрать контейнеры
make build
make up
```

### 2. "Connection refused"
```bash
# Проверить порты
docker ps
# Убедиться, что порты 8000, 3306, 7700 свободны
```

### 3. "Class not found"
```bash
# Переустановить зависимости
make composer
make npm
```

### 4. "Permission denied"
```bash
# Исправить права доступа
make shell
chmod -R 775 storage bootstrap/cache
```

## 📞 Получение помощи

Если проблема не решается:

1. **Соберите информацию:**
   - Результат `make check-data`
   - Логи приложения (`make logs`)
   - Ошибки из консоли браузера
   - Скриншот страницы `/api-test`

2. **Проверьте:**
   - Версию Docker
   - Версию PHP
   - Версию Laravel
   - Содержимое .env файла

3. **Попробуйте:**
   - Полную переустановку: `make clean && make setup`
   - Проверку на другой машине
   - Создание нового тенанта
