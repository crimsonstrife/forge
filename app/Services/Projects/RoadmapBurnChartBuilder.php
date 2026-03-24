<?php

namespace App\Services\Projects;

use App\Models\Issue;
use App\Models\IssueStatus;
use App\Models\IssueStatusEvent;
use App\Models\Milestone;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class RoadmapBurnChartBuilder
{
    public function __construct(private readonly RoadmapWindowResolver $windowResolver)
    {
    }

    /**
     * @param  Collection<int, Issue>  $issues
     * @return array<string, mixed>
     */
    public function build(?Milestone $milestone, Collection $issues): array
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
                ? $this->windowResolver->normalizeDate($doneEvent->changed_at)
                : (($issue->status?->is_done ?? false) ? $this->windowResolver->normalizeDate($issue->updated_at) : null);

            $doneAtByIssueId[(string) $issue->getKey()] = $doneAt;

            if ($doneAt && ($latestDoneAt === null || $doneAt->gt($latestDoneAt))) {
                $latestDoneAt = $doneAt;
            }
        }

        $start = $this->windowResolver->normalizeDate($milestone->starts_at)
            ?? $issues
                ->map(fn (Issue $issue) => $this->windowResolver->normalizeDate($issue->created_at))
                ->filter()
                ->sortBy(static fn (Carbon $date) => $date->getTimestamp())
                ->first()
            ?? now()->copy()->startOfDay();

        $milestoneEnd = $this->windowResolver->normalizeDate($milestone->released_at ?? $milestone->due_at);
        $latestCreatedAt = $issues
            ->map(fn (Issue $issue) => $this->windowResolver->normalizeDate($issue->created_at))
            ->filter()
            ->sortByDesc(static fn (Carbon $date) => $date->getTimestamp())
            ->first();

        $endCandidates = collect([
            $milestoneEnd,
            $latestDoneAt,
            $latestCreatedAt,
        ])->filter();

        if ($this->shouldExtendToToday($milestoneEnd, $issues)) {
            $endCandidates->push(now()->copy()->startOfDay());
        }

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
            $scopeStart = $this->windowResolver->normalizeDate($issue->created_at)?->startOfDay() ?? $start->copy();
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
            'window' => $this->windowResolver->formatWindow($start, $end),
        ];
    }

    /**
     * @param  Collection<int, Issue>  $issues
     */
    private function shouldExtendToToday(?Carbon $milestoneEnd, Collection $issues): bool
    {
        if ($milestoneEnd === null || $milestoneEnd->isFuture()) {
            return true;
        }

        return $issues->contains(static fn (Issue $issue): bool => ! ($issue->status?->is_done ?? false));
    }
}
