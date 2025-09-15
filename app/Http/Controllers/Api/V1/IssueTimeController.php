<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreTimeEntryRequest;
use App\Models\Issue;
use Carbon\CarbonImmutable;
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

        $running = $issue->timeEntries()
            ->where('user_id', $request->user()->getKey())
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first();

        $total = (int) $issue->totalTrackedSeconds();

        return response()->json([
            'data' => [
                'total_seconds'      => $total,
                'running'            => (bool) $running,
                'running_started_at' => $running?->started_at?->toImmutable()?->toIso8601String(),
                'elapsed_seconds'    => $running
                    ? $total + now()->diffInSeconds($running->started_at)
                    : $total,
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

        // Close any stray running entries for this user on this issue
        $issue->timeEntries()
            ->where('user_id', $request->user()->getKey())
            ->whereNull('ended_at')
            ->update([
                'ended_at' => now(),
                'duration_seconds' => \DB::raw('TIMESTAMPDIFF(SECOND, started_at, NOW())'),
            ]);

        $at = $this->parseWhen($request->input('at')); // null => now()

        $entry = $issue->timeEntries()->create([
            'user_id'   => $request->user()->getKey(),
            'started_at'=> $at ?? now(),
            'note'      => (string) $request->string('note'), // optional
        ]);

        return response()->json([
            'data' => [
                'running'            => true,
                'running_started_at' => $entry->started_at?->toImmutable()?->toIso8601String(),
                'entry' => [
                    'id'         => $entry->id,
                    'started_at' => $entry->started_at?->toIso8601String(),
                ],
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

        $running = $issue->timeEntries()
            ->where('user_id', $request->user()->getKey())
            ->whereNull('ended_at')
            ->latest('started_at')
            ->firstOrFail();

        $endedAt = $this->parseWhen($request->input('at')) ?? now();

        $running->ended_at = $endedAt;
        $running->duration_seconds = max(
            0,
            $endedAt->diffInSeconds($running->started_at)
        );
        $running->save();

        return response()->json([
            'data' => [
                'running'         => false,
                'entry' => [
                    'id'               => $running->id,
                    'started_at'       => $running->started_at?->toIso8601String(),
                    'ended_at'         => $running->ended_at?->toIso8601String(),
                    'duration_seconds' => $running->duration_seconds,
                ],
                'summary' => [
                    'total_seconds' => (int) $issue->totalTrackedSeconds(),
                ],
            ],
        ]);
    }

    /**
     * POST /api/v1/issues/{issue:id}/time
     * Manual log: accepts either {seconds} or {started_at, ended_at}, plus optional note.
     */
    public function store(StoreTimeEntryRequest $req, Issue $issue): JsonResponse
    {
        $this->authorize('update', $issue);

        $started = CarbonImmutable::parse($req->validated('started_at'));
        $ended   = CarbonImmutable::parse($req->validated('ended_at'));

        $entry = $issue->timeEntries()->create([
            'user_id'          => $req->user()->getKey(),
            'started_at'       => $started,
            'ended_at'         => $ended,
            'duration_seconds' => max(0, $ended->diffInSeconds($started)),
            'note'             => (string) $req->validated('note', ''),
        ]);

        return response()->json([
            'data' => [
                'entry' => [
                    'id'               => $entry->id,
                    'started_at'       => $entry->started_at?->toIso8601String(),
                    'ended_at'         => $entry->ended_at?->toIso8601String(),
                    'duration_seconds' => $entry->duration_seconds,
                ],
                'summary' => [
                    'total_seconds' => (int) $issue->totalTrackedSeconds(),
                ],
            ],
        ], 201);
    }

    private function parseWhen(?string $iso): ?CarbonImmutable
    {
        if (!$iso) return null;
        try { return CarbonImmutable::parse($iso); } catch (\Throwable) { return null; }
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
