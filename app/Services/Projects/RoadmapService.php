<?php

namespace App\Services\Projects;

use App\Models\Issue;
use App\Models\IssueStatus;
use App\Models\IssueStatusEvent;
use App\Models\Milestone;
use App\Models\Project;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class RoadmapService
{
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

        $milestones = $project->milestones()
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

        $issues = Issue::query()
            ->where('project_id', $project->id)
            ->with([
                'status:id,name,color,is_done',
                'type:id,name,key,tier',
                'assignee:id,name',
                'milestone:id,project_id,name,type,state,starts_at,due_at,released_at,version',
                'project:id,key,name',
                'incomingLinks' => fn ($query) => $query
                    ->whereHas('type', fn ($typeQuery) => $typeQuery->where('key', 'blocks'))
                    ->with([
                        'type:id,key,name,inverse_name,is_symmetric',
                        'from:id,key,summary,project_id,issue_status_id,milestone_id,parent_id,issue_type_id,children_count,progress_percent,starts_at,due_at,assignee_id,created_at,updated_at',
                        'from.status:id,name,color,is_done',
                        'from.type:id,name,key,tier',
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
                'progress_percent',
                'starts_at',
                'due_at',
                'created_at',
                'updated_at',
            ]);

        $issuesById = $issues->keyBy(static fn (Issue $issue): string => (string) $issue->getKey());
        $rootScopeIds = $this->resolveRootScopeIds($issues, $issuesById);

        [$groups, $groupIdByIssueId] = $groupBy === 'parent'
            ? $this->buildParentGroups($project, $issues, $issuesById, $rootScopeIds)
            : $this->buildMilestoneGroups($project, $milestones, $issues, $rootScopeIds);

        [$groups, $dependencyEdges] = $this->applyDependencyRisk(
            $project,
            $groups,
            $groupIdByIssueId,
            $issues,
            $issuesById,
            $groupBy
        );

        $groups = $this->finalizeGroups($groups, $issuesById);
        $summary = $this->buildSummary($milestones, $issues, $rootScopeIds, $groups);
        $timelineChart = $this->buildTimelineChart($groups);
        $milestoneOptions = $this->buildMilestoneOptions($milestones, $issues);
        $selectedMilestoneId = $this->resolveMilestoneSelection($milestones, $milestoneOptions, $selectedMilestoneId);
        $burnChart = $this->buildBurnChart(
            $milestones->firstWhere('id', $selectedMilestoneId),
            $issues->where('milestone_id', $selectedMilestoneId)->values()
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
                'starts_at' => $this->normalizeDate($milestone->starts_at),
                'ends_at' => $this->normalizeDate($milestone->released_at ?? $milestone->due_at),
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
                        'starts_at' => $this->normalizeDate($rootIssue->starts_at),
                        'ends_at' => $this->normalizeDate($rootIssue->due_at),
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
     * @param  array<string, string>  $groupIdByIssueId
     * @param  Collection<int, Issue>  $issues
     * @param  Collection<string, Issue>  $issuesById
     * @return array{0: array<string, array<string, mixed>>, 1: array<int, array<string, mixed>>}
     */
    private function applyDependencyRisk(
        Project $project,
        array $groups,
        array $groupIdByIssueId,
        Collection $issues,
        Collection $issuesById,
        string $groupBy
    ): array {
        $dependencyEdges = [];

        foreach ($issues as $issue) {
            if ($issue->status?->is_done) {
                continue;
            }

            $issueId = (string) $issue->getKey();
            $targetGroupId = $groupIdByIssueId[$issueId] ?? null;
            if ($targetGroupId === null || ! isset($groups[$targetGroupId])) {
                continue;
            }

            $activeBlockers = $issue->incomingLinks
                ->filter(static fn ($link) => $link->from && ! ($link->from->status?->is_done ?? false))
                ->values();

            if ($activeBlockers->isEmpty()) {
                continue;
            }

            $groups[$targetGroupId]['_blocked_issue_ids'][$issueId] = true;

            if (count($groups[$targetGroupId]['blocked_issue_samples']) < 3) {
                $groups[$targetGroupId]['blocked_issue_samples'][] = [
                    'key' => $issue->key,
                    'summary' => $issue->summary,
                    'url' => route('issues.show', ['project' => $project, 'issue' => $issue]),
                ];
            }

            foreach ($activeBlockers as $link) {
                $blockerMeta = $this->resolveBlockerGroupMeta(
                    $project,
                    $groups,
                    $groupIdByIssueId,
                    $groupBy,
                    $link->from
                );

                $blockerGroupId = $blockerMeta['id'];

                if (! isset($groups[$targetGroupId]['_blocked_by'][$blockerGroupId])) {
                    $groups[$targetGroupId]['_blocked_by'][$blockerGroupId] = [
                        'external' => $blockerMeta['external'],
                        'issue_ids' => [],
                        'label' => $blockerMeta['label'],
                        'url' => $blockerMeta['url'],
                    ];
                }

                $groups[$targetGroupId]['_blocked_by'][$blockerGroupId]['issue_ids'][$issueId] = true;

                if ($blockerGroupId === $targetGroupId) {
                    $groups[$targetGroupId]['_self_blocked_issue_ids'][$issueId] = true;

                    continue;
                }

                $edgeKey = $blockerGroupId.'>'.$targetGroupId;

                if (! isset($dependencyEdges[$edgeKey])) {
                    $dependencyEdges[$edgeKey] = [
                        'from' => $blockerMeta['label'],
                        'from_url' => $blockerMeta['url'],
                        'issue_ids' => [],
                        'to' => $groups[$targetGroupId]['name'],
                        'to_url' => $groups[$targetGroupId]['url'],
                    ];
                }

                $dependencyEdges[$edgeKey]['issue_ids'][$issueId] = true;
            }
        }

        $dependencyEdges = collect($dependencyEdges)
            ->map(function (array $edge): array {
                return [
                    'count' => count($edge['issue_ids']),
                    'from' => $edge['from'],
                    'from_url' => $edge['from_url'],
                    'to' => $edge['to'],
                    'to_url' => $edge['to_url'],
                ];
            })
            ->sortByDesc('count')
            ->take(10)
            ->values()
            ->all();

        return [$groups, $dependencyEdges];
    }

    /**
     * @param  array<string, array<string, mixed>>  $groups
     * @param  array<string, string>  $groupIdByIssueId
     * @return array{id:string,label:string,url:?string,external:bool}
     */
    private function resolveBlockerGroupMeta(
        Project $project,
        array $groups,
        array $groupIdByIssueId,
        string $groupBy,
        Issue $blocker
    ): array {
        if ((string) $blocker->project_id === (string) $project->id) {
            $groupId = $groupIdByIssueId[(string) $blocker->getKey()]
                ?? ($groupBy === 'parent' ? 'parent:unscoped' : 'milestone:unscheduled');

            return [
                'id' => $groupId,
                'label' => $groups[$groupId]['name'] ?? $blocker->key,
                'url' => $groups[$groupId]['url'] ?? route('issues.show', ['project' => $project, 'issue' => $blocker]),
                'external' => false,
            ];
        }

        $projectKey = $blocker->project?->key ?? 'External';

        if ($groupBy === 'milestone' && $blocker->milestone) {
            $label = $projectKey.' · '.$blocker->milestone->name;

            return [
                'id' => 'external:milestone:'.$blocker->milestone->id,
                'label' => $label,
                'url' => route('projects.milestones.show', [$blocker->project, $blocker->milestone]),
                'external' => true,
            ];
        }

        return [
            'id' => 'external:issue:'.$blocker->getKey(),
            'label' => $projectKey.' · '.$blocker->key,
            'url' => $blocker->project
                ? route('issues.show', ['project' => $blocker->project, 'issue' => $blocker])
                : null,
            'external' => true,
        ];
    }

    /**
     * @param  array<string, array<string, mixed>>  $groups
     * @param  Collection<string, Issue>  $issuesById
     * @return array<int, array<string, mixed>>
     */
    private function finalizeGroups(array $groups, Collection $issuesById): array
    {
        $now = now();

        $groups = collect($groups)->map(function (array $group) use ($issuesById, $now): array {
            $issueIds = array_keys($group['_issue_ids']);
            $groupIssues = collect($issueIds)
                ->map(static fn (string $issueId) => $issuesById->get($issueId))
                ->filter();

            $doneIssues = $groupIssues->filter(static fn (Issue $issue) => (bool) ($issue->status?->is_done ?? false));
            $openIssues = $groupIssues->reject(static fn (Issue $issue) => (bool) ($issue->status?->is_done ?? false));

            $startsAt = $this->resolveWindowStart($groupIssues, $group['starts_at']);
            $endsAt = $this->resolveWindowEnd($groupIssues, $group['ends_at'], $startsAt);

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
                $progressPercent,
                $blockedIssueCount,
                $overdueIssueCount,
                $unassignedIssueCount,
                $unscheduledIssueCount,
                $endsAt
            );

            ['label' => $riskLabel, 'tone' => $riskTone] = $this->riskState(
                $totalIssues,
                $progressPercent,
                $blockedIssueCount,
                $overdueIssueCount,
                $unassignedIssueCount,
                $unscheduledIssueCount,
                $endsAt
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
                'date_window_label' => $this->formatWindow($startsAt, $endsAt),
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

        return $groups;
    }

    /**
     * @param  Collection<int, Issue>  $groupIssues
     */
    private function resolveWindowStart(Collection $groupIssues, ?Carbon $startsAt): ?Carbon
    {
        if ($startsAt !== null) {
            return $startsAt->copy();
        }

        $candidate = $groupIssues
            ->flatMap(static fn (Issue $issue): array => [
                $issue->starts_at,
                $issue->created_at,
                $issue->milestone?->starts_at,
            ])
            ->filter()
            ->map(fn ($value) => $this->normalizeDate($value))
            ->sortBy(static fn (Carbon $date) => $date->getTimestamp())
            ->first();

        return $candidate?->copy();
    }

    /**
     * @param  Collection<int, Issue>  $groupIssues
     */
    private function resolveWindowEnd(Collection $groupIssues, ?Carbon $endsAt, ?Carbon $startsAt): ?Carbon
    {
        $candidate = $endsAt?->copy();

        if ($candidate === null) {
            $candidate = $groupIssues
                ->flatMap(static fn (Issue $issue): array => [
                    $issue->due_at,
                    $issue->updated_at,
                    $issue->milestone?->released_at,
                    $issue->milestone?->due_at,
                ])
                ->filter()
                ->map(fn ($value) => $this->normalizeDate($value))
                ->sortByDesc(static fn (Carbon $date) => $date->getTimestamp())
                ->first();
        }

        if ($candidate === null && $startsAt !== null) {
            $candidate = $startsAt->copy()->addDays(7);
        }

        if ($candidate !== null && $startsAt !== null && $candidate->lt($startsAt)) {
            $candidate = $startsAt->copy()->addDay();
        }

        return $candidate?->copy();
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
        $releaseCount = $milestones->filter(fn (Milestone $milestone) => $this->milestoneType($milestone) === 'release')->count();
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
                'window' => $this->formatWindow(
                    $this->normalizeDate($upcomingRelease->starts_at),
                    $this->normalizeDate($upcomingRelease->released_at ?? $upcomingRelease->due_at)
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
     * @param  array<int, array{id:string,label:string}>  $milestoneOptions
     */
    private function resolveMilestoneSelection(
        Collection $milestones,
        array $milestoneOptions,
        ?string $selectedMilestoneId
    ): ?string {
        if ($selectedMilestoneId && collect($milestoneOptions)->contains(static fn (array $option): bool => $option['id'] === $selectedMilestoneId)) {
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

    /**
     * @param  Collection<int, Issue>  $issues
     * @return array<string, mixed>
     */
    private function buildBurnChart(?Milestone $milestone, Collection $issues): array
    {
        if (! $milestone instanceof Milestone) {
            return [
                'empty' => true,
                'labels' => [],
                'series' => [],
                'stats' => [
                    'done' => 0,
                    'open' => 0,
                    'total' => 0,
                ],
                'subtitle' => null,
                'title' => 'Burnup / burndown',
                'window' => null,
            ];
        }

        $doneStatusIds = IssueStatus::query()
            ->where('is_done', true)
            ->pluck('id')
            ->map(static fn ($statusId): int => (int) $statusId)
            ->all();

        $eventsByIssueId = $issues->isEmpty()
            ? collect()
            : IssueStatusEvent::query()
                ->whereIn('issue_id', $issues->modelKeys())
                ->orderBy('changed_at')
                ->get(['issue_id', 'to_status_id', 'changed_at'])
                ->groupBy('issue_id');

        $doneAtByIssueId = [];
        $latestDoneAt = null;

        foreach ($issues as $issue) {
            $doneEvent = $eventsByIssueId
                ->get((string) $issue->getKey(), collect())
                ->first(static fn ($event): bool => in_array((int) $event->to_status_id, $doneStatusIds, true));

            $doneAt = $doneEvent?->changed_at
                ? $this->normalizeDate($doneEvent->changed_at)
                : (($issue->status?->is_done ?? false) ? $this->normalizeDate($issue->updated_at) : null);

            $doneAtByIssueId[(string) $issue->getKey()] = $doneAt;

            if ($doneAt && ($latestDoneAt === null || $doneAt->gt($latestDoneAt))) {
                $latestDoneAt = $doneAt;
            }
        }

        $start = $this->normalizeDate($milestone->starts_at)
            ?? $issues
                ->map(fn (Issue $issue) => $this->normalizeDate($issue->created_at))
                ->filter()
                ->sortBy(static fn (Carbon $date) => $date->getTimestamp())
                ->first()
            ?? now()->copy()->startOfDay();

        $endCandidates = collect([
            $this->normalizeDate($milestone->released_at),
            $this->normalizeDate($milestone->due_at),
            $latestDoneAt,
            $issues
                ->map(fn (Issue $issue) => $this->normalizeDate($issue->created_at))
                ->filter()
                ->sortByDesc(static fn (Carbon $date) => $date->getTimestamp())
                ->first(),
            now()->copy()->startOfDay(),
        ])->filter();

        $end = $endCandidates
            ->sortByDesc(static fn (Carbon $date) => $date->getTimestamp())
            ->first()
            ?->copy() ?? $start->copy()->addDays(14);

        if ($end->lt($start)) {
            $end = $start->copy()->addDays(14);
        }

        $scopeStarts = [];
        $doneDates = [];

        foreach ($issues as $issue) {
            $scopeStart = $this->normalizeDate($issue->created_at)?->startOfDay() ?? $start->copy();
            if ($scopeStart->lt($start)) {
                $scopeStart = $start->copy();
            }

            $scopeStarts[$scopeStart->toDateString()] = ($scopeStarts[$scopeStart->toDateString()] ?? 0) + 1;

            $doneAt = $doneAtByIssueId[(string) $issue->getKey()] ?? null;
            if (! $doneAt instanceof Carbon) {
                continue;
            }

            $doneDate = $doneAt->copy()->startOfDay();
            if ($doneDate->lt($start)) {
                $doneDate = $start->copy();
            }

            $doneDates[$doneDate->toDateString()] = ($doneDates[$doneDate->toDateString()] ?? 0) + 1;
        }

        $labels = [];
        $scopeSeries = [];
        $doneSeries = [];
        $remainingSeries = [];
        $scope = 0;
        $done = 0;
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            $key = $cursor->toDateString();
            $labels[] = $cursor->format('M j');

            $scope += (int) ($scopeStarts[$key] ?? 0);
            $done += (int) ($doneDates[$key] ?? 0);

            $scopeSeries[] = $scope;
            $doneSeries[] = $done;
            $remainingSeries[] = max(0, $scope - $done);

            $cursor->addDay();
        }

        return [
            'empty' => false,
            'labels' => $labels,
            'note' => 'Scope uses issue creation dates and first done transitions within the milestone.',
            'series' => [
                ['name' => 'Scope', 'data' => $scopeSeries],
                ['name' => 'Done', 'data' => $doneSeries],
                ['name' => 'Remaining', 'data' => $remainingSeries],
            ],
            'stats' => [
                'done' => $issues->filter(static fn (Issue $issue) => (bool) ($issue->status?->is_done ?? false))->count(),
                'open' => $issues->filter(static fn (Issue $issue) => ! ($issue->status?->is_done ?? false))->count(),
                'total' => $issues->count(),
            ],
            'subtitle' => $milestone->version,
            'title' => $milestone->name,
            'window' => $this->formatWindow($start, $end),
        ];
    }

    private function normalizeDate(mixed $value): ?Carbon
    {
        if ($value === null) {
            return null;
        }

        return Carbon::parse($value);
    }

    private function formatWindow(?Carbon $startsAt, ?Carbon $endsAt): ?string
    {
        if ($startsAt && $endsAt) {
            return $startsAt->format('M j').' to '.$endsAt->format('M j');
        }

        if ($startsAt) {
            return 'Starts '.$startsAt->format('M j');
        }

        if ($endsAt) {
            return 'Targets '.$endsAt->format('M j');
        }

        return null;
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
