<?php

namespace App\Services\Projects;

use App\Models\Issue;
use App\Models\Project;
use Illuminate\Support\Collection;

final class RoadmapDependencyAnalyzer
{
    /**
     * @param  array<string, array<string, mixed>>  $groups
     * @param  array<string, string>  $groupIdByIssueId
     * @param  Collection<int, Issue>  $issues
     * @return array{
     *     dependencyEdges: array<int, array<string, mixed>>,
     *     groups: array<string, array<string, mixed>>
     * }
     */
    public function analyze(
        Project $project,
        array $groups,
        array $groupIdByIssueId,
        Collection $issues,
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
                    project: $project,
                    groups: $groups,
                    groupIdByIssueId: $groupIdByIssueId,
                    groupBy: $groupBy,
                    blocker: $link->from,
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

        return [
            'dependencyEdges' => collect($dependencyEdges)
                ->map(static function (array $edge): array {
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
                ->all(),
            'groups' => $groups,
        ];
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

        if ($groupBy === 'milestone' && $blocker->milestone && $blocker->project) {
            return [
                'id' => 'external:milestone:'.$blocker->milestone->id,
                'label' => $projectKey.' · '.$blocker->milestone->name,
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
}
