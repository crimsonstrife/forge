<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/** @property \App\Models\Project $resource */
final class ProjectResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray($request): array
    {
        return [
            'id'   => (string) $this->id,
            'key'  => $this->key,
            'name' => $this->name,
        ];
    }
}
