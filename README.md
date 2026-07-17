# Task Manager API

REST API для управления задачами на Laravel 13.

## Реализовано

- создание, просмотр, обновление и удаление задач;
- поиск по названию;
- сортировка по `due_date` и `created_at`;
- пагинация;
- получение полного списка через `all=true`;
- категории в отдельной таблице;
- валидация через Form Request;
- API Resources;
- Swagger / OpenAPI;
- Feature-тесты;
- запуск через Docker.

## Стек

- PHP 8.4+
- Laravel 13
- MySQL 8.4
- Docker Compose
- PHPUnit
- Scramble OpenAPI

## Запуск через Docker

```bash
git clone <REPOSITORY_URL>
cd future-test-task
```

Создать `.env`:

```bash
cp .env.example .env
```

Для Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Сгенерировать ключ приложения:

```bash
php artisan key:generate
```

Запустить контейнеры:

```bash
docker compose up -d --build
```

Выполнить миграции и сидеры:

```bash
docker compose exec app php artisan migrate --seed
```

API будет доступно по адресу:

```text
http://localhost:8000/api/tasks
```

Swagger:

```text
http://localhost:8000/docs/api
```

OpenAPI JSON:

```text
http://localhost:8000/docs/api.json
```

## Локальный запуск без Docker

Установить зависимости:

```bash
composer install
```

Создать и настроить `.env`:

```bash
cp .env.example .env
php artisan key:generate
```

Указать подключение к MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=future_group
DB_USERNAME=root
DB_PASSWORD=
```

Запустить миграции:

```bash
php artisan migrate --seed
```

Запустить сервер:

```bash
php artisan serve
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

Параметры списка:

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
- обновление;
- очистка описания;
- удаление;
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

## Примечания

- авторизация не реализована, потому что её нет в требованиях;
- CRUD категорий не добавлен, категории используются как справочник;
- `.env` не хранится в Git;
- Docker-конфигурация предназначена для локального запуска и проверки задания.
