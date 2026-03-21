<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use RuntimeException;

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
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            $ageExpr = 'TIMESTAMPDIFF(MINUTE, i.created_at, NOW())';
            $nowExpr = 'NOW()';
        } elseif ($driver === 'pgsql') {
            $ageExpr = 'FLOOR(EXTRACT(EPOCH FROM (NOW() - i.created_at)) / 60)';
            $nowExpr = 'NOW()';
        } elseif ($driver === 'sqlite') {
            $ageExpr = "CAST((strftime('%s','now') - strftime('%s', i.created_at)) / 60 AS INTEGER)";
            $nowExpr = "CURRENT_TIMESTAMP";
        } else {
            throw new RuntimeException("Unsupported database driver: $driver");
        }

        $sql = "
            UPDATE issue_metrics m
            JOIN issues i           ON i.id = m.issue_id
            JOIN issue_statuses s   ON s.id = i.issue_status_id
            SET
                m.age_min = $ageExpr,
                m.current_status_id = i.issue_status_id,
                m.is_done = s.is_done,
                m.updated_at = $nowExpr
            WHERE m.project_id = ? AND s.is_done = 0
        ";

        // For SQLite, JOINs in UPDATE are not supported, so we need a workaround.
        if ($driver === 'sqlite') {
            // Use a subquery to select the relevant rows and update them one by one.
            $issues = DB::table('issue_metrics as m')
                ->join('issues as i', 'i.id', '=', 'm.issue_id')
                ->join('issue_statuses as s', 's.id', '=', 'i.issue_status_id')
                ->where('m.project_id', $this->projectId)
                ->where('s.is_done', 0)
                ->select('m.issue_id', 'i.created_at', 'i.issue_status_id', 's.is_done')
                ->get();

            foreach ($issues as $row) {
                $age = (int) ((strtotime('now') - strtotime($row->created_at)) / 60);
                DB::table('issue_metrics')
                    ->where('issue_id', $row->issue_id)
                    ->update([
                        'age_min' => $age,
                        'current_status_id' => $row->issue_status_id,
                        'is_done' => $row->is_done,
                        'updated_at' => now(),
                    ]);
            }
        } else {
            DB::update($sql, [$this->projectId]);
        }
    }
}
