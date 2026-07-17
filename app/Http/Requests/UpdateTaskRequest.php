<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
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
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'sometimes',
                'nullable',
                'string',
            ],

            'due_date' => [
                'sometimes',
                'required',
                'date_format:Y-m-d\TH:i:s',
            ],

            'status' => [
                'sometimes',
                'required',
                'boolean',
            ],

            'priority' => [
                'sometimes',
                'required',
                Rule::enum(TaskPriority::class),
            ],

            'category_id' => [
                'sometimes',
                'required',
                'integer',
                Rule::exists('categories', 'id'),
            ],
        ];
    }
}
