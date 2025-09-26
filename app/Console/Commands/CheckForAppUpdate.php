<?php

namespace App\Console\Commands;

use App\Services\SelfUpdateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckForAppUpdate extends Command
{
    protected $signature = 'app:update:check';
    protected $description = 'Check for a newer app version and log a notice if found';

    public function __construct(private SelfUpdateService $updates)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $current = config('self-update.version_installed'); // or settings
        $available = $this->updates->isUpdateAvailable($current);

        if ($available) {
            $this->info('New version available.');
            Log::info('SelfUpdate: new version available', ['current' => $current]);
            // TODO: Dispatch notification to admin users/Slack/email here.
        } else {
            $this->line('No updates found.');
        }

        return self::SUCCESS;
    }
}
