# Task Manager API

REST API для управления задачами на Laravel 13.

## Реализовано

- CRUD для задач;
- поиск задач по названию;
- сортировка по `due_date` и `created_at`;
- пагинация;
- получение полного списка задач через `all=true`;
- категории в отдельной таблице;
- валидация через Form Request;
- API Resources;
- Swagger / OpenAPI через Scramble;
- feature-тесты;
- запуск через Docker Compose.

## Стек

- PHP 8.3+ локально или PHP 8.4 в Docker
- Laravel 13
- MySQL 8.4
- Docker Compose
- PHPUnit
- Scramble OpenAPI

## Получение проекта

```bash
git clone <REPOSITORY_URL>
cd future-group
```

## Запуск

Создать файл окружения:

```bash
cp .env.example .env
```

Для Windows PowerShell:

```powershell
Copy-Item .env.example .env -Force
```

Запустить контейнеры:

```bash
docker compose up -d --build --force-recreate
```

Очистить кэш конфигурации:

```bash
docker compose exec app php artisan optimize:clear
```

Сгенерировать ключ приложения внутри контейнера:

```bash
docker compose exec app php artisan key:generate
```

Выполнить миграции и сидеры:

```bash
docker compose exec app php artisan migrate --seed
```

API будет доступно по адресу:

```text
http://localhost:8000/api/tasks
```

Swagger UI:

```text
http://localhost:8000/docs/api
```

OpenAPI JSON:

```text
http://localhost:8000/docs/api.json
```

## API

### Задачи

```text
GET    /api/tasks
POST   /api/tasks
GET    /api/tasks/{task}
PUT    /api/tasks/{task}
PATCH  /api/tasks/{task}
DELETE /api/tasks/{task}
```

Параметры списка задач:

| Параметр | Описание |
|---|---|
| `search` | поиск по названию |
| `sort` | `due_date` или `created_at` |
| `direction` | `asc` или `desc` |
| `per_page` | количество элементов на странице, максимум 100 |
| `page` | номер страницы |
| `all` | `true` — вернуть список без пагинации |

Пример создания задачи:

```json
{
    "title": "Подготовить тестовое задание",
    "description": "Реализовать REST API",
    "due_date": "2026-07-25T18:00:00",
    "status": false,
    "priority": "high",
    "category_id": 1
}
```

Допустимые значения `priority`:

```text
low
medium
high
```

### Категории

```text
GET /api/categories
```

## Тестирование

Запуск тестов локально:

```bash
php artisan test
```

Запуск тестов в Docker:

```bash
docker compose exec app php artisan test
```

Тестами проверяются:

- пагинация;
- получение списка без пагинации;
- поиск;
- сортировка;
- создание задачи;
- значение статуса по умолчанию;
- получение задачи по ID;
- обновление задачи;
- очистка описания;
- удаление задачи;
- ошибки валидации;
- ответ `404`;
- получение категорий.

Кроме автоматических тестов API проверялось вручную через Postman и Swagger UI.

## Основные HTTP-коды

| Код | Значение |
|---|---|
| `200` | успешное получение или обновление |
| `201` | задача создана |
| `204` | задача удалена |
| `404` | задача не найдена |
| `422` | ошибка валидации |
