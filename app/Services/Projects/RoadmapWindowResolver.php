<?php

namespace App\Services\Projects;

use App\Models\Issue;
use Carbon\CarbonInterface;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class RoadmapWindowResolver
{
    /**
     * @param  Collection<int, Issue>  $groupIssues
     */
    public function resolveWindowStart(Collection $groupIssues, ?Carbon $startsAt): ?Carbon
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
    public function resolveWindowEnd(Collection $groupIssues, ?Carbon $endsAt, ?Carbon $startsAt): ?Carbon
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

    public function formatWindow(?Carbon $startsAt, ?Carbon $endsAt): ?string
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

    public function normalizeDate(mixed $value): ?Carbon
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->copy();
        }

        if ($value instanceof CarbonInterface) {
            return Carbon::instance($value);
        }

        if ($value instanceof DateTimeInterface) {
            return Carbon::instance($value);
        }

        if (is_scalar($value)) {
            return Carbon::parse((string) $value, config('app.timezone'));
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return Carbon::parse((string) $value, config('app.timezone'));
        }

        return null;
    }
}
