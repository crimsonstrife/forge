<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/** @property \App\Models\Issue $resource */
final class IssueResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray($request): array
    {
        return [
            'id'      => (string) $this->id,
            'key'     => $this->key,
            'summary' => $this->summary,
            'description' => $this->description,
            'status'  => [
                'id' => $this->status?->id,
                'name' => $this->status?->name,
                'is_done' => (bool) $this->status?->is_done,
            ],
            'priority' => [
                'id' => $this->priority?->id,
                'name' => $this->priority?->name,
            ],
            'type' => [
                'id' => $this->type?->id,
                'key' => $this->type?->key,
                'name' => $this->type?->name,
            ],
            'assignee' => $this->assignee ? [
                'id' => $this->assignee->id,
                'name' => $this->assignee->name,
            ] : null,
            'project' => [
                'id' => $this->project?->id,
                'key' => $this->project?->key,
                'name' => $this->project?->name,
            ],
            'tags' => $this->whenLoaded('tags', fn () => $this->tags->pluck('name')->all()),
            'attachments_count' => $this->whenCounted('media'),
            'comments_count'    => $this->whenCounted('comments'),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
