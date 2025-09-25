<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Issue;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicProjectIssuesController extends Controller
{
    public function index(Request $request, string $slug): JsonResource
    {
        $project = Project::query()->where('public_slug', $slug)->firstOrFail();
        abort_unless($project->isPubliclyEmbeddable(), 404);

        $q = Issue::query()->with(['status','type','assignee','tags'])
            ->where('project_id', $project->id)
            ->where('is_public', true);

        $s = $request->input('s');
        if (filled($s)) {
            // Escape special LIKE characters
            $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $s);
            $likePattern = "%{$escaped}%";
            $q->where(static function ($qq) use ($likePattern): void {
                $qq->where('title', 'like', $likePattern, 'and', false)
                   ->orWhere('key', 'like', $likePattern, 'and', false);
            });
        }
        return JsonResource::collection($q->limit(500)->get()->map(fn ($i) => [
            'id' => (string) $i->id,
            'title' => $i->title,
            'status' => $i->status?->only(['id','name','is_done']),
            'type' => $i->type?->only(['id','name']),
            'assignee' => $i->assignee?->only(['id','name']),
            'tags' => $i->tags?->pluck('name')->all() ?? [],
        ]));
    }
}
