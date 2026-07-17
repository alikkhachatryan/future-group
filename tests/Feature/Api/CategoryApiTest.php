<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_categories_sorted_by_name(): void
    {
        Category::factory()->create([
            'name' => 'Работа',
        ]);

        Category::factory()->create([
            'name' => 'Дом',
        ]);

        Category::factory()->create([
            'name' => 'Личное',
        ]);

        $response = $this->getJson('/api/categories');

        $response
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                    ],
                ],
            ])
            ->assertJsonPath('data.0.name', 'Дом')
            ->assertJsonPath('data.1.name', 'Личное')
            ->assertJsonPath('data.2.name', 'Работа');
    }
}
