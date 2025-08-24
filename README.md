# Сервис вакансий

REST API для хранения и управления вакансиями. Проект построен на Yii2 (backend) + Nuxt 3 (frontend).

## Быстрый запуск

### Через Docker (Рекомендуемый способ)

```bash
# Клонирование проекта
git clone https://github.com/sliva-name/TestCaseFixPrice.git
cd TestCaseFixPrice

# Запуск всех сервисов
docker-compose up -d --build
```

**Примечание:** Миграции базы данных и заполнение тестовыми данными происходит автоматически при запуске backend контейнера.

### Ручное выполнение миграций (если требуется)

```bash
# Выполнение миграций
docker-compose exec backend php yii migrate --interactive=0

# Заполнение тестовыми данными
docker-compose exec backend php yii seed/vacancies

# Проверка подключения к БД
docker-compose exec backend php yii db/test
```

### Через Makefile

```bash
# Запуск проекта
make up

# Остановка проекта  
make down

# Просмотр логов
make logs

# Миграции
make migrate
```

## Доступ к сервисам

- **Frontend**: http://localhost
- **Backend API**: http://localhost/api
- **База данных**: localhost:3306 (пользователь: `root`, пароль: `password`)

## Архитектура

Проект состоит из 4 Docker контейнеров:

1. **nginx** (порт 80) - веб-сервер и прокси
   - Обслуживает статические файлы frontend
   - Проксирует `/api/*` запросы к backend
   - Проксирует остальные запросы к frontend

2. **backend** (PHP-FPM) - Yii2 API сервер
   - Обрабатывает API запросы
   - Автоматически выполняет миграции при запуске
   - Заполняет БД тестовыми данными при первом запуске

3. **frontend** (Node.js) - Nuxt 3 SPA
   - Веб-интерфейс для работы с вакансиями
   - Взаимодействует с backend через API

4. **db** (MySQL 8.0) - база данных
   - Хранит данные вакансий
   - Автоматически создается при запуске

## API Endpoints

### Получение списка вакансий
```http
GET /api/vacancies
```

**Параметры:**
- `page` - номер страницы (по умолчанию: 1)
- `per_page` - количество на странице (по умолчанию: 10, макс: 100)
- `sort_by` - поле сортировки: `title`, `salary`, `created_at`
- `sort_order` - порядок: `asc`, `desc`

**Пример:**
```http
GET /api/vacancies?page=1&per_page=5&sort_by=salary&sort_order=desc
```

### Получение конкретной вакансии
```http
GET /api/vacancy/{id}
```

**Параметры:**
- `fields` - список полей: `title`, `description` (опционально)

**Примеры:**
```http
GET /api/vacancy/1
GET /api/vacancy/1?fields=title
GET /api/vacancy/1?fields=title,description
```

### Создание вакансии
```http
POST /api/vacancy
Content-Type: application/json

{
  "title": "PHP Developer",
  "description": "Описание вакансии",
  "salary": 150000
}
```

## Структура проекта

```
TestCase2/
├── backend/           # Yii2 API
│   ├── controllers/   # API контроллеры
│   ├── models/        # Модели данных
│   ├── services/      # Бизнес-логика
│   ├── migrations/    # Миграции БД
│   └── config/        # Конфигурация
├── frontend/          # Nuxt 3 SPA
│   ├── pages/         # Страницы
│   ├── components/    # Компоненты
│   └── composables/   # API клиент
├── nginx/             # Nginx конфигурация
└── docker-compose.yml # Docker конфигурация
```

## Разработка

### Требования
- Docker & Docker Compose


## База данных

Проект использует MySQL. Схема создается автоматически через миграции.

### Структура таблицы `vacancy`:
- `id` - уникальный идентификатор
- `title` - название вакансии
- `description` - описание
- `salary` - зарплата
- `created_at` - дата создания
- `updated_at` - дата обновления