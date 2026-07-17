<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date?->format('Y-m-d\TH:i:s'),
            'status' => $this->status,
            'priority' => $this->priority->value,
            'category' => new CategoryResource(
                $this->whenLoaded('category')
            ),
            'created_at' => $this->created_at?->format('Y-m-d\TH:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d\TH:i:s'),
        ];
    }
}
