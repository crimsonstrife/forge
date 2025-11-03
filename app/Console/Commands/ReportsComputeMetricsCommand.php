<?php

namespace App\Console\Commands;

use App\Jobs\ComputeIssueMetricsJob;
use App\Models\Issue;
use Illuminate\Console\Command;

final class ReportsComputeMetricsCommand extends Command
{
    protected $signature = 'reports:compute-metrics {--project=*} {--only-open} {--chunk=500}';
    protected $description = 'Compute or refresh issue metrics (cycle/lead/age)';

    public function handle(): int
    {
        $chunk = (int) $this->option('chunk');
        $projects = (array) $this->option('project');
        $onlyOpen = (bool) $this->option('only-open');

        $q = Issue::query()->select(['id','project_id','issue_status_id']);

        if (!empty($projects)) {
            $q->whereIn('project_id', $projects);
        }

        if ($onlyOpen) {
            $q->whereHas('status', static fn ($s) => $s->where('is_done', false));
        }

        $count = 0;
        $q->chunkById($chunk, function ($issues) use (&$count): void {
            foreach ($issues as $issue) {
                dispatch(new ComputeIssueMetricsJob($issue->id))->onQueue('reports');
                $count++;
            }
        });

        $this->info("Dispatched {$count} metric job(s).");
        return self::SUCCESS;
    }
}
