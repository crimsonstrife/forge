<?php

namespace App\Services\Dashboards;

use App\Models\Activity;
use App\Models\Issue;
use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Milestone;
use App\Models\Project;
use App\Models\Sprint;
use App\Models\Ticket;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

final class PersonalDashboardService
{
    /**
     * @return array{
     *     signals: array<string, mixed>,
     *     widgets: array<string, mixed>
     * }
     */
    public function build(User $user): array
    {
        $signals = $this->signals($user);

        $myOpenIssues = $this->myOpenIssues($user, $signals['visible_project_ids']);
        $dueSoon = $this->dueSoon($user, $signals['visible_project_ids']);
        $projectPortfolio = $this->projectPortfolio($user);
        $recentActivity = $this->activityGroups($signals['visible_project_ids']);
        $mySprint = $this->mySprint($user, $signals['visible_project_ids']);
        $teamDelivery = $this->teamDelivery($signals['visible_project_ids'], $signals['team_context']);
        $supportQueue = $signals['can_view_support']
            ? $this->supportQueue($user)
            : null;
        $releaseHealth = $this->releaseHealth($signals['visible_project_ids']);

        return [
            'signals' => $signals,
            'widgets' => [
                'my_open_issues' => [
                    'items' => $myOpenIssues,
                    'status_summary' => $this->statusSummary($user, $signals['visible_project_ids']),
                ],
                'due_soon' => [
                    'items' => $dueSoon,
                ],
                'project_portfolio' => [
                    'items' => $projectPortfolio,
                ],
                'recent_activity' => [
                    'groups' => $recentActivity,
                ],
                'my_sprint' => $mySprint,
                'team_delivery' => $teamDelivery,
                'support_queue' => $supportQueue,
                'release_health' => $releaseHealth,
                'exec_summary' => $this->execSummary(
                    $signals,
                    $teamDelivery,
                    $releaseHealth,
                    $supportQueue
                ),
                'solo_today' => [
                    'open_issue_count' => count($myOpenIssues),
                    'due_soon_count' => count($dueSoon),
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function signals(User $user): array
    {
        $visibleProjectIds = Project::query()
            ->visibleTo($user)
            ->pluck('id')
            ->map(static fn ($id): string => (string) $id)
            ->values()
            ->all();

        $teamContext = $this->teamContext($user);
        $isAdmin = $user->hasPermissionTo('is-super-admin') || $user->can('is-admin');
        $canViewSupport = Gate::forUser($user)->allows('viewAny', Ticket::class);

        return [
            'visible_project_ids' => $visibleProjectIds,
            'project_count' => count($visibleProjectIds),
            'team_context' => $teamContext,
            'is_admin' => $isAdmin,
            'can_view_support' => $canViewSupport,
            'has_active_sprint' => $this->hasActiveSprint($user, $visibleProjectIds),
            'has_releases' => $this->hasReleases($visibleProjectIds),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function teamContext(User $user): ?array
    {
        $team = $user->currentTeam;

        if (! $team) {
            return null;
        }

        return [
            'id' => (string) $team->getKey(),
            'name' => $team->name,
            'is_owner' => (string) $team->user_id === (string) $user->getKey(),
            'is_personal' => (bool) $team->personal_team,
        ];
    }

    /**
     * @param  array<int, string>  $visibleProjectIds
     * @return array<int, array<string, mixed>>
     */
    private function myOpenIssues(User $user, array $visibleProjectIds): array
    {
        return Issue::query()
            ->select([
                'id',
                'summary',
                'key',
                'project_id',
                'issue_status_id',
                'updated_at',
                'due_at',
                'sprint_id',
            ])
            ->with([
                'project:id,key,name',
                'status:id,name,color,is_done',
                'sprint:id,project_id,name,end_date',
            ])
            ->where('assignee_id', $user->getKey())
            ->whereIn('project_id', $visibleProjectIds)
            ->whereHas('status', fn (Builder $query): Builder => $query->where('is_done', false))
            ->latest('updated_at')
            ->limit(10)
            ->get()
            ->map(fn (Issue $issue): array => $this->formatIssue($issue))
            ->all();
    }

    /**
     * @param  array<int, string>  $visibleProjectIds
     * @return array<int, array<string, mixed>>
     */
    private function dueSoon(User $user, array $visibleProjectIds): array
    {
        return Issue::query()
            ->select(['id', 'summary', 'key', 'project_id', 'issue_status_id', 'due_at'])
            ->with(['project:id,key,name', 'status:id,name,color,is_done'])
            ->where('assignee_id', $user->getKey())
            ->whereIn('project_id', $visibleProjectIds)
            ->whereNotNull('due_at')
            ->whereBetween('due_at', [now(), now()->addDays(14)])
            ->orderBy('due_at')
            ->limit(10)
            ->get()
            ->map(fn (Issue $issue): array => $this->formatIssue($issue))
            ->all();
    }

    /**
     * @param  array<int, string>  $visibleProjectIds
     * @return array<int, array<string, mixed>>
     */
    private function statusSummary(User $user, array $visibleProjectIds): array
    {
        $counts = Issue::query()
            ->where('assignee_id', $user->getKey())
            ->whereIn('project_id', $visibleProjectIds)
            ->selectRaw('issue_status_id, COUNT(*) as total')
            ->groupBy('issue_status_id')
            ->pluck('total', 'issue_status_id');

        if ($counts->isEmpty()) {
            return [];
        }

        $statuses = IssueStatus::query()
            ->whereIn('id', $counts->keys()->all())
            ->get(['id', 'name', 'color', 'order'])
            ->keyBy('id');

        return $counts
            ->map(function ($total, $statusId) use ($statuses): array {
                /** @var IssueStatus|null $status */
                $status = $statuses->get($statusId);

                return [
                    'label' => $status?->name ?? 'Unknown',
                    'color' => $status?->color ?? '#6b7280',
                    'total' => (int) $total,
                    'order' => (int) ($status?->order ?? 999),
                ];
            })
            ->sortBy('order')
            ->values()
            ->map(fn (array $row): array => [
                'label' => $row['label'],
                'color' => $row['color'],
                'total' => $row['total'],
            ])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function projectPortfolio(User $user): array
    {
        return Project::query()
            ->visibleTo($user)
            ->select(['projects.id', 'projects.name', 'projects.key', 'projects.updated_at', 'projects.organization_id'])
            ->with(['organization:id,name'])
            ->withCount([
                'issues as open_issues_count' => fn (Builder $query): Builder => $query->whereHas(
                    'status',
                    fn (Builder $statusQuery): Builder => $statusQuery->where('is_done', false)
                ),
                'issues as due_soon_issues_count' => fn (Builder $query): Builder => $query
                    ->whereHas('status', fn (Builder $statusQuery): Builder => $statusQuery->where('is_done', false))
                    ->whereNotNull('due_at')
                    ->whereBetween('due_at', [now(), now()->addDays(14)]),
                'sprints as active_sprints_count' => fn (Builder $query): Builder => $query->active(),
            ])
            ->latest('projects.updated_at')
            ->limit(8)
            ->get()
            ->map(function (Project $project): array {
                return [
                    'id' => (string) $project->getKey(),
                    'name' => $project->name,
                    'key' => $project->key,
                    'organization_name' => $project->organization?->name,
                    'url' => route('projects.show', ['project' => $project]),
                    'open_issues_count' => (int) $project->open_issues_count,
                    'due_soon_issues_count' => (int) $project->due_soon_issues_count,
                    'active_sprints_count' => (int) $project->active_sprints_count,
                    'updated_label' => $project->updated_at?->diffForHumans(),
                ];
            })
            ->all();
    }

    /**
     * @param  array<int, string>  $visibleProjectIds
     * @return array<int, array<string, mixed>>
     */
    private function activityGroups(array $visibleProjectIds): array
    {
        if ($visibleProjectIds === []) {
            return [];
        }

        /** @var Collection<int, Activity> $rawActivity */
        $rawActivity = Activity::query()
            ->where(function (Builder $query) use ($visibleProjectIds): void {
                $query
                    ->where(function (Builder $projectActivity) use ($visibleProjectIds): void {
                        $projectActivity
                            ->where('subject_type', Project::class)
                            ->whereIn('subject_id', $visibleProjectIds);
                    })
                    ->orWhereIn('properties->project_id', $visibleProjectIds);
            })
            ->latest()
            ->limit(40)
            ->get([
                'id',
                'description',
                'event',
                'created_at',
                'properties',
                'causer_id',
                'causer_type',
                'subject_type',
                'subject_id',
                'log_name',
            ]);

        if ($rawActivity->isEmpty()) {
            return [];
        }

        $issueIds = $rawActivity
            ->where('subject_type', Issue::class)
            ->pluck('subject_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $projectIds = collect($visibleProjectIds)
            ->merge(
                $rawActivity
                    ->where('subject_type', Project::class)
                    ->pluck('subject_id')
                    ->filter()
            )
            ->merge(
                $rawActivity
                    ->pluck('properties.project_id')
                    ->filter()
            )
            ->unique()
            ->values()
            ->all();

        $causerIds = $rawActivity
            ->where('causer_type', User::class)
            ->pluck('causer_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $statusIds = [];
        $assigneeIds = [];
        $priorityIds = [];
        $typeIds = [];
        $parentIds = [];

        foreach ($rawActivity as $activity) {
            $props = (array) ($activity->properties ?? []);
            $new = (array) ($props['attributes'] ?? $props['new'] ?? []);
            $old = (array) ($props['old'] ?? $props['attributes_before'] ?? []);

            foreach (['issue_status_id', 'assignee_id', 'issue_priority_id', 'issue_type_id', 'parent_id'] as $key) {
                if (isset($new[$key])) {
                    $this->collectChangedId(
                        $key,
                        $new[$key],
                        $statusIds,
                        $assigneeIds,
                        $priorityIds,
                        $typeIds,
                        $parentIds
                    );
                }

                if (isset($old[$key])) {
                    $this->collectChangedId(
                        $key,
                        $old[$key],
                        $statusIds,
                        $assigneeIds,
                        $priorityIds,
                        $typeIds,
                        $parentIds
                    );
                }
            }
        }

        $projectsById = Project::query()
            ->whereIn('id', $projectIds)
            ->get(['id', 'key', 'name'])
            ->keyBy('id');

        $issuesById = Issue::query()
            ->whereIn('id', $issueIds)
            ->with(['project:id,key,name'])
            ->get(['id', 'key', 'summary', 'project_id'])
            ->keyBy('id');

        $usersById = User::query()
            ->whereIn('id', $causerIds)
            ->get(['id', 'name', 'profile_photo_path'])
            ->keyBy('id');

        $statusMap = IssueStatus::query()->whereIn('id', $this->uniqueNonEmpty($statusIds))->get(['id', 'name', 'color'])->keyBy('id');
        $assigneeMap = User::query()->whereIn('id', $this->uniqueNonEmpty($assigneeIds))->get(['id', 'name', 'profile_photo_path'])->keyBy('id');
        $priorityMap = IssuePriority::query()->whereIn('id', $this->uniqueNonEmpty($priorityIds))->get(['id', 'name'])->keyBy('id');
        $typeMap = IssueType::query()->whereIn('id', $this->uniqueNonEmpty($typeIds))->get(['id', 'name'])->keyBy('id');
        $parentMap = Issue::query()->whereIn('id', $this->uniqueNonEmpty($parentIds))->get(['id', 'key', 'summary'])->keyBy('id');

        $labelMap = [
            'summary' => 'Summary',
            'description' => 'Description',
            'issue_status_id' => 'Status',
            'assignee_id' => 'Assignee',
            'issue_priority_id' => 'Priority',
            'issue_type_id' => 'Type',
            'parent_id' => 'Parent',
        ];

        $items = $rawActivity->map(function (Activity $activity) use (
            $usersById,
            $issuesById,
            $projectsById,
            $labelMap,
            $statusMap,
            $assigneeMap,
            $priorityMap,
            $typeMap,
            $parentMap
        ): array {
            $props = (array) ($activity->properties ?? []);
            $new = (array) ($props['attributes'] ?? $props['new'] ?? []);
            $old = (array) ($props['old'] ?? $props['attributes_before'] ?? []);

            $actor = $activity->causer_type === User::class ? $usersById->get($activity->causer_id) : null;
            $targetLabel = 'record';
            $targetUrl = null;

            if ($activity->subject_type === Issue::class) {
                /** @var Issue|null $issue */
                $issue = $issuesById->get($activity->subject_id);

                if ($issue && $issue->project) {
                    $targetLabel = "{$issue->key}: {$issue->summary}";
                    $targetUrl = route('issues.show', ['project' => $issue->project, 'issue' => $issue]);
                }
            } elseif ($activity->subject_type === Project::class) {
                /** @var Project|null $project */
                $project = $projectsById->get($activity->subject_id);

                if ($project) {
                    $targetLabel = "{$project->key} — {$project->name}";
                    $targetUrl = route('projects.show', ['project' => $project]);
                }
            } elseif (isset($props['issue_key'], $props['issue_summary'])) {
                $targetLabel = "{$props['issue_key']}: {$props['issue_summary']}";
            }

            $verb = $activity->event
                ?: (Str::contains((string) $activity->description, '.')
                    ? Str::after((string) $activity->description, '.')
                    : (string) $activity->description);

            $changes = [];
            $keys = array_unique(array_merge(array_keys($new), array_keys($old)));

            foreach ($keys as $key) {
                $label = $labelMap[$key] ?? Str::of($key)->headline()->toString();
                $from = $old[$key] ?? null;
                $to = $new[$key] ?? null;

                if ($key === 'issue_status_id') {
                    $from = $from ? ($statusMap->get($from)?->name ?? $from) : null;
                    $to = $to ? ($statusMap->get($to)?->name ?? $to) : null;
                } elseif ($key === 'assignee_id') {
                    $from = $from ? ($assigneeMap->get($from)?->name ?? $from) : null;
                    $to = $to ? ($assigneeMap->get($to)?->name ?? $to) : null;
                } elseif ($key === 'issue_priority_id') {
                    $from = $from ? ($priorityMap->get($from)?->name ?? $from) : null;
                    $to = $to ? ($priorityMap->get($to)?->name ?? $to) : null;
                } elseif ($key === 'issue_type_id') {
                    $from = $from ? ($typeMap->get($from)?->name ?? $from) : null;
                    $to = $to ? ($typeMap->get($to)?->name ?? $to) : null;
                } elseif ($key === 'parent_id') {
                    $from = $from ? (($parent = $parentMap->get($from)) ? "{$parent->key}: {$parent->summary}" : $from) : null;
                    $to = $to ? (($parent = $parentMap->get($to)) ? "{$parent->key}: {$parent->summary}" : $to) : null;
                }

                if (($from ?? '') === ($to ?? '')) {
                    continue;
                }

                $changes[] = [
                    'label' => $label,
                    'from' => $from,
                    'to' => $to,
                    'key' => $key,
                    'to_color' => $key === 'issue_status_id' && isset($new['issue_status_id'])
                        ? ($statusMap->get($new['issue_status_id'])?->color)
                        : null,
                ];
            }

            return [
                'id' => $activity->id,
                'actor_name' => $actor?->name ?? 'System',
                'actor_avatar' => $actor?->profile_photo_url ?? $actor?->profile_photo_path,
                'verb' => (string) Str::of($verb)->replace(['issue.', 'project.', 'comment.'], '')->headline(),
                'target_label' => $targetLabel,
                'target_url' => $targetUrl,
                'changes' => $changes,
                'created_at' => $activity->created_at,
                'ago' => $activity->created_at?->diffForHumans(),
            ];
        });

        return $items
            ->groupBy(function (array $item): string {
                $date = $item['created_at'];

                if ($date?->isToday()) {
                    return 'Today';
                }

                if ($date?->isYesterday()) {
                    return 'Yesterday';
                }

                return $date?->toFormattedDateString() ?? 'Recent';
            })
            ->map(fn (Collection $group, string $label): array => [
                'label' => $label,
                'items' => $group
                    ->values()
                    ->map(function (array $item): array {
                        unset($item['created_at']);

                        return $item;
                    })
                    ->all(),
            ])
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $visibleProjectIds
     * @return array<string, mixed>
     */
    private function mySprint(User $user, array $visibleProjectIds): array
    {
        $sprints = Sprint::query()
            ->active()
            ->whereIn('project_id', $visibleProjectIds)
            ->select(['id', 'project_id', 'name', 'start_date', 'end_date'])
            ->with(['project:id,key,name'])
            ->withCount([
                'issues as total_issues_count',
                'issues as done_issues_count' => fn (Builder $query): Builder => $query->whereHas(
                    'status',
                    fn (Builder $statusQuery): Builder => $statusQuery->where('is_done', true)
                ),
                'issues as assigned_to_me_count' => fn (Builder $query): Builder => $query
                    ->where('assignee_id', $user->getKey())
                    ->whereHas('status', fn (Builder $statusQuery): Builder => $statusQuery->where('is_done', false)),
            ])
            ->orderBy('end_date')
            ->limit(4)
            ->get();

        $issues = Issue::query()
            ->select([
                'id',
                'summary',
                'key',
                'project_id',
                'issue_status_id',
                'updated_at',
                'due_at',
                'sprint_id',
            ])
            ->with([
                'project:id,key,name',
                'status:id,name,color,is_done',
                'sprint:id,project_id,name,end_date',
            ])
            ->where('assignee_id', $user->getKey())
            ->whereIn('project_id', $visibleProjectIds)
            ->whereNotNull('sprint_id')
            ->whereHas('status', fn (Builder $query): Builder => $query->where('is_done', false))
            ->whereHas('sprint', fn (Builder $query): Builder => $query->active())
            ->orderByRaw('CASE WHEN due_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_at')
            ->latest('updated_at')
            ->limit(10)
            ->get();

        return [
            'summary' => [
                'active_sprint_count' => $sprints->count(),
                'assigned_issue_count' => $issues->count(),
                'due_this_week_count' => $issues
                    ->filter(fn (Issue $issue): bool => $issue->due_at !== null && $issue->due_at->lte(now()->addDays(7)))
                    ->count(),
            ],
            'sprints' => $sprints->map(function (Sprint $sprint): array {
                $totalIssues = max(1, (int) $sprint->total_issues_count);

                return [
                    'id' => (string) $sprint->getKey(),
                    'name' => $sprint->name,
                    'project_name' => $sprint->project?->name,
                    'project_key' => $sprint->project?->key,
                    'project_url' => $sprint->project ? route('projects.scrum', ['project' => $sprint->project]) : null,
                    'window' => $this->dateWindowLabel($sprint->start_date, $sprint->end_date),
                    'total_issues_count' => (int) $sprint->total_issues_count,
                    'done_issues_count' => (int) $sprint->done_issues_count,
                    'assigned_to_me_count' => (int) $sprint->assigned_to_me_count,
                    'completion_percent' => (int) round(((int) $sprint->done_issues_count / $totalIssues) * 100),
                ];
            })->all(),
            'issues' => $issues->map(fn (Issue $issue): array => $this->formatIssue($issue))->all(),
        ];
    }

    /**
     * @param  array<int, string>  $visibleProjectIds
     * @param  array<string, mixed>|null  $teamContext
     * @return array<string, mixed>
     */
    private function teamDelivery(array $visibleProjectIds, ?array $teamContext): array
    {
        if ($visibleProjectIds === []) {
            return [
                'summary' => [
                    'project_count' => 0,
                    'open_issue_count' => 0,
                    'due_soon_count' => 0,
                    'overdue_count' => 0,
                    'active_sprint_count' => 0,
                    'scope_label' => $teamContext['name'] ?? null,
                ],
                'projects' => [],
            ];
        }

        $projectRows = Project::query()
            ->whereIn('projects.id', $visibleProjectIds)
            ->select(['projects.id', 'projects.name', 'projects.key', 'projects.updated_at', 'projects.organization_id'])
            ->with(['organization:id,name'])
            ->withCount([
                'issues as open_issues_count' => fn (Builder $query): Builder => $query->whereHas(
                    'status',
                    fn (Builder $statusQuery): Builder => $statusQuery->where('is_done', false)
                ),
                'issues as due_soon_issues_count' => fn (Builder $query): Builder => $query
                    ->whereHas('status', fn (Builder $statusQuery): Builder => $statusQuery->where('is_done', false))
                    ->whereNotNull('due_at')
                    ->whereBetween('due_at', [now(), now()->addDays(14)]),
                'issues as overdue_issues_count' => fn (Builder $query): Builder => $query
                    ->whereHas('status', fn (Builder $statusQuery): Builder => $statusQuery->where('is_done', false))
                    ->whereNotNull('due_at')
                    ->where('due_at', '<', now()),
                'sprints as active_sprints_count' => fn (Builder $query): Builder => $query->active(),
            ])
            ->latest('projects.updated_at')
            ->limit(8)
            ->get();

        return [
            'summary' => [
                'project_count' => count($visibleProjectIds),
                'open_issue_count' => (int) Issue::query()
                    ->whereIn('project_id', $visibleProjectIds)
                    ->whereHas('status', fn (Builder $query): Builder => $query->where('is_done', false))
                    ->count(),
                'due_soon_count' => (int) Issue::query()
                    ->whereIn('project_id', $visibleProjectIds)
                    ->whereHas('status', fn (Builder $query): Builder => $query->where('is_done', false))
                    ->whereNotNull('due_at')
                    ->whereBetween('due_at', [now(), now()->addDays(14)])
                    ->count(),
                'overdue_count' => (int) Issue::query()
                    ->whereIn('project_id', $visibleProjectIds)
                    ->whereHas('status', fn (Builder $query): Builder => $query->where('is_done', false))
                    ->whereNotNull('due_at')
                    ->where('due_at', '<', now())
                    ->count(),
                'active_sprint_count' => (int) Sprint::query()
                    ->active()
                    ->whereIn('project_id', $visibleProjectIds)
                    ->count(),
                'scope_label' => $teamContext['name'] ?? null,
            ],
            'projects' => $projectRows->map(function (Project $project): array {
                return [
                    'id' => (string) $project->getKey(),
                    'name' => $project->name,
                    'key' => $project->key,
                    'organization_name' => $project->organization?->name,
                    'url' => route('projects.show', ['project' => $project]),
                    'open_issues_count' => (int) $project->open_issues_count,
                    'due_soon_issues_count' => (int) $project->due_soon_issues_count,
                    'overdue_issues_count' => (int) $project->overdue_issues_count,
                    'active_sprints_count' => (int) $project->active_sprints_count,
                    'updated_label' => $project->updated_at?->diffForHumans(),
                ];
            })->all(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function supportQueue(User $user): array
    {
        $openTicketQuery = Ticket::query()->where(function (Builder $query): void {
            $query
                ->whereNull('status_id')
                ->orWhereHas('status', fn (Builder $statusQuery): Builder => $statusQuery->where('is_done', false));
        });

        $tickets = (clone $openTicketQuery)
            ->with([
                'status:id,name,is_done',
                'priority:id,name',
                'type:id,name',
                'assignee:id,name',
                'product:id,name',
            ])
            ->latest()
            ->limit(8)
            ->get();

        return [
            'summary' => [
                'open_count' => (clone $openTicketQuery)->count(),
                'unassigned_count' => (clone $openTicketQuery)->whereNull('assigned_to_user_id')->count(),
                'mine_count' => (clone $openTicketQuery)->where('assigned_to_user_id', $user->getKey())->count(),
                'breached_count' => (clone $openTicketQuery)->where(function (Builder $query): void {
                    $query
                        ->where(function (Builder $window): void {
                            $window
                                ->whereNotNull('first_response_due_at')
                                ->whereNull('first_responded_at')
                                ->where('first_response_due_at', '<', now());
                        })
                        ->orWhere(function (Builder $window): void {
                            $window
                                ->whereNotNull('next_response_due_at')
                                ->whereNull('resolved_at')
                                ->where('next_response_due_at', '<', now());
                        })
                        ->orWhere(function (Builder $window): void {
                            $window
                                ->whereNotNull('resolve_due_at')
                                ->whereNull('resolved_at')
                                ->where('resolve_due_at', '<', now());
                        });
                })->count(),
            ],
            'tickets' => $tickets->map(function (Ticket $ticket): array {
                $windows = collect($ticket->slaWindows());
                $breachedWindow = $windows->firstWhere('breached', true);
                $openWindow = $windows->firstWhere('open', true);

                $slaLabel = 'No active SLA';

                if ($breachedWindow) {
                    $slaLabel = $breachedWindow['label'].' breached';
                } elseif ($openWindow && $openWindow['due_at'] instanceof CarbonInterface) {
                    $slaLabel = $openWindow['label'].' '.$openWindow['due_at']->diffForHumans();
                }

                return [
                    'id' => (string) $ticket->getKey(),
                    'key' => $ticket->key,
                    'subject' => $ticket->subject,
                    'product_name' => $ticket->product?->name,
                    'status_name' => $ticket->status?->name ?? 'Open',
                    'priority_name' => $ticket->priority?->name,
                    'assignee_name' => $ticket->assignee?->name,
                    'opened_label' => $ticket->created_at?->diffForHumans(),
                    'sla_label' => $slaLabel,
                    'breached' => (bool) $breachedWindow,
                    'url' => route('support.staff.show', ['key' => $ticket->key]),
                ];
            })->all(),
        ];
    }

    /**
     * @param  array<int, string>  $visibleProjectIds
     * @return array<string, mixed>
     */
    private function releaseHealth(array $visibleProjectIds): array
    {
        if ($visibleProjectIds === []) {
            return [
                'summary' => [
                    'release_count' => 0,
                    'at_risk_count' => 0,
                    'upcoming_label' => null,
                ],
                'releases' => [],
            ];
        }

        $releases = Milestone::query()
            ->releases()
            ->whereIn('project_id', $visibleProjectIds)
            ->where(function (Builder $query): void {
                $query
                    ->whereNull('released_at')
                    ->orWhere('released_at', '>=', now()->subDays(30));
            })
            ->select(['id', 'project_id', 'name', 'version', 'starts_at', 'due_at', 'released_at'])
            ->with(['project:id,key,name'])
            ->withCount([
                'issues as total_issues_count',
                'issues as done_issues_count' => fn (Builder $query): Builder => $query->whereHas(
                    'status',
                    fn (Builder $statusQuery): Builder => $statusQuery->where('is_done', true)
                ),
                'issues as open_issues_count' => fn (Builder $query): Builder => $query->whereHas(
                    'status',
                    fn (Builder $statusQuery): Builder => $statusQuery->where('is_done', false)
                ),
                'issues as overdue_issues_count' => fn (Builder $query): Builder => $query
                    ->whereHas('status', fn (Builder $statusQuery): Builder => $statusQuery->where('is_done', false))
                    ->whereNotNull('due_at')
                    ->where('due_at', '<', now()),
            ])
            ->orderByRaw('COALESCE(due_at, starts_at, released_at) asc')
            ->limit(8)
            ->get()
            ->map(function (Milestone $milestone): array {
                $totalIssues = (int) $milestone->total_issues_count;
                $doneIssues = (int) $milestone->done_issues_count;
                $endsAt = $milestone->released_at ?? $milestone->due_at;
                $progressPercent = $totalIssues > 0
                    ? (int) round(($doneIssues / $totalIssues) * 100)
                    : 0;

                $risk = $this->releaseRisk(
                    totalIssues: $totalIssues,
                    progressPercent: $progressPercent,
                    overdueIssueCount: (int) $milestone->overdue_issues_count,
                    endsAt: $endsAt,
                );

                return [
                    'id' => (string) $milestone->getKey(),
                    'name' => trim($milestone->name.' '.($milestone->version ?? '')),
                    'project_name' => $milestone->project?->name,
                    'project_key' => $milestone->project?->key,
                    'url' => $milestone->project
                        ? route('projects.milestones.show', ['project' => $milestone->project, 'milestone' => $milestone])
                        : null,
                    'window' => $this->dateWindowLabel($milestone->starts_at, $endsAt),
                    'total_issues_count' => $totalIssues,
                    'done_issues_count' => $doneIssues,
                    'open_issues_count' => (int) $milestone->open_issues_count,
                    'overdue_issues_count' => (int) $milestone->overdue_issues_count,
                    'progress_percent' => $progressPercent,
                    'risk_label' => $risk['label'],
                    'risk_tone' => $risk['tone'],
                ];
            })
            ->all();

        return [
            'summary' => [
                'release_count' => count($releases),
                'at_risk_count' => collect($releases)
                    ->filter(fn (array $release): bool => in_array($release['risk_tone'], ['warning', 'danger'], true))
                    ->count(),
                'upcoming_label' => $releases[0]['name'] ?? null,
            ],
            'releases' => $releases,
        ];
    }

    /**
     * @param  array<string, mixed>  $signals
     * @param  array<string, mixed>  $teamDelivery
     * @param  array<string, mixed>  $releaseHealth
     * @param  array<string, mixed>|null  $supportQueue
     * @return array<string, mixed>
     */
    private function execSummary(
        array $signals,
        array $teamDelivery,
        array $releaseHealth,
        ?array $supportQueue
    ): array {
        $stats = [
            ['label' => 'Projects', 'value' => $teamDelivery['summary']['project_count'] ?? 0],
            ['label' => 'Open issues', 'value' => $teamDelivery['summary']['open_issue_count'] ?? 0],
            ['label' => 'Due soon', 'value' => $teamDelivery['summary']['due_soon_count'] ?? 0],
            ['label' => 'Overdue', 'value' => $teamDelivery['summary']['overdue_count'] ?? 0],
            ['label' => 'At-risk releases', 'value' => $releaseHealth['summary']['at_risk_count'] ?? 0],
        ];

        if ($supportQueue !== null) {
            $stats[] = ['label' => 'Open tickets', 'value' => $supportQueue['summary']['open_count'] ?? 0];
        }

        $highlights = collect();

        $highlights = $highlights->merge(
            collect($releaseHealth['releases'] ?? [])
                ->filter(fn (array $release): bool => in_array($release['risk_tone'], ['warning', 'danger'], true))
                ->take(2)
                ->map(fn (array $release): array => [
                    'label' => 'Release risk',
                    'text' => "{$release['name']} is {$release['risk_label']} with {$release['open_issues_count']} open items.",
                    'url' => $release['url'],
                    'tone' => $release['risk_tone'],
                ])
        );

        $highlights = $highlights->merge(
            collect($teamDelivery['projects'] ?? [])
                ->filter(fn (array $project): bool => (int) $project['overdue_issues_count'] > 0)
                ->take(2)
                ->map(fn (array $project): array => [
                    'label' => 'Delivery risk',
                    'text' => "{$project['name']} has {$project['overdue_issues_count']} overdue issue".((int) $project['overdue_issues_count'] === 1 ? '' : 's').'.',
                    'url' => $project['url'],
                    'tone' => 'danger',
                ])
        );

        if ($supportQueue !== null && (int) ($supportQueue['summary']['breached_count'] ?? 0) > 0) {
            $highlights->push([
                'label' => 'Support',
                'text' => "{$supportQueue['summary']['breached_count']} ticket".((int) $supportQueue['summary']['breached_count'] === 1 ? ' is' : 's are').' outside SLA.',
                'url' => route('support.staff.index'),
                'tone' => 'warning',
            ]);
        }

        if ($highlights->isEmpty()) {
            $highlights->push([
                'label' => 'Summary',
                'text' => $signals['project_count'] > 0
                    ? 'No immediate delivery risks surfaced across your visible work.'
                    : 'No visible projects yet. Add a project or join a team to populate the dashboard.',
                'url' => $signals['project_count'] > 0 ? null : route('projects.create'),
                'tone' => 'secondary',
            ]);
        }

        return [
            'stats' => $stats,
            'highlights' => $highlights->take(5)->values()->all(),
        ];
    }

    /**
     * @return array{label:string,tone:string}
     */
    private function releaseRisk(int $totalIssues, int $progressPercent, int $overdueIssueCount, ?CarbonInterface $endsAt): array
    {
        if ($totalIssues === 0) {
            return ['label' => 'No scope', 'tone' => 'secondary'];
        }

        if ($progressPercent === 100 && $overdueIssueCount === 0) {
            return ['label' => 'Ready', 'tone' => 'success'];
        }

        if (
            $overdueIssueCount > 0
            || ($endsAt && $endsAt->isPast() && $progressPercent < 100)
        ) {
            return ['label' => 'High risk', 'tone' => 'danger'];
        }

        if ($endsAt && now()->diffInDays($endsAt, false) <= 14 && $progressPercent < 75) {
            return ['label' => 'At risk', 'tone' => 'warning'];
        }

        return ['label' => 'On track', 'tone' => 'primary'];
    }

    /**
     * @param  array<int, string>  $visibleProjectIds
     */
    private function hasActiveSprint(User $user, array $visibleProjectIds): bool
    {
        if ($visibleProjectIds === []) {
            return false;
        }

        return Issue::query()
            ->where('assignee_id', $user->getKey())
            ->whereIn('project_id', $visibleProjectIds)
            ->whereHas('sprint', fn (Builder $query): Builder => $query->active())
            ->whereHas('status', fn (Builder $query): Builder => $query->where('is_done', false))
            ->exists();
    }

    /**
     * @param  array<int, string>  $visibleProjectIds
     */
    private function hasReleases(array $visibleProjectIds): bool
    {
        if ($visibleProjectIds === []) {
            return false;
        }

        return Milestone::query()
            ->releases()
            ->whereIn('project_id', $visibleProjectIds)
            ->exists();
    }

    private function collectChangedId(
        string $key,
        mixed $value,
        array &$statusIds,
        array &$assigneeIds,
        array &$priorityIds,
        array &$typeIds,
        array &$parentIds
    ): void {
        match ($key) {
            'issue_status_id' => $statusIds[] = $value,
            'assignee_id' => $assigneeIds[] = $value,
            'issue_priority_id' => $priorityIds[] = $value,
            'issue_type_id' => $typeIds[] = $value,
            'parent_id' => $parentIds[] = $value,
            default => null,
        };
    }

    /**
     * @return array<int, string|int>
     */
    private function uniqueNonEmpty(array $values): array
    {
        return array_values(array_unique(array_filter($values, static fn ($value): bool => filled($value))));
    }

    /**
     * @return array<string, mixed>
     */
    private function formatIssue(Issue $issue): array
    {
        return [
            'id' => (string) $issue->getKey(),
            'key' => $issue->key,
            'summary' => $issue->summary,
            'url' => $issue->project ? route('issues.show', ['project' => $issue->project, 'issue' => $issue]) : null,
            'project_name' => $issue->project?->name,
            'project_key' => $issue->project?->key,
            'status_name' => $issue->status?->name,
            'status_color' => $issue->status?->color,
            'due_label' => $issue->due_at?->diffForHumans(),
            'due_date' => $issue->due_at?->toFormattedDateString(),
            'updated_label' => $issue->updated_at?->diffForHumans(),
            'sprint_name' => $issue->sprint?->name,
        ];
    }

    private function dateWindowLabel(?CarbonInterface $startsAt, ?CarbonInterface $endsAt): string
    {
        if ($startsAt && $endsAt) {
            return $startsAt->format('M j').' - '.$endsAt->format('M j');
        }

        if ($endsAt) {
            return 'Due '.$endsAt->format('M j');
        }

        if ($startsAt) {
            return 'Starts '.$startsAt->format('M j');
        }

        return 'Schedule open';
    }
}
