<?php
namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read string $id
 * @property-read string $summary
 * @property-read string|null $description
 * @property-read array{id:string,name:string,is_done?:bool}|null $status
 * @property-read array{id:string,name:string}|null $type
 * @property-read array{id:string,name:string}|null $assignee
 * @property-read array{id:string,summary:string}|null $parent
 * @property-read int|null $children_count
 * @property-read int|null $children_done_count
 * @property-read int|null $children_points_total
 * @property-read int|null $children_points_done
 */
class PublicIssueResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $childrenCount = (int) ($this->children_count ?? 0);
        $childrenDone  = (int) ($this->children_done_count ?? 0);

        $progressPercent = $childrenCount > 0
            ? (int) round(($childrenDone / $childrenCount) * 100)
            : ((bool) ($this->status['is_done'] ?? false) ? 100 : 0);

        return [
            'id' => (string) $this->id,
            'description' => $this->description,
            'summary' => $this->summary,
            'status' => $this->status?->only(['id', 'name', 'is_done']),
            'type' => $this->type?->only(['id', 'name']),
            'assignee' => $this->assignee?->only(['id', 'name']),
            'parent' => $this->parent?->only(['id', 'summary']),
            'progress_percent' => $progressPercent,
            // Children aggregates include both public and private children:
            'children_count' => $childrenCount,
            'children_done_count' => $childrenDone,
            'children_points_total' => (int) ($this->children_points_total ?? 0),
            'children_points_done' => (int) ($this->children_points_done ?? 0),
            'tags' => $this->tags?->pluck('name')->all() ?? [],
        ];
    }
}
