<?php

namespace App\Http\Resources\Api\V1\Feedback;

use App\Models\FeedbackBoard;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property FeedbackBoard $resource */
class FeedbackBoardResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray($request): array
    {
        return [
            'id' => (string) $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'public_url' => $this->public_url,
            'service_product' => [
                'id' => (string) $this->product?->id,
                'name' => $this->product?->name,
            ],
            'categories' => $this->categories->map(fn ($category) => [
                'id' => (string) $category->id,
                'slug' => $category->slug,
                'name' => $category->name,
                'color' => $category->color,
            ])->values(),
            'statuses' => $this->statuses->map(fn ($status) => [
                'id' => (string) $status->id,
                'slug' => $status->slug,
                'name' => $status->name,
                'color' => $status->color,
                'is_default' => (bool) $status->is_default,
                'is_terminal' => (bool) $status->is_terminal,
            ])->values(),
            'default_sort' => $this->default_sort,
        ];
    }
}
