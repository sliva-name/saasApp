# Makefile для SaaS Laravel приложения
.PHONY: help up down build restart logs shell migrate migrate-fresh seed composer npm artisan test clean fix-perms

# Переменные
COMPOSE_FILE = docker-compose.yml
APP_CONTAINER = saasapp-app-1
DB_CONTAINER = saasapp-db-1

# Цвета для вывода
GREEN = \033[0;32m
YELLOW = \033[1;33m
RED = \033[0;31m
NC = \033[0m # No Color

help: ## Показать справку по командам
	@echo "$(GREEN)Доступные команды:$(NC)"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(YELLOW)%-20s$(NC) %s\n", $$1, $$2}'

up: ## Запустить все контейнеры
	@echo "$(GREEN)🚀 Запуск контейнеров...$(NC)"
	docker-compose -f $(COMPOSE_FILE) up -d
	@echo "$(GREEN)✅ Контейнеры запущены!$(NC)"
	@echo "$(YELLOW)📱 Приложение доступно по адресу: http://localhost:8000$(NC)"
	@echo "$(YELLOW)🔍 Meilisearch доступен по адресу: http://localhost:7700$(NC)"

down: ## Остановить все контейнеры
	@echo "$(YELLOW)🛑 Остановка контейнеров...$(NC)"
	docker-compose -f $(COMPOSE_FILE) down
	@echo "$(GREEN)✅ Контейнеры остановлены!$(NC)"

build: ## Пересобрать контейнеры
	@echo "$(YELLOW)🔨 Пересборка контейнеров...$(NC)"
	docker-compose -f $(COMPOSE_FILE) build --no-cache
	@echo "$(GREEN)✅ Контейнеры пересобраны!$(NC)"

restart: down up ## Перезапустить контейнеры

logs: ## Показать логи приложения
	@echo "$(YELLOW)📋 Логи приложения:$(NC)"
	docker-compose -f $(COMPOSE_FILE) logs -f app

logs-all: ## Показать логи всех контейнеров
	@echo "$(YELLOW)📋 Логи всех контейнеров:$(NC)"
	docker-compose -f $(COMPOSE_FILE) logs -f

shell: ## Войти в контейнер приложения
	@echo "$(YELLOW)🐚 Вход в контейнер приложения...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app bash

db-shell: ## Войти в контейнер базы данных
	@echo "$(YELLOW)🐚 Вход в контейнер базы данных...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec db mysql -u root -p

migrate: ## Выполнить миграции
	@echo "$(YELLOW)🔄 Выполнение миграций...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app php artisan migrate
	@echo "$(GREEN)✅ Миграции выполнены!$(NC)"

migrate-fresh: ## Сбросить и выполнить миграции заново
	@echo "$(RED)⚠️  Сброс базы данных и выполнение миграций...$(NC)"
	@read -p "Вы уверены? Это удалит все данные! (y/N): " confirm && [ "$$confirm" = "y" ] || exit 1
	docker-compose -f $(COMPOSE_FILE) exec app php artisan migrate:fresh
	@echo "$(GREEN)✅ База данных сброшена и миграции выполнены!$(NC)"

migrate-tenant: ## Выполнить миграции для тенантов
	@echo "$(YELLOW)🔄 Выполнение миграций для тенантов...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app php artisan tenants:migrate
	@echo "$(GREEN)✅ Миграции тенантов выполнены!$(NC)"

seed: ## Заполнить базу тестовыми данными
	@echo "$(YELLOW)🌱 Заполнение базы тестовыми данными...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app php artisan db:seed
	@echo "$(GREEN)✅ База заполнена тестовыми данными!$(NC)"

check-data: ## Проверить данные в базе
	@echo "$(YELLOW)🔍 Проверка данных в базе...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app php artisan check:data

composer: ## Установить composer зависимости
	@echo "$(YELLOW)📦 Установка composer зависимостей...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app composer install
	@echo "$(GREEN)✅ Composer зависимости установлены!$(NC)"

composer-update: ## Обновить composer зависимости
	@echo "$(YELLOW)📦 Обновление composer зависимостей...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app composer update
	@echo "$(GREEN)✅ Composer зависимости обновлены!$(NC)"

npm: ## Установить npm зависимости
	@echo "$(YELLOW)📦 Установка npm зависимостей...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app npm install
	@echo "$(GREEN)✅ NPM зависимости установлены!$(NC)"

npm-build: ## Собрать фронтенд
	@echo "$(YELLOW)🎨 Сборка фронтенда...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app npm run build
	@echo "$(GREEN)✅ Фронтенд собран!$(NC)"

npm-dev: ## Запустить dev сервер для фронтенда
	@echo "$(YELLOW)🎨 Запуск dev сервера...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app npm run dev

artisan: ## Выполнить artisan команду (использование: make artisan cmd="migrate:status")
	@if [ -z "$(cmd)" ]; then \
		echo "$(RED)❌ Укажите команду: make artisan cmd=\"команда\"$(NC)"; \
		exit 1; \
	fi
	@echo "$(YELLOW)🔧 Выполнение artisan команды: $(cmd)$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app php artisan $(cmd)

test: ## Запустить тесты
	@echo "$(YELLOW)🧪 Запуск тестов...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app php artisan test
	@echo "$(GREEN)✅ Тесты выполнены!$(NC)"

cache-clear: ## Очистить кэш Laravel
	@echo "$(YELLOW)🧹 Очистка кэша Laravel...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app php artisan cache:clear
	docker-compose -f $(COMPOSE_FILE) exec app php artisan config:clear
	docker-compose -f $(COMPOSE_FILE) exec app php artisan route:clear
	docker-compose -f $(COMPOSE_FILE) exec app php artisan view:clear
	@echo "$(GREEN)✅ Кэш очищен!$(NC)"

optimize: ## Оптимизировать приложение
	@echo "$(YELLOW)⚡ Оптимизация приложения...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app php artisan optimize
	@echo "$(GREEN)✅ Приложение оптимизировано!$(NC)"

storage-link: ## Создать символическую ссылку для storage
	@echo "$(YELLOW)🔗 Создание символической ссылки для storage...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app php artisan storage:link
	@echo "$(GREEN)✅ Символическая ссылка создана!$(NC)"

setup: up migrate seed ## Полная настройка приложения (запуск + миграции + сиды)

clean: ## Очистить все контейнеры и volumes
	@echo "$(RED)⚠️  Очистка всех контейнеров и volumes...$(NC)"
	@read -p "Вы уверены? Это удалит все данные! (y/N): " confirm && [ "$$confirm" = "y" ] || exit 1
	docker-compose -f $(COMPOSE_FILE) down -v --remove-orphans
	docker system prune -f
	@echo "$(GREEN)✅ Очистка завершена!$(NC)"

status: ## Показать статус контейнеров
	@echo "$(YELLOW)📊 Статус контейнеров:$(NC)"
	docker-compose -f $(COMPOSE_FILE) ps

# Команды для работы с Scout (поиск)
scout-flush: ## Очистить индекс Scout
	@echo "$(YELLOW)🧹 Очистка индекса Scout...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app php artisan scout:flush
	@echo "$(GREEN)✅ Индекс Scout очищен!$(NC)"

scout-import: ## Импортировать данные в Scout
	@echo "$(YELLOW)📥 Импорт данных в Scout...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app php artisan scout:import
	@echo "$(GREEN)✅ Данные импортированы в Scout!$(NC)"

# Команды для работы с очередями
queue-work: ## Запустить обработку очередей
	@echo "$(YELLOW)🔄 Запуск обработки очередей...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app php artisan queue:work

queue-restart: ## Перезапустить обработку очередей
	@echo "$(YELLOW)🔄 Перезапуск обработки очередей...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app php artisan queue:restart
	@echo "$(GREEN)✅ Очереди перезапущены!$(NC)"

# Команды для работы с планировщиком
schedule-run: ## Запустить планировщик задач
	@echo "$(YELLOW)⏰ Запуск планировщика задач...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app php artisan schedule:run
	@echo "$(GREEN)✅ Планировщик выполнен!$(NC)"

# Команды для работы с тенантами
tenant-create: ## Создать нового тенанта (использование: make tenant-create domain="example.com")
	@if [ -z "$(domain)" ]; then \
		echo "$(RED)❌ Укажите домен: make tenant-create domain=\"example.com\"$(NC)"; \
		exit 1; \
	fi
	@echo "$(YELLOW)🏢 Создание тенанта для домена: $(domain)$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app php artisan tenants:create $(domain)
	@echo "$(GREEN)✅ Тенант создан!$(NC)"

# Команды для разработки
dev: up npm-dev ## Запуск в режиме разработки
	@echo "$(GREEN)🎯 Режим разработки запущен!$(NC)"
	@echo "$(YELLOW)📱 Приложение: http://localhost:8000$(NC)"
	@echo "$(YELLOW)🔍 Meilisearch: http://localhost:7700$(NC)"
	@echo "$(YELLOW)🎨 Vite dev server: http://localhost:5173$(NC)"

fix-perms: ## Исправить права на каталоги storage и bootstrap/cache внутри контейнера
	@echo "$(YELLOW)🔒 Исправление прав (storage, bootstrap/cache)...$(NC)"
	docker-compose -f $(COMPOSE_FILE) exec app bash -lc "set -e; \
	mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache; \
	chown -R www-data:www-data storage bootstrap/cache; \
	find storage -type d -exec chmod 775 {} \; ; \
	find storage -type f -exec chmod 664 {} \; ; \
	find bootstrap/cache -type d -exec chmod 775 {} \; ; \
	find bootstrap/cache -type f -exec chmod 664 {} \; ; \
	chmod -R ug+rwX storage bootstrap/cache"
	@echo "$(GREEN)✅ Права успешно исправлены!$(NC)"
