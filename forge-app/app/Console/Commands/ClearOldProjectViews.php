<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ClearOldProjectViews
 *
 * This command is responsible for clearing old project views.
 */
class ClearOldProjectViews extends Command
{
    protected $signature = 'project-views:clear {days=30}';
    protected $description = 'Clear project views older than a specified number of days';

    /**
     * Handle the command to clear old project views.
     *
     * This method is the main entry point for the command execution.
     *
     * @return void
     */
    public function handle()
    {
        $days = (int) $this->argument('days');
        $cutoff = now()->subDays($days);

        // Check both created_at and updated_at timestamps, as we may have views that were created but not updated
        $deleted = DB::table('project_views')
            ->where('created_at', '<', $cutoff)
            ->orWhere('updated_at', '<', $cutoff)
            ->delete();

        $this->info("Deleted $deleted old project views.");
    }
}
