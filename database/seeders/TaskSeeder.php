<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::query()->pluck('id');

        if ($categories->isEmpty()) {
            return;
        }

        Task::factory()
            ->count(20)
            ->state(fn () => [
                'category_id' => $categories->random(),
            ])
            ->create();
    }
}
