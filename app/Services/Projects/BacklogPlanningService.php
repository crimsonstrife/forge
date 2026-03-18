<?php

namespace App\Services\Projects;

use App\Models\Issue;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

final class BacklogPlanningService
{
    /**
     * @param  array<int, string>  $issueIds
     */
    public function moveIssues(Project $project, array $issueIds, ?string $targetSprintId): int
    {
        $issueIds = $this->sanitizeIds($issueIds);

        if ($issueIds === []) {
            return 0;
        }

        return DB::transaction(function () use ($project, $issueIds, $targetSprintId): int {
            $issues = Issue::query()
                ->where('project_id', $project->id)
                ->whereIn('id', $issueIds)
                ->get(['id', 'sprint_id'])
                ->keyBy(fn (Issue $issue) => (string) $issue->id);

            $orderedIds = [];
            $lanesToNormalize = [$targetSprintId];

            foreach ($issueIds as $issueId) {
                $issue = $issues->get($issueId);

                if (! $issue instanceof Issue) {
                    continue;
                }

                $orderedIds[] = (string) $issue->id;
                $lanesToNormalize[] = $issue->sprint_id;
            }

            if ($orderedIds === []) {
                return 0;
            }

            foreach ($this->uniqueLaneIds($lanesToNormalize) as $laneSprintId) {
                $this->ensureLaneOrdering($project->id, $laneSprintId);
            }

            $nextOrder = $this->nextPlanningOrder($project->id, $targetSprintId);

            $issueUpdates = [];

            foreach ($orderedIds as $offset => $issueId) {
                $issueUpdates[] = [
                    'id' => $issueId,
                    'project_id' => $project->id,
                    'sprint_id' => $targetSprintId,
                    'planning_order' => $nextOrder + $offset,
                ];
            }

            if ($issueUpdates !== []) {
                Issue::upsert(
                    $issueUpdates,
                    ['id', 'project_id'],
                    ['sprint_id', 'planning_order']
                );
            }

            foreach ($this->uniqueLaneIds($lanesToNormalize) as $laneSprintId) {
                $this->normalizeLane($project->id, $laneSprintId);
            }

            return count($orderedIds);
        });
    }

    /**
     * @param  array<int, string>  $orderedIssueIds
     */
    public function reorderLane(Project $project, ?string $sprintId, array $orderedIssueIds): void
    {
        $orderedIssueIds = $this->sanitizeIds($orderedIssueIds);

        if ($orderedIssueIds === []) {
            return;
        }

        DB::transaction(function () use ($project, $sprintId, $orderedIssueIds): void {
            $this->ensureLaneOrdering($project->id, $sprintId);

            $laneIssueIds = Issue::query()
                ->where('project_id', $project->id)
                ->when(
                    $sprintId === null,
                    fn ($query) => $query->whereNull('sprint_id'),
                    fn ($query) => $query->where('sprint_id', $sprintId)
                )
                ->whereIn('id', $orderedIssueIds)
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->all();

            $rank = 1;

            $issueUpdates = [];

            foreach ($orderedIssueIds as $issueId) {
                if (! in_array($issueId, $laneIssueIds, true)) {
                    continue;
                }

                $issueUpdates[] = [
                    'id' => $issueId,
                    'project_id' => $project->id,
                    'planning_order' => $rank,
                ];

                $rank++;
            }

            if ($issueUpdates !== []) {
                Issue::upsert(
                    $issueUpdates,
                    ['id', 'project_id'],
                    ['planning_order']
                );
            }
        });
    }

    private function nextPlanningOrder(string $projectId, ?string $sprintId): int
    {
        $max = Issue::query()
            ->where('project_id', $projectId)
            ->when(
                $sprintId === null,
                fn ($query) => $query->whereNull('sprint_id'),
                fn ($query) => $query->where('sprint_id', $sprintId)
            )
            ->lockForUpdate()
            ->max('planning_order');

        return ((int) $max) + 1;
    }

    private function ensureLaneOrdering(string $projectId, ?string $sprintId): void
    {
        $hasMissingOrder = Issue::query()
            ->where('project_id', $projectId)
            ->when(
                $sprintId === null,
                fn ($query) => $query->whereNull('sprint_id'),
                fn ($query) => $query->where('sprint_id', $sprintId)
            )
            ->whereNull('planning_order')
            ->exists();

        if (! $hasMissingOrder) {
            return;
        }

        $this->normalizeLane($projectId, $sprintId);
    }

    private function normalizeLane(string $projectId, ?string $sprintId): void
    {
        $issueIds = Issue::query()
            ->where('project_id', $projectId)
            ->when(
                $sprintId === null,
                fn ($query) => $query->whereNull('sprint_id'),
                fn ($query) => $query->where('sprint_id', $sprintId)
            )
            ->orderedForPlanning()
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        foreach ($issueIds as $index => $issueId) {
            Issue::query()
                ->whereKey($issueId)
                ->where('project_id', $projectId)
                ->update(['planning_order' => $index + 1]);
        }
    }

    /**
     * @param  array<int, mixed>  $issueIds
     * @return array<int, string>
     */
    private function sanitizeIds(array $issueIds): array
    {
        return array_values(array_unique(array_filter(
            array_map(
                static fn ($id): string => trim((string) $id),
                $issueIds
            ),
            static fn (string $id): bool => $id !== ''
        )));
    }

    /**
     * @param  array<int, string|null>  $laneIds
     * @return array<int, string|null>
     */
    private function uniqueLaneIds(array $laneIds): array
    {
        $seen = [];
        $unique = [];

        foreach ($laneIds as $laneId) {
            $key = $laneId ?? '__backlog__';

            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $unique[] = $laneId;
        }

        return $unique;
    }
}
