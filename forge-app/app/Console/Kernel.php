<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel
 *
 * This class is the console kernel that extends the Laravel console kernel. It is used to schedule commands and register commands for the application.
 */
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // Add your scheduled tasks here.
        // $schedule->command('repositories:update-metadata')->daily();  TODO: Uncomment this line when the command is ready to be scheduled
        // $schedule->command('discord:run')->everyMinute(); TODO: Uncomment this line when the command is ready to be scheduled
        $schedule->command('project-views:clear 30')->daily(); // Clear 30-day old project views every day
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
