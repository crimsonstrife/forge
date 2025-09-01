<?php

namespace App\Console\Commands;

use App\Domain\Issues\IssueRollupService;
use App\Models\Issue;
use Illuminate\Console\Command;

class RecalcIssueRollups extends Command
{
    protected $signature = 'issues:recalc-rollups {--no-bubble : Do not bubble progress to ancestors}';
    protected $description = 'Recalculate roll-up metrics (children counts, points, and progress_percent) for all parent issues.';

    public function handle(IssueRollupService $service): int
    {
        $parentIds = Issue::query()
            ->whereIn('id', function ($q) {
                $q->select('parent_id')->from('issues')->whereNotNull('parent_id')->distinct();
            })
            ->pluck('id');

        $bar = $this->output->createProgressBar($parentIds->count());
        $bar->start();

        $bubble = ! $this->option('no-bubble');

        foreach ($parentIds as $id) {
            $service->recalc((string) $id, $bubble);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done.');
        return self::SUCCESS;
    }
}
