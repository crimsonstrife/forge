<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ScheduleDiscordBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discord:bot-schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule the Discord bot to run';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Schedule the Discord bot to run every minute, but only if the bot is not already running. This will prevent multiple instances of the bot from running.
        $this->info('Scheduling Discord bot to run...');
        // Use the `pgrep` command to check if the bot is already running
        $isRunning = exec('pgrep -f discord-bot.js');

        if (!$isRunning) {
            // If the bot is not running, schedule the bot to run
            exec('php artisan discord:run');
        } else {
            $this->info('Discord bot is already running.');
        }
    }
}
