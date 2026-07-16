<?php

namespace App\Models;

use App\Enums\TaskPriority;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'due_date', 'status', 'priority', 'category_id'];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'status' => 'boolean',
            'priority' => TaskPriority::class,
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
