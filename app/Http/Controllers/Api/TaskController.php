<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IndexTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    /**
     * Получить список задач
     *
     * Возвращает список задач с поиском по названию,
     * сортировкой и пагинацией.
     */
    public function index(
        IndexTaskRequest $request
    ): AnonymousResourceCollection {
        $validated = $request->validated();

        $query = Task::query()
            ->with('category')
            ->search($validated['search'] ?? null)
            ->orderBy(
                $validated['sort'] ?? 'created_at',
                $validated['direction'] ?? 'desc'
            );

        if ($request->boolean('all')) {
            return TaskResource::collection($query->get());
        }

        $tasks = $query
            ->paginate($validated['per_page'] ?? 15)
            ->withQueryString();

        return TaskResource::collection($tasks);
    }

    /**
     * Создать задачу
     *
     * Создаёт новую задачу.
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::query()->create($request->validated());

        $task->load('category');

        return (new TaskResource($task))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Получить задачу
     *
     * Возвращает задачу по идентификатору.
     */
    public function show(Task $task): TaskResource
    {
        $task->load('category');

        return new TaskResource($task);
    }

    /**
     * Обновить задачу
     *
     * Обновляет переданные поля задачи.
     */
    public function update(
        UpdateTaskRequest $request,
        Task $task
    ): TaskResource {
        $task->update($request->validated());
        $task->load('category');

        return new TaskResource($task);
    }

    /**
     * Удалить задачу
     *
     * Удаляет задачу по идентификатору.
     */
    public function destroy(Task $task): Response
    {
        $task->delete();

        return response()->noContent();
    }
}
