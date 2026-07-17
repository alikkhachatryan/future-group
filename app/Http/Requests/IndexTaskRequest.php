<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('all')) {
            $this->merge([
                'all' => filter_var(
                    $this->input('all'),
                    FILTER_VALIDATE_BOOLEAN,
                    FILTER_NULL_ON_FAILURE
                ),
            ]);
        }
    }


    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'search' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
            ],

            'sort' => [
                'sometimes',
                Rule::in(['due_date', 'created_at']),
            ],

            'direction' => [
                'sometimes',
                Rule::in(['asc', 'desc']),
            ],

            'per_page' => [
                'sometimes',
                'integer',
                'min:1',
                'max:100',
            ],

            'page' => [
                'sometimes',
                'integer',
                'min:1',
            ],
            'all' => [
                'sometimes',
                'boolean',
            ],
        ];
    }
}
