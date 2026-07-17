<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_paginates_tasks_by_default(): void
    {
        $category = Category::factory()->create();

        Task::factory()
            ->count(20)
            ->for($category)
            ->create();

        $response = $this->getJson('/api/tasks');

        $response
            ->assertOk()
            ->assertJsonCount(15, 'data')
            ->assertJsonPath('meta.total', 20)
            ->assertJsonStructure([
                'data',
                'links',
                'meta',
            ]);
    }

    public function test_it_searches_tasks_by_title(): void
    {
        $category = Category::factory()->create();

        Task::factory()->for($category)->create([
            'title' => 'Подготовить документацию API',
        ]);

        Task::factory()->for($category)->create([
            'title' => 'Купить продукты',
        ]);

        $response = $this->getJson(
            '/api/tasks?search=документацию'
        );

        $response
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath(
                'data.0.title',
                'Подготовить документацию API'
            );
    }

    public function test_it_sorts_tasks_by_due_date(): void
    {
        $category = Category::factory()->create();

        Task::factory()->for($category)->create([
            'title' => 'Поздняя задача',
            'due_date' => '2026-08-20 18:00:00',
        ]);

        Task::factory()->for($category)->create([
            'title' => 'Ранняя задача',
            'due_date' => '2026-07-20 18:00:00',
        ]);

        $response = $this->getJson(
            '/api/tasks?sort=due_date&direction=asc'
        );

        $response
            ->assertOk()
            ->assertJsonPath('data.0.title', 'Ранняя задача')
            ->assertJsonPath('data.1.title', 'Поздняя задача');
    }

    public function test_it_rejects_invalid_sort_parameter(): void
    {
        $response = $this->getJson(
            '/api/tasks?sort=title'
        );

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['sort']);
    }

    public function test_it_returns_all_tasks_when_all_parameter_is_true(): void
    {
        $category = Category::factory()->create();

        Task::factory()
            ->count(20)
            ->for($category)
            ->create();

        $response = $this->getJson('/api/tasks?all=true');

        $response
            ->assertOk()
            ->assertJsonCount(20, 'data')
            ->assertJsonMissingPath('meta')
            ->assertJsonMissingPath('links');
    }

    public function test_it_creates_a_task(): void
    {
        $category = Category::factory()->create();

        $payload = [
            'title' => 'Подготовить Swagger',
            'description' => 'Описать методы API',
            'due_date' => '2026-07-25T18:00:00',
            'status' => false,
            'priority' => 'high',
            'category_id' => $category->id,
        ];

        $response = $this->postJson('/api/tasks', $payload);

        $response
            ->assertCreated()
            ->assertJsonPath('data.title', $payload['title'])
            ->assertJsonPath('data.priority', 'high')
            ->assertJsonPath('data.category.id', $category->id);

        $this->assertDatabaseHas('tasks', [
            'title' => $payload['title'],
            'priority' => 'high',
            'category_id' => $category->id,
        ]);
    }

    public function test_it_returns_a_single_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->getJson("/api/tasks/{$task->id}");

        $response
            ->assertOk()
            ->assertJsonPath('data.id', $task->id)
            ->assertJsonPath('data.title', $task->title)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'description',
                    'due_date',
                    'status',
                    'priority',
                    'category' => [
                        'id',
                        'name',
                    ],
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_it_updates_a_task(): void
    {
        $task = Task::factory()->create([
            'title' => 'Старое название',
            'status' => false,
            'priority' => 'low',
        ]);

        $response = $this->patchJson("/api/tasks/{$task->id}", [
            'title' => 'Новое название',
            'status' => true,
            'priority' => 'high',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.title', 'Новое название')
            ->assertJsonPath('data.status', true)
            ->assertJsonPath('data.priority', 'high');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Новое название',
            'status' => true,
            'priority' => 'high',
        ]);
    }

    public function test_it_deletes_a_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    public function test_it_returns_not_found_for_missing_task(): void
    {
        $this->getJson('/api/tasks/999999')
            ->assertNotFound();
    }

    public function test_it_can_clear_task_description(): void
    {
        $task = Task::factory()->create([
            'description' => 'Старое описание',
        ]);

        $response = $this->patchJson("/api/tasks/{$task->id}", [
            'description' => null,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.description', null);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'description' => null,
        ]);
    }

    public function test_new_task_is_pending_by_default(): void
    {
        $category = Category::factory()->create();

        $response = $this->postJson('/api/tasks', [
            'title' => 'Новая задача',
            'due_date' => '2026-07-25T18:00:00',
            'priority' => 'medium',
            'category_id' => $category->id,
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.status', false);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Новая задача',
            'status' => false,
        ]);
    }

    public function test_it_rejects_invalid_task_data_on_creation(): void
    {
        $response = $this->postJson('/api/tasks', [
            'title' => '',
            'due_date' => 'invalid-date',
            'priority' => 'urgent',
            'category_id' => 999999,
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'title',
                'due_date',
                'priority',
                'category_id',
            ]);
    }
}
