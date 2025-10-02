<?php

namespace App\Console\Commands;

use App\Jobs\BuildProjectDailyReportsJob;
use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ReportsBackfillCommand extends Command
{
    protected $signature = 'reports:backfill {--days=60} {--project=*}';
    protected $description = 'Backfill project reporting snapshots for the last N days.';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $projectIds = (array) $this->option('project');

        $query = Project::query()->select('id');
        if (!empty($projectIds)) {
            $query->whereIn('id', $projectIds);
        }

        $dates = collect(range(0, $days - 1))
            ->map(static fn (int $i): Carbon => now()->copy()->subDays($i)->startOfDay())
            ->reverse()
            ->values();

        $query->chunkById(200, function ($projects) use ($dates): void {
            foreach ($projects as $project) {
                foreach ($dates as $date) {
                    dispatch(new BuildProjectDailyReportsJob($project->id, $date))->onQueue('reports');
                }
            }
        });

        $this->info("Dispatched backfill for {$days} day(s).");
        return self::SUCCESS;
    }
}
