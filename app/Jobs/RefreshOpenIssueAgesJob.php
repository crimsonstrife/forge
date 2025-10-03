<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

final class RefreshOpenIssueAgesJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(private readonly string $projectId)
    {
    }

    public function handle(): void
    {
        // Keep age_min, current_status_id, and is_done fresh for non-done issues.
        DB::update(<<<'SQL'
            UPDATE issue_metrics m
            JOIN issues i           ON i.id = m.issue_id
            JOIN issue_statuses s   ON s.id = i.issue_status_id
            SET
                m.age_min = TIMESTAMPDIFF(MINUTE, i.created_at, NOW()),
                m.current_status_id = i.issue_status_id,
                m.is_done = s.is_done,
                m.updated_at = NOW()
            WHERE m.project_id = ? AND s.is_done = 0
        SQL, [$this->projectId]);
    }
}
