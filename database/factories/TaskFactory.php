<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->text(),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'status' => $this->faker->boolean(),
            'priority' => $this->faker->randomElement(TaskPriority::cases())->value,
            'category_id' => Category::factory(),
        ];
    }
}
