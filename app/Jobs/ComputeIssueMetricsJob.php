<?php

namespace App\Jobs;

use App\Models\Issue;
use App\Models\IssueMetric;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection as ECollection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class ComputeIssueMetricsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly string $issueId
    ) {
    }

    public function handle(): void
    {
        /** @var Issue|null $issue */
        $issue = Issue::query()
            ->select(['id','project_id','issue_status_id','created_at'])
            ->find($this->issueId);

        if ($issue === null) {
            return;
        }

        // status events are written by IssueObserver
        /** @var ECollection<int,object> $events */
        $events = DB::table('issue_status_events')
            ->where('issue_id', $issue->id)
            ->orderBy('changed_at')
            ->get(['to_status_id','changed_at']);

        $firstStartedAt = $events->first()?->changed_at ? Carbon::parse($events->first()->changed_at) : null;

            $firstDoneEvent = $events->firstWhere(fn ($e) => in_array((int) $e->to_status_id, $doneStatusIds, true));
            $firstDoneAt = $firstDoneEvent
                ? Carbon::parse($firstDoneEvent->changed_at)
            // first time it entered a "done" status
            $doneStatusIds = DB::table('issue_statuses')->where('is_done', true)->pluck('id')->all();
            $firstDoneAt = $events->firstWhere(fn ($e) => in_array((int) $e->to_status_id, $doneStatusIds, true))
                ? Carbon::parse($events->firstWhere(fn ($e) => in_array((int) $e->to_status_id, $doneStatusIds, true))->changed_at)
                : null;
        }

        $now = now();
        $isDone = (bool)DB::table('issue_statuses')->where('id', $issue->issue_status_id)->value('is_done');

        $leadMin  = $firstDoneAt ? (int) $issue->created_at->diffInMinutes($firstDoneAt) : 0;
        $cycleMin = match (true) {
            $firstStartedAt && $firstDoneAt => (int) $firstStartedAt->diffInMinutes($firstDoneAt),
            $firstStartedAt && !$firstDoneAt => (int) $firstStartedAt->diffInMinutes($now),
            default => 0,
        };
        $ageMin   = $isDone ? 0 : (int) $issue->created_at->diffInMinutes($now);

        IssueMetric::query()->updateOrCreate(
            ['issue_id' => $issue->id],
            [
                'project_id'        => $issue->project_id,
                'first_started_at'  => $firstStartedAt,
                'first_done_at'     => $firstDoneAt,
                'lead_time_min'     => $leadMin,
                'cycle_time_min'    => $cycleMin,
                'age_min'           => $ageMin,
                'current_status_id' => (int) $issue->issue_status_id,
                'is_done'           => $isDone,
            ]
        );
    }
}
