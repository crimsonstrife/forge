<?php

namespace App\Jobs;

use App\Domain\Issues\IssueRollupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class RecalculateIssueRollups implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly string $parentIssueId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(IssueRollupService $service): void
    {
        $service->recalc($this->parentIssueId, true);
    }
}
