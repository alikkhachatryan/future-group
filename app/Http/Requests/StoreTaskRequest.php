<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string|\Illuminate\Contracts\Validation\ValidationRule>>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'due_date' => [
                'required',
                'date_format:Y-m-d\TH:i:s',
            ],

            'status' => [
                'sometimes',
                'boolean',
            ],

            'priority' => [
                'required',
                Rule::enum(TaskPriority::class),
            ],

            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id'),
            ],
        ];
    }
}
