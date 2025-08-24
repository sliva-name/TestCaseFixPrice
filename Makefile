# Сервис вакансий - Makefile
# Использование: make <команда>

.PHONY: help start stop restart build up down logs clean test db-test db-create migrate seed install-backend install-frontend check-db fix-permissions

# Цвета для вывода
GREEN := \033[0;32m
YELLOW := \033[1;33m
RED := \033[0;31m
NC := \033[0m # No Color

# Помощь
help: ## Показать справку по командам
	@echo "$(GREEN)Сервис вакансий - Доступные команды:$(NC)"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(YELLOW)%-15s$(NC) %s\n", $$1, $$2}'
	@echo ""

# Основные команды
start: ## Запустить проект (полная установка и настройка)
	@echo "$(GREEN)🚀 Запуск сервиса вакансий...$(NC)"
	@make stop
	@make up
	@echo "$(YELLOW)⏳ Ожидание запуска сервисов...$(NC)"
	@sleep 10
	@make install-backend
	@make db-test
	@make db-create
	@make migrate
	@make seed
	@make install-frontend
	@make fix-permissions
	@echo ""
	@echo "$(GREEN)✅ Сервис вакансий успешно запущен!$(NC)"
	@echo ""
	@echo "$(GREEN)🌐 Доступ к приложению:$(NC)"
	@echo "   Frontend: http://localhost"
	@echo "   API: http://localhost/api"
	@echo ""
	@echo "$(GREEN)📚 Документация: README.md$(NC)"
	@echo ""
	@echo "$(GREEN)🔍 Просмотр логов: make logs$(NC)"
	@echo "$(GREEN)🛑 Остановка: make stop$(NC)"

up: ## Запустить контейнеры
	@echo "$(YELLOW)🐳 Запуск Docker контейнеров...$(NC)"
	docker-compose up -d

down: ## Остановить контейнеры
	@echo "$(YELLOW)🛑 Остановка контейнеров...$(NC)"
	docker-compose down

stop: ## Остановить и удалить контейнеры
	@echo "$(YELLOW)🛑 Остановка и удаление контейнеров...$(NC)"
	docker-compose down

restart: ## Перезапустить контейнеры
	@echo "$(YELLOW)🔄 Перезапуск контейнеров...$(NC)"
	docker-compose restart

build: ## Пересобрать образы
	@echo "$(YELLOW)🔨 Пересборка образов...$(NC)"
	docker-compose build --no-cache
	@make fix-permissions

# Установка зависимостей
install-backend: ## Установить зависимости Backend
	@echo "$(YELLOW)📦 Установка зависимостей Backend...$(NC)"
	docker-compose exec -T backend composer install --no-interaction
	@make fix-permissions

install-frontend: ## Установить зависимости Frontend
	@echo "$(YELLOW)📦 Установка зависимостей Frontend...$(NC)"
	docker-compose exec -T frontend npm install
	@make fix-permissions

# Права доступа
fix-permissions: ## Исправить права доступа для кэша и временных файлов
	@echo "$(YELLOW)🔧 Исправление прав доступа...$(NC)"
	@docker-compose exec -T backend chmod -R 777 runtime/ 2>/dev/null || true
	@docker-compose exec -T backend chmod -R 777 web/assets/ 2>/dev/null || true
	@docker-compose exec -T backend chmod -R 777 backend/runtime/ 2>/dev/null || true
	@docker-compose exec -T backend chmod -R 777 backend/web/assets/ 2>/dev/null || true
	@docker-compose exec -T frontend chmod -R 777 .nuxt/ 2>/dev/null || true
	@docker-compose exec -T frontend chmod -R 777 node_modules/.cache/ 2>/dev/null || true
	@echo "$(GREEN)✅ Права доступа исправлены$(NC)"

# База данных
db-test: ## Проверить подключение к базе данных
	@echo "$(YELLOW)🔍 Проверка подключения к базе данных...$(NC)"
	docker-compose exec -T backend php yii db/test

db-create: ## Создать базу данных
	@echo "$(YELLOW)🗄️ Создание базы данных...$(NC)"
	docker-compose exec -T backend php yii db/create

db-tables: ## Показать информацию о таблицах
	@echo "$(YELLOW)📋 Информация о таблицах:$(NC)"
	docker-compose exec -T backend php yii db/tables

# Миграции и сидеры
migrate: ## Запустить миграции
	@echo "$(YELLOW)🗄️ Создание таблиц базы данных...$(NC)"
	docker-compose exec -T backend php yii migrate --interactive=0

migrate-down: ## Откатить последнюю миграцию
	@echo "$(YELLOW)⬇️ Откат последней миграции...$(NC)"
	docker-compose exec -T backend php yii migrate/down

migrate-history: ## Показать историю миграций
	@echo "$(YELLOW)📜 История миграций:$(NC)"
	docker-compose exec -T backend php yii migrate/history

seed: ## Заполнить тестовыми данными
	@echo "$(YELLOW)🌱 Заполнение тестовыми данными...$(NC)"
	docker-compose exec -T backend php yii seed/vacancies

# Логи и мониторинг
logs: ## Показать логи всех сервисов
	@echo "$(YELLOW)📝 Логи всех сервисов:$(NC)"
	docker-compose logs -f

logs-backend: ## Показать логи Backend
	@echo "$(YELLOW)📝 Логи Backend:$(NC)"
	docker-compose logs -f backend

logs-frontend: ## Показать логи Frontend
	@echo "$(YELLOW)📝 Логи Frontend:$(NC)"
	docker-compose logs -f frontend

logs-db: ## Показать логи базы данных
	@echo "$(YELLOW)📝 Логи базы данных:$(NC)"
	docker-compose logs -f db

logs-nginx: ## Показать логи Nginx
	@echo "$(YELLOW)📝 Логи Nginx:$(NC)"
	docker-compose logs -f nginx

# Проверка состояния
check-db: ## Полная проверка состояния базы данных
	@echo "$(GREEN)🔍 Проверка состояния базы данных...$(NC)"
	@echo "$(YELLOW)📦 Статус контейнеров:$(NC)"
	@docker-compose ps
	@echo ""
	@echo "$(YELLOW)🔌 Проверка подключения к базе данных:$(NC)"
	@docker-compose exec -T backend php yii db/test
	@echo ""
	@echo "$(YELLOW)📋 Информация о таблицах:$(NC)"
	@docker-compose exec -T backend php yii db/tables
	@echo ""
	@echo "$(YELLOW)📝 Последние логи базы данных:$(NC)"
	@docker-compose logs --tail=10 db
	@echo ""
	@echo "$(GREEN)✅ Проверка завершена!$(NC)"

status: ## Показать статус контейнеров
	@echo "$(YELLOW)📦 Статус контейнеров:$(NC)"
	@docker-compose ps

# Тестирование
test: ## Запустить тесты
	@echo "$(YELLOW)🧪 Запуск тестов...$(NC)"
	@docker-compose exec -T backend vendor/bin/codecept run

test-unit: ## Запустить unit тесты
	@echo "$(YELLOW)🧪 Запуск unit тестов...$(NC)"
	@docker-compose exec -T backend vendor/bin/codecept run unit

test-functional: ## Запустить functional тесты
	@echo "$(YELLOW)🧪 Запуск functional тестов...$(NC)"
	@docker-compose exec -T backend vendor/bin/codecept run functional

test-frontend: ## Запустить frontend тесты
	@echo "$(YELLOW)🧪 Запуск frontend тестов...$(NC)"
	@docker-compose exec -T frontend npm run test

# Очистка
clean: ## Очистить все (контейнеры, образы, volumes)
	@echo "$(RED)🧹 Полная очистка проекта...$(NC)"
	@docker-compose down -v --rmi all
	@docker system prune -f

clean-volumes: ## Очистить volumes (данные БД)
	@echo "$(RED)🧹 Очистка volumes...$(NC)"
	@docker-compose down -v

# Резервное копирование
backup: ## Создать бэкап базы данных
	@echo "$(YELLOW)💾 Создание бэкапа базы данных...$(NC)"
	@docker-compose exec -T db mysqldump -u root -ppassword yii2app > backup_$(shell date +%Y%m%d_%H%M%S).sql
	@echo "$(GREEN)✅ Бэкап создан: backup_$(shell date +%Y%m%d_%H%M%S).sql$(NC)"

backup-vacancy: ## Создать бэкап только таблицы vacancy
	@echo "$(YELLOW)💾 Создание бэкапа таблицы vacancy...$(NC)"
	@docker-compose exec -T db mysqldump -u root -ppassword yii2app vacancy > vacancy_backup_$(shell date +%Y%m%d_%H%M%S).sql
	@echo "$(GREEN)✅ Бэкап создан: vacancy_backup_$(shell date +%Y%m%d_%H%M%S).sql$(NC)"

# Разработка
dev-backend: ## Запустить Backend в режиме разработки
	@echo "$(YELLOW)🔧 Запуск Backend в режиме разработки...$(NC)"
	@docker-compose exec backend php yii serve 0.0.0.0:8080

dev-frontend: ## Запустить Frontend в режиме разработки
	@echo "$(YELLOW)🔧 Запуск Frontend в режиме разработки...$(NC)"
	@docker-compose exec frontend npm run dev

# Сборка
build-frontend: ## Собрать Frontend для продакшена
	@echo "$(YELLOW)🔨 Сборка Frontend...$(NC)"
	@docker-compose exec frontend npm run build

# Прямое подключение к БД
db-connect: ## Подключиться к MySQL
	@echo "$(YELLOW)🔌 Подключение к MySQL...$(NC)"
	@docker-compose exec db mysql -u root -ppassword

db-connect-app: ## Подключиться к базе данных приложения
	@echo "$(YELLOW)🔌 Подключение к базе данных приложения...$(NC)"
	@docker-compose exec db mysql -u root -ppassword yii2app

# Статистика
stats: ## Показать статистику использования ресурсов
	@echo "$(YELLOW)📊 Статистика использования ресурсов:$(NC)"
	@docker stats --no-stream

# По умолчанию показываем справку
.DEFAULT_GOAL := help
