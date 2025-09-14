<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTimeEntryRequest;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class IssueTimeController extends Controller
{
    /**
     * GET /api/v1/issues/{issue:id}/time/summary
     */
    public function summary(Request $request, Issue $issue): JsonResponse
    {
        $this->authorize('view', $issue);
        $this->ensureTimeAbility($request);

        return response()->json([
            'data' => [
                'total_seconds' => $issue->totalTrackedSeconds(),
                'running'       => $issue->timeEntries()
                    ->where('user_id', $request->user()->getKey())
                    ->whereNull('ended_at')
                    ->exists(),
            ],
        ]);
    }

    /**
     * POST /api/v1/issues/{issue:id}/time/start
     * Starts a running timer for the authenticated user.
     */
    public function start(Request $request, Issue $issue): JsonResponse
    {
        $this->authorize('update', $issue);
        $this->ensureTimeAbility($request);

        $userId = $request->user()->getKey();

        $existing = $issue->timeEntries()
            ->where('user_id', $userId)
            ->whereNull('ended_at')
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'A timer is already running for this issue.',
                'data'    => ['entry_id' => $existing->getKey()],
            ], 409);
        }

        $entry = $issue->timeEntries()->create([
            'user_id'          => $userId,
            'started_at'       => now(),
            'ended_at'         => null,
            'duration_seconds' => 0,
            'note'             => null,
        ]);

        return response()->json([
            'data' => [
                'entry_id' => $entry->getKey(),
                'running'  => true,
            ],
        ], 201);
    }

    /**
     * POST /api/v1/issues/{issue:id}/time/stop
     * Stops the current running timer for the authenticated user.
     */
    public function stop(Request $request, Issue $issue): JsonResponse
    {
        $this->authorize('update', $issue);
        $this->ensureTimeAbility($request);

        $userId = $request->user()->getKey();

        $entry = $issue->timeEntries()
            ->where('user_id', $userId)
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first();

        if (! $entry) {
            return response()->json(['message' => 'No running timer found.'], 404);
        }

        $ended = now();
        $seconds = max(1, $ended->diffInSeconds(Carbon::parse($entry->started_at)));

        $entry->forceFill([
            'ended_at'         => $ended,
            'duration_seconds' => $seconds,
        ])->save();

        return response()->json([
            'data' => [
                'entry_id'       => $entry->getKey(),
                'seconds'        => $seconds,
                'total_seconds'  => $issue->totalTrackedSeconds(),
                'running'        => false,
            ],
        ]);
    }

    /**
     * POST /api/v1/issues/{issue:id}/time
     * Manual log: accepts either {seconds} or {started_at, ended_at}, plus optional note.
     */
    public function store(StoreTimeEntryRequest $request, Issue $issue): JsonResponse
    {
        $this->authorize('update', $issue);

        $data = $request->validated();
        $userId = $request->user()->getKey();

        /** @var int|null $seconds */
        $seconds = $data['seconds'] ?? null;

        if ($seconds && $seconds > 0) {
            $endedAt = now();
            $startedAt = (clone $endedAt)->subSeconds($seconds);
        } else {
            /** @var \DateTimeInterface $startedAt */
            /** @var \DateTimeInterface $endedAt */
            $startedAt = Carbon::parse($data['started_at']);
            $endedAt   = Carbon::parse($data['ended_at']);
            $seconds   = max(1, $endedAt->diffInSeconds($startedAt));
        }

        $entry = $issue->timeEntries()->create([
            'user_id'          => $userId,
            'started_at'       => $startedAt,
            'ended_at'         => $endedAt,
            'duration_seconds' => $seconds,
            'note'             => $data['note'] ?? null,
        ]);

        return response()->json([
            'data' => [
                'entry_id'      => $entry->getKey(),
                'seconds'       => $seconds,
                'total_seconds' => $issue->totalTrackedSeconds(),
            ],
        ], 201);
    }

    /**
     * Ensure the token has a suitable ability (time:write or issues:write).
     */
    private function ensureTimeAbility(Request $request): void
    {
        $user = $request->user();
        abort_unless(
            $user && ($user->tokenCan('time:write') || $user->tokenCan('issues:write')),
            403,
            'Your token lacks the required ability (time:write or issues:write).'
        );
    }
}
