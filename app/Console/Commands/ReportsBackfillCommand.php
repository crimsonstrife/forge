<?php

namespace App\Console\Commands;

use App\Jobs\BuildProjectDailyReportsJob;
use App\Jobs\BuildSprintDailyReportsJob;
use App\Models\Project;
use App\Models\Sprint;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ReportsBackfillCommand extends Command
{
    protected $signature = 'reports:backfill {--days=60} {--project=*} {--sprint=*}';
    protected $description = 'Backfill project & sprint reporting snapshots for the last N days.';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $projectIds = (array) $this->option('project');
        $sprintIds  = (array) $this->option('sprint');

        $dates = collect(range(0, $days - 1))
            ->map(static fn (int $i): Carbon => now()->copy()->subDays($i)->startOfDay())
            ->reverse()
            ->values();

        // Projects
        $projQ = Project::query()->select('id');
        if (!empty($projectIds)) {
            $projQ->whereIn('id', $projectIds);
        }

        $projQ->chunkById(200, function ($projects) use ($dates): void {
            foreach ($projects as $p) {
                foreach ($dates as $d) {
                    dispatch(new BuildProjectDailyReportsJob($p->id, $d))->onQueue('reports');
                }
            }
        });

        // Sprints
        $sprQ = Sprint::query()->select(['id', 'project_id']);
        if (!empty($sprintIds)) {
            $sprQ->whereIn('id', $sprintIds);
        }

        $sprQ->chunkById(200, function ($sprints) use ($dates): void {
            foreach ($sprints as $s) {
                foreach ($dates as $d) {
                    dispatch(new BuildSprintDailyReportsJob($s->project_id, $s->id, $d))->onQueue('reports');
                }
            }
        });

        $this->info("Dispatched backfill for {$days} day(s).");
        return self::SUCCESS;
    }
}
