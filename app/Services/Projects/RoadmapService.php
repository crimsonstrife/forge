<?php

namespace App\Services\Projects;

use App\Models\Issue;
use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class RoadmapService
{
    public function __construct(
        private readonly RoadmapBurnChartBuilder $burnChartBuilder,
        private readonly RoadmapDependencyAnalyzer $dependencyAnalyzer,
        private readonly RoadmapWindowResolver $windowResolver,
    ) {}

    /**
     * @return array{
     *     burnChart: array<string, mixed>,
     *     dependencyEdges: array<int, array<string, mixed>>,
     *     groupBy: string,
     *     groups: array<int, array<string, mixed>>,
     *     milestoneOptions: array<int, array{id:string,label:string}>,
     *     selectedMilestoneId: string|null,
     *     summary: array<string, mixed>,
     *     timelineChart: array<string, mixed>
     * }
     */
    public function build(Project $project, string $groupBy = 'milestone', ?string $selectedMilestoneId = null): array
    {
        $groupBy = in_array($groupBy, ['milestone', 'parent'], true) ? $groupBy : 'milestone';

        $milestones = $this->loadMilestones($project);
        $selectedMilestoneId = $this->resolveMilestoneSelection($milestones, $selectedMilestoneId);

        $issues = $this->loadRoadmapIssues($project);
        $issuesById = $issues->keyBy(static fn (Issue $issue): string => (string) $issue->getKey());
        $rootScopeIds = $this->resolveRootScopeIds($issues, $issuesById);

        [$groups, $groupIdByIssueId] = $groupBy === 'parent'
            ? $this->buildParentGroups($project, $issues, $issuesById, $rootScopeIds)
            : $this->buildMilestoneGroups($project, $milestones, $issues, $rootScopeIds);

        [
            'dependencyEdges' => $dependencyEdges,
            'groups' => $groups,
        ] = $this->dependencyAnalyzer->analyze(
            project: $project,
            groups: $groups,
            groupIdByIssueId: $groupIdByIssueId,
            issues: $issues,
            groupBy: $groupBy,
        );

        $groups = $this->finalizeGroups($groups, $issuesById);
        $summary = $this->buildSummary($milestones, $issues, $rootScopeIds, $groups);
        $timelineChart = $this->buildTimelineChart($groups);
        $milestoneOptions = $this->buildMilestoneOptions($milestones, $issues);

        $burnChart = $this->burnChartBuilder->build(
            milestone: $milestones->firstWhere('id', $selectedMilestoneId),
            issues: $this->loadBurnChartIssues($selectedMilestoneId),
        );

        return [
            'burnChart' => $burnChart,
            'dependencyEdges' => $dependencyEdges,
            'groupBy' => $groupBy,
            'groups' => $groups,
            'milestoneOptions' => $milestoneOptions,
            'selectedMilestoneId' => $selectedMilestoneId,
            'summary' => $summary,
            'timelineChart' => $timelineChart,
        ];
    }

    /**
     * @return Collection<int, Milestone>
     */
    private function loadMilestones(Project $project): Collection
    {
        return $project->milestones()
            ->orderByRaw('CASE WHEN due_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_at')
            ->orderByRaw('CASE WHEN starts_at IS NULL THEN 1 ELSE 0 END')
            ->orderBy('starts_at')
            ->orderBy('name')
            ->get([
                'id',
                'project_id',
                'name',
                'type',
                'state',
                'version',
                'starts_at',
                'due_at',
                'released_at',
            ]);
    }

    /**
     * @return Collection<int, Issue>
     */
    private function loadRoadmapIssues(Project $project): Collection
    {
        return Issue::query()
            ->where('project_id', $project->id)
            ->with([
                'status:id,name,color,is_done',
                'type:id,name,key,tier',
                'milestone:id,project_id,name,type,state,starts_at,due_at,released_at,version',
                'incomingLinks' => fn ($query) => $query
                    ->whereHas('type', fn ($typeQuery) => $typeQuery->where('key', 'blocks'))
                    ->with([
                        'type:id,key,name,inverse_name,is_symmetric',
                        'from:id,key,summary,project_id,issue_status_id,milestone_id',
                        'from.status:id,name,color,is_done',
                        'from.milestone:id,project_id,name,type,state,starts_at,due_at,released_at,version',
                        'from.project:id,key,name',
                    ]),
            ])
            ->get([
                'id',
                'key',
                'summary',
                'project_id',
                'parent_id',
                'milestone_id',
                'issue_status_id',
                'issue_type_id',
                'assignee_id',
                'story_points',
                'children_count',
                'starts_at',
                'due_at',
                'created_at',
                'updated_at',
            ]);
    }

    /**
     * @return Collection<int, Issue>
     */
    private function loadBurnChartIssues(?string $selectedMilestoneId): Collection
    {
        if ($selectedMilestoneId === null) {
            return collect();
        }

        return Issue::query()
            ->where('milestone_id', $selectedMilestoneId)
            ->with('status:id,name,color,is_done')
            ->get([
                'id',
                'key',
                'milestone_id',
                'issue_status_id',
                'created_at',
                'updated_at',
            ]);
    }

    /**
     * @param  Collection<int, Issue>  $issues
     * @param  Collection<string, Issue>  $issuesById
     * @return array<string, string|null>
     */
    private function resolveRootScopeIds(Collection $issues, Collection $issuesById): array
    {
        $rootScopeIds = [];

        foreach ($issues as $issue) {
            $root = $this->resolveRootScopeIssue($issue, $issuesById);
            $rootScopeIds[(string) $issue->getKey()] = $root?->getKey();
        }

        return $rootScopeIds;
    }

    /**
     * @param  Collection<string, Issue>  $issuesById
     */
    private function resolveRootScopeIssue(Issue $issue, Collection $issuesById): ?Issue
    {
        $current = $issue;
        $seen = [];
        $root = null;

        while ($current->parent_id && $issuesById->has((string) $current->parent_id)) {
            $seenKey = (string) $current->getKey();
            if (isset($seen[$seenKey])) {
                break;
            }

            $seen[$seenKey] = true;
            $current = $issuesById->get((string) $current->parent_id);
            $root = $current;
        }

        if ($root instanceof Issue) {
            return $root;
        }

        $tier = $issue->type?->tier?->value;
        if ($tier === 'epic' || (int) ($issue->children_count ?? 0) > 0) {
            return $issue;
        }

        return null;
    }

    /**
     * @param  Collection<int, Milestone>  $milestones
     * @param  Collection<int, Issue>  $issues
     * @param  array<string, string|null>  $rootScopeIds
     * @return array{0: array<string, array<string, mixed>>, 1: array<string, string>}
     */
    private function buildMilestoneGroups(
        Project $project,
        Collection $milestones,
        Collection $issues,
        array $rootScopeIds
    ): array {
        $groups = [];
        $groupIdByIssueId = [];

        foreach ($milestones as $milestone) {
            $groupId = 'milestone:'.$milestone->id;

            $groups[$groupId] = [
                'id' => $groupId,
                'kind' => 'milestone',
                'type' => $this->milestoneType($milestone),
                'name' => $milestone->name,
                'subtitle' => $milestone->version ?: null,
                'state_label' => Str::headline((string) ($milestone->state?->value ?? $milestone->state ?? 'planned')),
                'url' => route('projects.milestones.show', [$project, $milestone]),
                'starts_at' => $this->windowResolver->normalizeDate($milestone->starts_at),
                'ends_at' => $this->windowResolver->normalizeDate($milestone->released_at ?? $milestone->due_at),
                '_issue_ids' => [],
                '_scope_ids' => [],
                '_milestone_ids' => [$milestone->id => true],
                '_blocked_issue_ids' => [],
                '_self_blocked_issue_ids' => [],
                '_blocked_by' => [],
                'blocked_issue_samples' => [],
            ];
        }

        foreach ($issues as $issue) {
            $groupId = $issue->milestone_id
                ? 'milestone:'.$issue->milestone_id
                : 'milestone:unscheduled';

            if (! isset($groups[$groupId])) {
                $groups[$groupId] = [
                    'id' => $groupId,
                    'kind' => 'milestone',
                    'type' => 'unscheduled',
                    'name' => 'Unscheduled',
                    'subtitle' => 'Open work without a release',
                    'state_label' => null,
                    'url' => route('projects.milestones.index', [$project]),
                    'starts_at' => null,
                    'ends_at' => null,
                    '_issue_ids' => [],
                    '_scope_ids' => [],
                    '_milestone_ids' => [],
                    '_blocked_issue_ids' => [],
                    '_self_blocked_issue_ids' => [],
                    '_blocked_by' => [],
                    'blocked_issue_samples' => [],
                ];
            }

            $issueId = (string) $issue->getKey();
            $groups[$groupId]['_issue_ids'][$issueId] = true;
            $groupIdByIssueId[$issueId] = $groupId;

            if ($rootScopeIds[$issueId] ?? null) {
                $groups[$groupId]['_scope_ids'][$rootScopeIds[$issueId]] = true;
            }
        }

        return [$groups, $groupIdByIssueId];
    }

    /**
     * @param  Collection<int, Issue>  $issues
     * @param  Collection<string, Issue>  $issuesById
     * @param  array<string, string|null>  $rootScopeIds
     * @return array{0: array<string, array<string, mixed>>, 1: array<string, string>}
     */
    private function buildParentGroups(
        Project $project,
        Collection $issues,
        Collection $issuesById,
        array $rootScopeIds
    ): array {
        $groups = [];
        $groupIdByIssueId = [];

        foreach ($issues as $issue) {
            $issueId = (string) $issue->getKey();
            $rootId = $rootScopeIds[$issueId] ?? null;

            if ($rootId !== null && $issuesById->has($rootId)) {
                $groupId = 'parent:'.$rootId;
                $rootIssue = $issuesById->get($rootId);

                if (! isset($groups[$groupId])) {
                    $groups[$groupId] = [
                        'id' => $groupId,
                        'kind' => 'parent',
                        'type' => 'parent',
                        'name' => $rootIssue->summary,
                        'subtitle' => $rootIssue->key,
                        'state_label' => $rootIssue->status?->name,
                        'url' => route('issues.show', ['project' => $project, 'issue' => $rootIssue]),
                        'starts_at' => $this->windowResolver->normalizeDate($rootIssue->starts_at),
                        'ends_at' => $this->windowResolver->normalizeDate($rootIssue->due_at),
                        '_issue_ids' => [],
                        '_scope_ids' => [$rootId => true],
                        '_milestone_ids' => [],
                        '_blocked_issue_ids' => [],
                        '_self_blocked_issue_ids' => [],
                        '_blocked_by' => [],
                        'blocked_issue_samples' => [],
                    ];
                }
            } else {
                $groupId = 'parent:unscoped';

                if (! isset($groups[$groupId])) {
                    $groups[$groupId] = [
                        'id' => $groupId,
                        'kind' => 'parent',
                        'type' => 'unscoped',
                        'name' => 'Standalone work',
                        'subtitle' => 'Issues not grouped under an epic parent',
                        'state_label' => null,
                        'url' => route('projects.backlog', ['project' => $project]),
                        'starts_at' => null,
                        'ends_at' => null,
                        '_issue_ids' => [],
                        '_scope_ids' => [],
                        '_milestone_ids' => [],
                        '_blocked_issue_ids' => [],
                        '_self_blocked_issue_ids' => [],
                        '_blocked_by' => [],
                        'blocked_issue_samples' => [],
                    ];
                }
            }

            $groups[$groupId]['_issue_ids'][$issueId] = true;
            $groupIdByIssueId[$issueId] = $groupId;

            if ($issue->milestone_id) {
                $groups[$groupId]['_milestone_ids'][(string) $issue->milestone_id] = true;
            }
        }

        return [$groups, $groupIdByIssueId];
    }

    /**
     * @param  array<string, array<string, mixed>>  $groups
     * @param  Collection<string, Issue>  $issuesById
     * @return array<int, array<string, mixed>>
     */
    private function finalizeGroups(array $groups, Collection $issuesById): array
    {
        $now = now();

        return collect($groups)
            ->map(function (array $group) use ($issuesById, $now): array {
                $issueIds = array_keys($group['_issue_ids']);
                $groupIssues = collect($issueIds)
                    ->map(static fn (string $issueId) => $issuesById->get($issueId))
                    ->filter();

                $doneIssues = $groupIssues->filter(static fn (Issue $issue) => (bool) ($issue->status?->is_done ?? false));
                $openIssues = $groupIssues->reject(static fn (Issue $issue) => (bool) ($issue->status?->is_done ?? false));

                $startsAt = $this->windowResolver->resolveWindowStart($groupIssues, $group['starts_at']);
                $endsAt = $this->windowResolver->resolveWindowEnd($groupIssues, $group['ends_at'], $startsAt);

                $totalIssues = $groupIssues->count();
                $doneCount = $doneIssues->count();
                $openCount = $openIssues->count();
                $progressPercent = $totalIssues > 0 ? (int) round(($doneCount / $totalIssues) * 100) : 0;
                $storyPointsTotal = (int) $groupIssues->sum('story_points');
                $storyPointsDone = (int) $doneIssues->sum('story_points');
                $blockedIssueCount = count($group['_blocked_issue_ids']);
                $selfBlockedCount = count($group['_self_blocked_issue_ids']);
                $overdueIssueCount = $openIssues
                    ->filter(static fn (Issue $issue) => $issue->due_at !== null && $issue->due_at->lt($now))
                    ->count();
                $unassignedIssueCount = $openIssues
                    ->filter(static fn (Issue $issue) => blank($issue->assignee_id))
                    ->count();
                $unscheduledIssueCount = $openIssues
                    ->filter(static fn (Issue $issue) => $issue->starts_at === null || $issue->due_at === null)
                    ->count();

                $readinessPercent = $this->readinessPercent(
                    progressPercent: $progressPercent,
                    blockedIssueCount: $blockedIssueCount,
                    overdueIssueCount: $overdueIssueCount,
                    unassignedIssueCount: $unassignedIssueCount,
                    unscheduledIssueCount: $unscheduledIssueCount,
                    endsAt: $endsAt,
                );

                ['label' => $riskLabel, 'tone' => $riskTone] = $this->riskState(
                    totalIssues: $totalIssues,
                    progressPercent: $progressPercent,
                    blockedIssueCount: $blockedIssueCount,
                    overdueIssueCount: $overdueIssueCount,
                    unassignedIssueCount: $unassignedIssueCount,
                    unscheduledIssueCount: $unscheduledIssueCount,
                    endsAt: $endsAt,
                );

                $blockedBy = collect($group['_blocked_by'])
                    ->map(static function (array $entry): array {
                        return [
                            'count' => count($entry['issue_ids']),
                            'external' => $entry['external'],
                            'label' => $entry['label'],
                            'url' => $entry['url'],
                        ];
                    })
                    ->sortByDesc('count')
                    ->take(4)
                    ->values()
                    ->all();

                return [
                    'id' => $group['id'],
                    'kind' => $group['kind'],
                    'type' => $group['type'],
                    'name' => $group['name'],
                    'subtitle' => $group['subtitle'],
                    'state_label' => $group['state_label'],
                    'url' => $group['url'],
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'date_window_label' => $this->windowResolver->formatWindow($startsAt, $endsAt),
                    'total_issues' => $totalIssues,
                    'done_issues' => $doneCount,
                    'open_issues' => $openCount,
                    'story_points_total' => $storyPointsTotal,
                    'story_points_done' => $storyPointsDone,
                    'progress_percent' => $progressPercent,
                    'readiness_percent' => $readinessPercent,
                    'blocked_issue_count' => $blockedIssueCount,
                    'self_blocked_issue_count' => $selfBlockedCount,
                    'overdue_issue_count' => $overdueIssueCount,
                    'unassigned_issue_count' => $unassignedIssueCount,
                    'unscheduled_issue_count' => $unscheduledIssueCount,
                    'scope_count' => count($group['_scope_ids']),
                    'milestone_count' => count($group['_milestone_ids']),
                    'risk_label' => $riskLabel,
                    'risk_tone' => $riskTone,
                    'risk_badge_class' => $this->badgeClassForTone($riskTone),
                    'chart_color' => $this->chartColorForTone($riskTone),
                    'type_badge_class' => $this->typeBadgeClass($group['type']),
                    'blocked_by' => $blockedBy,
                    'blocked_issue_samples' => $group['blocked_issue_samples'],
                ];
            })
            ->sortBy(function (array $group): string {
                $synthetic = in_array($group['type'], ['unscheduled', 'unscoped'], true) ? 1 : 0;
                $startsAt = $group['starts_at']?->getTimestamp() ?? 9999999999;
                $endsAt = $group['ends_at']?->getTimestamp() ?? 9999999999;

                return sprintf('%d-%010d-%010d-%s', $synthetic, $startsAt, $endsAt, Str::lower($group['name']));
            })
            ->values()
            ->all();
    }

    private function readinessPercent(
        int $progressPercent,
        int $blockedIssueCount,
        int $overdueIssueCount,
        int $unassignedIssueCount,
        int $unscheduledIssueCount,
        ?Carbon $endsAt
    ): int {
        $score = $progressPercent;
        $score -= min(35, $blockedIssueCount * 12);
        $score -= min(25, $overdueIssueCount * 10);
        $score -= min(15, $unassignedIssueCount * 5);
        $score -= min(15, $unscheduledIssueCount * 5);

        if ($endsAt && $endsAt->lt(now()) && $progressPercent < 100) {
            $score -= 10;
        }

        if ($progressPercent === 100 && $blockedIssueCount === 0 && $overdueIssueCount === 0) {
            $score = 100;
        }

        return max(0, min(100, $score));
    }

    /**
     * @return array{label:string,tone:string}
     */
    private function riskState(
        int $totalIssues,
        int $progressPercent,
        int $blockedIssueCount,
        int $overdueIssueCount,
        int $unassignedIssueCount,
        int $unscheduledIssueCount,
        ?Carbon $endsAt
    ): array {
        if ($totalIssues === 0) {
            return ['label' => 'No scope', 'tone' => 'secondary'];
        }

        if ($progressPercent === 100 && $blockedIssueCount === 0 && $overdueIssueCount === 0) {
            return ['label' => 'Ready', 'tone' => 'success'];
        }

        if (
            $overdueIssueCount > 0
            || ($endsAt && $endsAt->lt(now()) && $progressPercent < 100)
            || $blockedIssueCount >= 2
        ) {
            return ['label' => 'High risk', 'tone' => 'danger'];
        }

        if ($blockedIssueCount > 0 || $unassignedIssueCount > 0 || $unscheduledIssueCount > 0) {
            return ['label' => 'At risk', 'tone' => 'warning'];
        }

        return ['label' => 'On track', 'tone' => 'primary'];
    }

    /**
     * @param  Collection<int, Milestone>  $milestones
     * @param  Collection<int, Issue>  $issues
     * @param  array<string, string|null>  $rootScopeIds
     * @param  array<int, array<string, mixed>>  $groups
     * @return array<string, mixed>
     */
    private function buildSummary(
        Collection $milestones,
        Collection $issues,
        array $rootScopeIds,
        array $groups
    ): array {
        $releaseCount = $milestones
            ->filter(fn (Milestone $milestone) => $this->milestoneType($milestone) === 'release')
            ->count();

        $upcomingRelease = $milestones
            ->filter(fn (Milestone $milestone) => $this->milestoneType($milestone) === 'release')
            ->sortBy(static function (Milestone $milestone): int {
                return ($milestone->due_at ?? $milestone->starts_at ?? $milestone->released_at ?? now())->getTimestamp();
            })
            ->first();

        $blockedIssueCount = $issues
            ->filter(static fn (Issue $issue) => ! ($issue->status?->is_done ?? false))
            ->filter(static function (Issue $issue): bool {
                return $issue->incomingLinks->contains(
                    static fn ($link) => $link->from && ! ($link->from->status?->is_done ?? false)
                );
            })
            ->count();

        $atRiskReleaseCount = collect($groups)
            ->filter(static fn (array $group): bool => $group['kind'] === 'milestone' && $group['type'] === 'release')
            ->filter(static fn (array $group): bool => in_array($group['risk_tone'], ['warning', 'danger'], true))
            ->count();

        $standaloneIssueCount = collect($rootScopeIds)
            ->filter(static fn (?string $rootId): bool => $rootId === null)
            ->count();

        return [
            'release_count' => $releaseCount,
            'milestone_count' => $milestones->count(),
            'at_risk_release_count' => $atRiskReleaseCount,
            'blocked_issue_count' => $blockedIssueCount,
            'unscheduled_issue_count' => $issues->whereNull('milestone_id')->count(),
            'standalone_issue_count' => $standaloneIssueCount,
            'upcoming_release' => $upcomingRelease ? [
                'label' => trim($upcomingRelease->name.' '.($upcomingRelease->version ?? '')),
                'window' => $this->windowResolver->formatWindow(
                    $this->windowResolver->normalizeDate($upcomingRelease->starts_at),
                    $this->windowResolver->normalizeDate($upcomingRelease->released_at ?? $upcomingRelease->due_at),
                ),
            ] : null,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $groups
     * @return array<string, mixed>
     */
    private function buildTimelineChart(array $groups): array
    {
        $data = collect($groups)
            ->filter(static fn (array $group): bool => $group['starts_at'] instanceof Carbon && $group['ends_at'] instanceof Carbon)
            ->map(static function (array $group): array {
                return [
                    'blocked' => $group['blocked_issue_count'],
                    'done' => $group['done_issues'],
                    'fillColor' => $group['chart_color'],
                    'open' => $group['open_issues'],
                    'progress' => $group['progress_percent'],
                    'readiness' => $group['readiness_percent'],
                    'risk' => $group['risk_label'],
                    'url' => $group['url'],
                    'window' => $group['date_window_label'],
                    'x' => $group['name'],
                    'y' => [$group['starts_at']->valueOf(), $group['ends_at']->valueOf()],
                ];
            })
            ->values()
            ->all();

        return [
            'height' => max(320, count($data) * 54),
            'series' => $data === [] ? [] : [[
                'data' => $data,
                'name' => 'Roadmap',
            ]],
        ];
    }

    /**
     * @param  Collection<int, Milestone>  $milestones
     * @param  Collection<int, Issue>  $issues
     * @return array<int, array{id:string,label:string}>
     */
    private function buildMilestoneOptions(Collection $milestones, Collection $issues): array
    {
        $counts = $issues
            ->filter(static fn (Issue $issue) => filled($issue->milestone_id))
            ->groupBy('milestone_id')
            ->map(static fn (Collection $group): int => $group->count());

        return $milestones->map(function (Milestone $milestone) use ($counts): array {
            $typeLabel = $this->milestoneType($milestone) === 'release' ? 'Release' : 'Milestone';
            $version = $milestone->version ? ' · '.$milestone->version : '';
            $count = (int) ($counts->get((string) $milestone->id) ?? 0);

            return [
                'id' => (string) $milestone->id,
                'label' => sprintf('%s · %s%s (%d)', $typeLabel, $milestone->name, $version, $count),
            ];
        })->values()->all();
    }

    /**
     * @param  Collection<int, Milestone>  $milestones
     */
    private function resolveMilestoneSelection(Collection $milestones, ?string $selectedMilestoneId): ?string
    {
        if ($selectedMilestoneId && $milestones->contains(static fn (Milestone $milestone): bool => (string) $milestone->id === $selectedMilestoneId)) {
            return $selectedMilestoneId;
        }

        $releaseSelection = $milestones
            ->filter(fn (Milestone $milestone) => $this->milestoneType($milestone) === 'release')
            ->sortBy(static function (Milestone $milestone): int {
                return ($milestone->due_at ?? $milestone->starts_at ?? $milestone->released_at ?? now())->getTimestamp();
            })
            ->first();

        return $releaseSelection?->id ?? $milestones->first()?->id;
    }

    private function milestoneType(Milestone $milestone): string
    {
        return (string) ($milestone->type?->value ?? $milestone->type ?? 'milestone');
    }

    private function badgeClassForTone(string $tone): string
    {
        return match ($tone) {
            'danger' => 'text-bg-danger',
            'warning' => 'text-bg-warning',
            'success' => 'text-bg-success',
            'primary' => 'text-bg-primary',
            default => 'text-bg-secondary',
        };
    }

    private function typeBadgeClass(string $type): string
    {
        return match ($type) {
            'release' => 'text-bg-primary',
            'milestone' => 'text-bg-light border text-body',
            'parent' => 'text-bg-dark',
            default => 'text-bg-secondary',
        };
    }

    private function chartColorForTone(string $tone): string
    {
        return match ($tone) {
            'danger' => '#dc3545',
            'warning' => '#f59f00',
            'success' => '#198754',
            'primary' => '#0d6efd',
            default => '#6c757d',
        };
    }
}
