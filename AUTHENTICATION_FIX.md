# 🔐 Решение проблемы с аутентификацией

## Проблема
При попытке создать магазин возникает ошибка 403 "Unauthorized action" или редирект на страницу входа.

## Причина
Пользователь не аутентифицирован в системе. Все маршруты для работы с магазинами защищены middleware `auth`.

## Решение

### 1. Вход в систему
Перейдите на страницу входа: `http://localhost:8000/login`

### 2. Регистрация (если нет аккаунта)
Если у вас нет аккаунта, зарегистрируйтесь: `http://localhost:8000/register`

### 3. Проверка существующих пользователей
Если вы забыли данные для входа, можете проверить существующих пользователей:

```bash
docker-compose exec app php artisan tinker --execute="App\Models\User::all(['id', 'name', 'email'])->each(function(\$user) { echo \$user->id . ': ' . \$user->name . ' (' . \$user->email . ')' . PHP_EOL; });"
```

### 4. Создание тестового пользователя
Если пользователей нет, создайте тестового пользователя:

```bash
docker-compose exec app php artisan tinker --execute="
\$user = new App\Models\User();
\$user->name = 'Test User';
\$user->email = 'test@example.com';
\$user->password = bcrypt('password');
\$user->save();
echo 'User created: ' . \$user->email . ' with password: password';
"
```

### 5. Сброс пароля
Если забыли пароль, используйте функцию сброса пароля: `http://localhost:8000/forgot-password`

## Обновления в коде

### Изменения в StoreController
- Добавлен редирект на страницу входа вместо ошибки 401
- Улучшена обработка неаутентифицированных пользователей
- Добавлен метод `settings()` для управления магазинами
- **Исправлена проверка владения магазином**: изменено с `owner_id` на `user_id`

### Исправления в моделях
- **Store.php**: исправлена связь `owner()` для использования поля `user_id`
- **User.php**: исправлена связь `stores()` для использования поля `user_id`

### Новые шаблоны
- Создан шаблон `resources/views/stores/settings.blade.php` для управления магазинами

## Проверка работы

### 1. Войдите в систему
```
Email: test@example.com
Password: password
```

### 2. Перейдите к созданию магазина
`http://localhost:8000/stores/create`

### 3. Заполните форму и создайте магазин

### 4. Проверьте настройки магазинов
`http://localhost:8000/settings`

## Дополнительная информация

### Маршруты аутентификации
- `/login` - страница входа
- `/register` - страница регистрации
- `/forgot-password` - восстановление пароля
- `/reset-password/{token}` - сброс пароля

### Защищенные маршруты
- `/stores/*` - управление магазинами (требует аутентификации)
- `/settings` - настройки (требует аутентификации)
- `/dashboard` - панель управления (требует аутентификации)

### Middleware
Все маршруты магазинов защищены middleware `auth`, который автоматически перенаправляет неаутентифицированных пользователей на страницу входа.

## Troubleshooting

### Если проблема не решается:

1. **Очистите кеш:**
```bash
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
```

2. **Проверьте сессии:**
```bash
docker-compose exec app php artisan session:table
docker-compose exec app php artisan migrate
```

3. **Проверьте логи:**
```bash
docker-compose logs app
```

4. **Перезапустите контейнеры:**
```bash
docker-compose down
docker-compose up -d
```

---

**После выполнения этих шагов проблема с аутентификацией должна быть решена! 🎉**
