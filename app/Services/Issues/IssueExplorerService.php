<?php

namespace App\Services\Issues;

use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

final class IssueExplorerService
{
    /** @var array<int, string> */
    public const FILTER_KEYS = [
        'project',
        'assignee',
        'reporter',
        'status',
        'type',
        'priority',
        'milestone',
        'sprint',
        'tag',
        'ticket',
        'due_from',
        'due_to',
        'updated_from',
        'updated_to',
    ];

    /** @var array<int, string> */
    public const ALLOWED_SORTS = [
        'updated_desc',
        'updated_asc',
        'created_desc',
        'created_asc',
        'project_asc',
        'project_desc',
        'key_asc',
        'key_desc',
        'summary_asc',
        'summary_desc',
        'type_asc',
        'type_desc',
        'status_asc',
        'status_desc',
        'priority_asc',
        'priority_desc',
        'assignee_asc',
        'assignee_desc',
        'reporter_asc',
        'reporter_desc',
        'milestone_asc',
        'milestone_desc',
        'sprint_asc',
        'sprint_desc',
        'due_asc',
        'due_desc',
    ];

    public function __construct(
        private readonly IssueQueryParser $parser,
    ) {
    }

    /**
     * @param  array<string, mixed>  $savedState
     * @return array{
     *     query:string,
     *     sort:string,
     *     next:bool,
     *     filters:array<string, string>
     * }
     */
    public function stateFromRequest(Request $request, array $savedState = []): array
    {
        $filters = [];

        foreach (self::FILTER_KEYS as $key) {
            $filters[$key] = (string) $request->query($key, Arr::get($savedState, "filters.{$key}", ''));
        }

        $sort = (string) $request->query('sort', Arr::get($savedState, 'sort', 'updated_desc'));

        if (! in_array($sort, self::ALLOWED_SORTS, true)) {
            $sort = 'updated_desc';
        }

        return [
            'query' => trim((string) $request->query('query', Arr::get($savedState, 'query', ''))),
            'sort' => $sort,
            'next' => $request->boolean('next', (bool) Arr::get($savedState, 'next', false)),
            'filters' => $filters,
        ];
    }

    /**
     * @param  array{
     *     query:string,
     *     sort:string,
     *     next:bool,
     *     filters:array<string, string>
     * }  $state
     * @return array<string, mixed>
     */
    public function stateForStorage(array $state): array
    {
        return [
            'query' => $state['query'],
            'sort' => $state['sort'],
            'next' => $state['next'],
            'filters' => $state['filters'],
        ];
    }

    /**
     * @param  array{
     *     query:string,
     *     sort:string,
     *     next:bool,
     *     filters:array<string, string>
     * }  $state
     * @return array<string, mixed>
     */
    public function toQueryParameters(array $state, ?string $viewSlug = null): array
    {
        $params = [
            'view' => $viewSlug,
            'query' => $state['query'] !== '' ? $state['query'] : null,
            'sort' => $state['sort'] !== 'updated_desc' ? $state['sort'] : null,
            'next' => $state['next'] ? 1 : null,
        ];

        foreach ($state['filters'] as $key => $value) {
            $params[$key] = $value !== '' ? $value : null;
        }

        return array_filter($params, static fn (mixed $value): bool => $value !== null && $value !== '');
    }

    /** @return Builder<Project> */
    public function visibleProjectsQuery(User $user): Builder
    {
        if ($user->hasPermissionTo('is-super-admin')) {
            return Project::query();
        }

        return Project::query()->visibleTo($user);
    }

    /** @return Builder<Issue> */
    public function baseQuery(User $user): Builder
    {
        $query = Issue::query()
            ->select('issues.*')
            ->withMeta()
            ->with([
                'milestone:id,name,project_id,due_at',
                'sprint:id,name,project_id,state',
            ])
            ->withCount('tickets')
            ->selectSub(
                Project::query()
                    ->select('name')
                    ->whereColumn('projects.id', 'issues.project_id'),
                'project_name'
            )
            ->selectSub(
                \App\Models\IssuePriority::query()
                    ->select('name')
                    ->whereColumn('issue_priorities.id', 'issues.issue_priority_id'),
                'priority_name'
            )
            ->selectSub(
                \App\Models\IssueStatus::query()
                    ->select('name')
                    ->whereColumn('issue_statuses.id', 'issues.issue_status_id'),
                'status_name'
            )
            ->selectSub(
                \App\Models\IssueType::query()
                    ->select('name')
                    ->whereColumn('issue_types.id', 'issues.issue_type_id'),
                'type_name'
            )
            ->selectSub(
                User::query()
                    ->select('name')
                    ->whereColumn('users.id', 'issues.assignee_id'),
                'assignee_name'
            )
            ->selectSub(
                User::query()
                    ->select('name')
                    ->whereColumn('users.id', 'issues.reporter_id'),
                'reporter_name'
            )
            ->selectSub(
                \App\Models\Milestone::query()
                    ->select('name')
                    ->whereColumn('milestones.id', 'issues.milestone_id'),
                'milestone_name'
            )
            ->selectSub(
                \App\Models\Sprint::query()
                    ->select('name')
                    ->whereColumn('sprints.id', 'issues.sprint_id'),
                'sprint_name'
            );

        if ($user->hasPermissionTo('is-super-admin')) {
            return $query;
        }

        return $query->whereHas('project', fn (Builder $projectQuery) => $projectQuery->visibleTo($user));
    }

    /**
     * @param  array{
     *     query:string,
     *     sort:string,
     *     next:bool,
     *     filters:array<string, string>
     * }  $state
     */
    public function apply(Builder $query, User $user, array $state): Builder
    {
        $filters = $state['filters'];

        if ($filters['project'] !== '') {
            $query->where('project_id', $filters['project']);
        }

        $this->applyAssigneeFilter($query, $user, $filters['assignee']);
        $this->applyReporterFilter($query, $user, $filters['reporter']);
        $this->applyExactFilter($query, 'issue_status_id', $filters['status']);
        $this->applyExactFilter($query, 'issue_type_id', $filters['type']);
        $this->applyExactFilter($query, 'issue_priority_id', $filters['priority']);
        $this->applyMilestoneFilter($query, $filters['milestone']);
        $this->applySprintFilter($query, $filters['sprint']);
        $this->applyTagFilter($query, $filters['tag']);
        $this->applyTicketFilter($query, $filters['ticket']);
        $this->applyDateRangeFilter($query, 'due_at', $filters['due_from'], $filters['due_to']);
        $this->applyDateRangeFilter($query, 'updated_at', $filters['updated_from'], $filters['updated_to']);

        if ($state['next']) {
            $query->where('is_next', true);
        }

        if ($state['query'] !== '') {
            $this->applyQueryLanguage($query, $user, $state['query']);
        }

        $this->applySort($query, $state['sort']);

        return $query;
    }

    private function applyExactFilter(Builder $query, string $column, string $value): void
    {
        if ($value === '') {
            return;
        }

        $query->where($column, $value);
    }

    private function applyAssigneeFilter(Builder $query, User $user, string $value): void
    {
        if ($value === '') {
            return;
        }

        if ($value === 'me') {
            $query->where('assignee_id', $user->id);

            return;
        }

        if ($value === 'unassigned') {
            $query->whereNull('assignee_id');

            return;
        }

        $query->where('assignee_id', $value);
    }

    private function applyReporterFilter(Builder $query, User $user, string $value): void
    {
        if ($value === '') {
            return;
        }

        if ($value === 'me') {
            $query->where('reporter_id', $user->id);

            return;
        }

        $query->where('reporter_id', $value);
    }

    private function applyMilestoneFilter(Builder $query, string $value): void
    {
        if ($value === '') {
            return;
        }

        if ($value === 'none') {
            $query->whereNull('milestone_id');

            return;
        }

        $query->where('milestone_id', $value);
    }

    private function applySprintFilter(Builder $query, string $value): void
    {
        if ($value === '') {
            return;
        }

        if ($value === 'backlog') {
            $query->whereNull('sprint_id');

            return;
        }

        $query->where('sprint_id', $value);
    }

    private function applyTagFilter(Builder $query, string $value): void
    {
        $needle = trim($value);

        if ($needle === '') {
            return;
        }

        $query->withAnyTags([$needle]);
    }

    private function applyTicketFilter(Builder $query, string $value): void
    {
        $needle = trim($value);

        if ($needle === '') {
            return;
        }

        $like = '%'.$needle.'%';

        $query->whereHas('tickets', static function (Builder $ticketQuery) use ($like, $needle): void {
            $ticketQuery->where(function (Builder $builder) use ($like, $needle): void {
                $builder
                    ->where('key', 'like', $like)
                    ->orWhere('subject', 'like', $like);

                if (preg_match('/^[A-Z]+-\d+$/i', $needle) === 1) {
                    $builder->orWhere('key', strtoupper($needle));
                }
            });
        });
    }

    private function applyDateRangeFilter(Builder $query, string $column, string $from, string $to): void
    {
        if ($from !== '') {
            $query->whereDate($column, '>=', $from);
        }

        if ($to !== '') {
            $query->whereDate($column, '<=', $to);
        }
    }

    private function applyQueryLanguage(Builder $query, User $user, string $rawQuery): void
    {
        $parsed = $this->parser->parse($rawQuery);

        foreach ($parsed['terms'] as $term) {
            $this->applySearchTerm($query, $term);
        }

        $grouped = collect($parsed['clauses'])->groupBy('field');

        foreach ($grouped as $field => $clauses) {
            if (in_array($field, ['due', 'updated'], true)) {
                foreach ($clauses as $clause) {
                    $this->applyDateClause($query, $field, $clause['operator'], $clause['value']);
                }

                continue;
            }

            $equalityClauses = collect($clauses)
                ->filter(fn (array $clause): bool => in_array($clause['operator'], [':', '='], true))
                ->values();

            if ($equalityClauses->isNotEmpty()) {
                $query->where(function (Builder $builder) use ($field, $equalityClauses, $user): void {
                    foreach ($equalityClauses as $index => $clause) {
                        $this->applyFieldClause(
                            $builder,
                            $user,
                            $field,
                            $clause['value'],
                            $index === 0 ? 'where' : 'orWhere'
                        );
                    }
                });
            }
        }
    }

    private function applySearchTerm(Builder $query, string $term): void
    {
        $needle = trim($term);

        if ($needle === '') {
            return;
        }

        $numberSearch = null;
        $keySearch = null;

        if (preg_match('/^#?(\d+)$/', $needle, $numberMatches) === 1) {
            $numberSearch = (int) $numberMatches[1];
        }

        if (preg_match('/^[A-Z]+-\d+$/i', $needle) === 1) {
            $keySearch = strtoupper($needle);
        }

        $like = '%'.$needle.'%';

        $query->where(function (Builder $builder) use ($needle, $like, $numberSearch, $keySearch): void {
            $builder
                ->where('summary', 'like', $like)
                ->orWhere('description', 'like', $like)
                ->orWhereHas('project', static function (Builder $projectQuery) use ($like, $needle): void {
                    $projectQuery
                        ->where('name', 'like', $like)
                        ->orWhere('key', strtoupper($needle));
                });

            if ($numberSearch !== null) {
                $builder->orWhere('number', $numberSearch);
            }

            if ($keySearch !== null) {
                $builder->orWhere('key', $keySearch);
            }
        });
    }

    private function applyFieldClause(Builder $query, User $user, string $field, string $value, string $method): void
    {
        $normalized = trim($value);

        if ($normalized === '') {
            return;
        }

        $relationMethod = $method === 'orWhere' ? 'orWhereHas' : 'whereHas';
        $like = '%'.$normalized.'%';

        switch ($field) {
            case 'project':
                $query->{$relationMethod}('project', static function (Builder $builder) use ($normalized, $like): void {
                    $builder
                        ->where('id', $normalized)
                        ->orWhere('key', strtoupper($normalized))
                        ->orWhere('name', 'like', $like);
                });
                break;
            case 'assignee':
                if (in_array(strtolower($normalized), ['me', '@me'], true)) {
                    $query->{$method}('assignee_id', $user->id);

                    break;
                }

                if (in_array(strtolower($normalized), ['none', 'unassigned'], true)) {
                    if ($method === 'orWhere') {
                        $query->orWhereNull('assignee_id');
                    } else {
                        $query->whereNull('assignee_id');
                    }

                    break;
                }

                $query->{$relationMethod}('assignee', static function (Builder $builder) use ($normalized, $like): void {
                    $builder
                        ->where('id', $normalized)
                        ->orWhere('email', $normalized)
                        ->orWhere('name', 'like', $like);
                });
                break;
            case 'reporter':
                if (in_array(strtolower($normalized), ['me', '@me'], true)) {
                    $query->{$method}('reporter_id', $user->id);

                    break;
                }

                $query->{$relationMethod}('reporter', static function (Builder $builder) use ($normalized, $like): void {
                    $builder
                        ->where('id', $normalized)
                        ->orWhere('email', $normalized)
                        ->orWhere('name', 'like', $like);
                });
                break;
            case 'status':
                $query->{$relationMethod}('status', static function (Builder $builder) use ($normalized, $like): void {
                    $builder
                        ->where('id', $normalized)
                        ->orWhere('key', strtoupper($normalized))
                        ->orWhere('name', 'like', $like);
                });
                break;
            case 'type':
                $query->{$relationMethod}('type', static function (Builder $builder) use ($normalized, $like): void {
                    $builder
                        ->where('id', $normalized)
                        ->orWhere('key', strtoupper($normalized))
                        ->orWhere('name', 'like', $like);
                });
                break;
            case 'priority':
                $query->{$relationMethod}('priority', static function (Builder $builder) use ($normalized, $like): void {
                    $builder
                        ->where('id', $normalized)
                        ->orWhere('key', strtoupper($normalized))
                        ->orWhere('name', 'like', $like);
                });
                break;
            case 'milestone':
                $query->{$relationMethod}('milestone', static function (Builder $builder) use ($normalized, $like): void {
                    $builder
                        ->where('id', $normalized)
                        ->orWhere('name', 'like', $like)
                        ->orWhere('version', 'like', $like);
                });
                break;
            case 'sprint':
                if (strtolower($normalized) === 'backlog') {
                    if ($method === 'orWhere') {
                        $query->orWhereNull('sprint_id');
                    } else {
                        $query->whereNull('sprint_id');
                    }

                    break;
                }

                $query->{$relationMethod}('sprint', static function (Builder $builder) use ($normalized, $like): void {
                    $builder
                        ->where('id', $normalized)
                        ->orWhere('name', 'like', $like);
                });
                break;
            case 'tag':
                $tagMethod = $method === 'orWhere' ? 'orWhereHas' : 'whereHas';
                $query->{$tagMethod}('tags', static function (Builder $builder) use ($normalized): void {
                    $builder->where('name->en', $normalized);
                });
                break;
            case 'ticket':
                $query->{$relationMethod}('tickets', static function (Builder $builder) use ($normalized, $like): void {
                    $builder
                        ->where('key', strtoupper($normalized))
                        ->orWhere('key', 'like', $like)
                        ->orWhere('subject', 'like', $like);
                });
                break;
            case 'is':
                if (strtolower($normalized) === 'next') {
                    $query->{$method}('is_next', true);
                }
                break;
            case 'has':
                if (in_array(strtolower($normalized), ['ticket', 'tickets'], true)) {
                    $query->{$relationMethod}('tickets', static fn (Builder $builder) => $builder);
                }
                break;
        }
    }

    private function applyDateClause(Builder $query, string $field, string $operator, string $value): void
    {
        $column = $field === 'due' ? 'due_at' : 'updated_at';
        $normalized = trim($value);

        if ($normalized === '') {
            return;
        }

        $dateOperator = match ($operator) {
            ':' => '=',
            default => $operator,
        };

        if (! in_array($dateOperator, ['=', '<', '<=', '>', '>='], true)) {
            return;
        }

        $query->whereDate($column, $dateOperator, $normalized);
    }

    private function applySort(Builder $query, string $sort): void
    {
        switch ($sort) {
            case 'updated_asc':
                $query->orderBy('updated_at', 'asc');
                break;
            case 'updated_desc':
                $query->orderBy('updated_at', 'desc');
                break;
            case 'created_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'created_desc':
                $query->orderBy('created_at', 'desc');
                break;
            case 'project_asc':
                $query->orderByRaw("COALESCE(project_name, 'zzzz') ASC")
                    ->orderBy('updated_at', 'desc');
                break;
            case 'project_desc':
                $query->orderByRaw("COALESCE(project_name, '') DESC")
                    ->orderBy('updated_at', 'desc');
                break;
            case 'key_asc':
                $query->orderBy('key', 'asc');
                break;
            case 'key_desc':
                $query->orderBy('key', 'desc');
                break;
            case 'summary_asc':
                $query->orderBy('summary', 'asc');
                break;
            case 'summary_desc':
                $query->orderBy('summary', 'desc');
                break;
            case 'type_asc':
                $query->orderByRaw("COALESCE(type_name, 'zzzz') ASC")
                    ->orderBy('updated_at', 'desc');
                break;
            case 'type_desc':
                $query->orderByRaw("COALESCE(type_name, '') DESC")
                    ->orderBy('updated_at', 'desc');
                break;
            case 'status_asc':
                $query->orderByRaw("COALESCE(status_name, 'zzzz') ASC")
                    ->orderBy('updated_at', 'desc');
                break;
            case 'status_desc':
                $query->orderByRaw("COALESCE(status_name, '') DESC")
                    ->orderBy('updated_at', 'desc');
                break;
            case 'priority_asc':
                $query->orderByRaw("COALESCE(priority_name, 'zzzz') ASC")
                    ->orderBy('updated_at', 'desc');
                break;
            case 'priority_desc':
                $query->orderByRaw("COALESCE(priority_name, '') DESC")
                    ->orderBy('updated_at', 'desc');
                break;
            case 'assignee_asc':
                $query->orderByRaw("COALESCE(assignee_name, 'zzzz') ASC")
                    ->orderBy('updated_at', 'desc');
                break;
            case 'assignee_desc':
                $query->orderByRaw("COALESCE(assignee_name, '') DESC")
                    ->orderBy('updated_at', 'desc');
                break;
            case 'reporter_asc':
                $query->orderByRaw("COALESCE(reporter_name, 'zzzz') ASC")
                    ->orderBy('updated_at', 'desc');
                break;
            case 'reporter_desc':
                $query->orderByRaw("COALESCE(reporter_name, '') DESC")
                    ->orderBy('updated_at', 'desc');
                break;
            case 'milestone_asc':
                $query->orderByRaw("COALESCE(milestone_name, 'zzzz') ASC")
                    ->orderBy('updated_at', 'desc');
                break;
            case 'milestone_desc':
                $query->orderByRaw("COALESCE(milestone_name, '') DESC")
                    ->orderBy('updated_at', 'desc');
                break;
            case 'sprint_asc':
                $query->orderByRaw("COALESCE(sprint_name, 'zzzz') ASC")
                    ->orderBy('updated_at', 'desc');
                break;
            case 'sprint_desc':
                $query->orderByRaw("COALESCE(sprint_name, '') DESC")
                    ->orderBy('updated_at', 'desc');
                break;
            case 'due_asc':
                $query->orderByRaw('CASE WHEN due_at IS NULL THEN 1 ELSE 0 END')
                    ->orderBy('due_at', 'asc')
                    ->orderBy('updated_at', 'desc');
                break;
            case 'due_desc':
                $query->orderByRaw('CASE WHEN due_at IS NULL THEN 1 ELSE 0 END')
                    ->orderBy('due_at', 'desc')
                    ->orderBy('updated_at', 'desc');
                break;
            default:
                $query->orderBy('updated_at', 'desc');
        }
    }
}
