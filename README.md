# Сервис вакансий

REST API для хранения и управления вакансиями. Проект построен на Yii2 (backend) + Nuxt 3 (frontend).

## Быстрый запуск

### Через Docker

```bash
# Клонирование проекта
git clone <repository-url>
cd TestCase2

# Запуск всех сервисов
docker-compose up -d

# Выполнение миграций
docker-compose exec backend php yii migrate --interactive=0
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

- **Frontend**: http://localhost:3000
- **Backend API**: http://localhost/api
- **Backend Admin**: http://localhost

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
- Node.js 18+ (для локальной разработки frontend)
- PHP 8.1+ (для локальной разработки backend)

### Frontend разработка
```bash
cd frontend
npm install
npm run dev
```

### Backend разработка
```bash
cd backend
composer install
php yii serve
```

## База данных

Проект использует MySQL. Схема создается автоматически через миграции.

### Структура таблицы `vacancy`:
- `id` - уникальный идентификатор
- `title` - название вакансии
- `description` - описание
- `salary` - зарплата
- `created_at` - дата создания
- `updated_at` - дата обновления