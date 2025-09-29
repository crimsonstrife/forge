<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PublicIssueResource;
use App\Models\Issue;
use App\Models\Project;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PublicProjectIssuesController extends Controller
{
    public function index(Request $request, string $slug): AnonymousResourceCollection
    {
        $project = Project::query()
            ->where('public_slug', $slug)
            ->firstOrFail();

        abort_unless($project->isPubliclyEmbeddable(), 404);

        // -------- Filters --------
        $search   = (string) $request->query('s', '');
        $perPage  = max(1, min((int) $request->query('per_page', 50), 200));
        $sort     = (string) $request->query('sort', '-created_at'); // e.g., "created_at" or "-created_at"

        // -------- Visible (public) issues list with aggregates from ALL children --------
        $visible = Issue::query()
            ->with([
                'status:id,name,is_done',
                'type:id,name',
                'assignee:id,name',
                'parent:id,summary',
                'tags:id,name',
            ])
            ->where('project_id', $project->id)
            ->where('is_public', true)
            ->withCount([
                'children as children_count',
                'children as children_done_count' => fn ($q) =>
                $q->whereHas('status', fn ($s) => $s->where('is_done', true)),
            ])
            ->withSum('children as children_points_total', 'story_points')
            ->withSum([
                'children as children_points_done' => fn ($q) =>
                $q->whereHas('status', fn ($s) => $s->where('is_done', true))
            ], 'story_points');

        if ($search !== '') {
            $escaped = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $search);
            $like    = "%{$escaped}%";
            $visible->where(static function (Builder $q) use ($like): void {
                $q->where('summary', 'like', $like)
                    ->orWhere('key', 'like', $like);
            });
        }

        // Aggregates that include *all* children (public + private)
        // children_count
        $visible->selectSub(
            Issue::query()
                ->from('issues as c')
                ->selectRaw('count(*)')
                ->whereColumn('c.parent_id', 'issues.id'),
            'children_count'
        );
        // children_done_count
        $visible->selectSub(
            Issue::query()
                ->from('issues as c')
                ->join('issue_statuses as s', 's.id', '=', 'c.issue_status_id')
                ->where('s.is_done', true)
                ->whereColumn('c.parent_id', 'issues.id')
                ->selectRaw('count(*)'),
            'children_done_count'
        );
        // children_points_total
        $visible->selectSub(
            Issue::query()
                ->from('issues as c')
                ->whereColumn('c.parent_id', 'issues.id')
                ->selectRaw('COALESCE(SUM(c.story_points), 0)'),
            'children_points_total'
        );
        // children_points_done
        $visible->selectSub(
            Issue::query()
                ->from('issues as c')
                ->join('issue_statuses as s', 's.id', '=', 'c.issue_status_id')
                ->where('s.is_done', true)
                ->whereColumn('c.parent_id', 'issues.id')
                ->selectRaw('COALESCE(SUM(c.story_points), 0)'),
            'children_points_done'
        );

        // Sorting
        if ($sort !== '') {
            $dir = str_starts_with($sort, '-') ? 'desc' : 'asc';
            $col = ltrim($sort, '-');
            $visible->orderBy($col, $dir);
        }

        $issues = $visible->paginate($perPage)->appends($request->query());

        // -------- Project-level summary (includes private issues) --------
        $allIssues = Issue::query()->where('project_id', $project->id);

        // If a team/tenant scope hides issues on this public endpoint:
        // $allIssues = Issue::withoutGlobalScopes()->where('project_id', $project->id); TODO: Implement this later

        $total = (clone $allIssues)->count();
        $done  = (clone $allIssues)->whereHas('status', static fn (Builder $q) => $q->where('is_done', true))->count();
        $open  = $total - $done;

        $publicTotal = Issue::query()
            ->where('project_id', $project->id)
            ->where('is_public', true)
            ->count();
        $publicDone = Issue::query()
            ->where('project_id', $project->id)
            ->where('is_public', true)
            ->whereHas('status', static fn (Builder $q) => $q->where('is_done', true))
            ->count();
        $publicOpen = $publicTotal - $publicDone;

        $progressPercent = $total > 0 ? (int) round(($done / $total) * 100) : 0;

        $projectSummary = [
            'id' => (string) $project->id,
            'name' => $project->name,
            'key' => $project->key,
            'public_slug' => $project->public_slug,
            'progress_percent' => $progressPercent,
            'counts' => [
                'all' => ['total' => $total, 'open' => $open, 'done' => $done],
                'public' => ['total' => $publicTotal, 'open' => $publicOpen, 'done' => $publicDone],
            ],
            'updated_at' => optional($project->updated_at)?->toISOString(),
        ];

        // -------- Response (paginated resource + extra top-level data) --------
        return PublicIssueResource::collection($issues)
            ->additional([
                'project' => $projectSummary,
                // Echo back applied filters so the client can display chips, etc.
                'filters' => [
                    's' => $search,
                    'sort' => $sort,
                    'per_page' => $perPage,
                ],
            ]);
    }
}
