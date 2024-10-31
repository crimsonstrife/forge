<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

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
        $process = new Process(['pgrep', '-f', 'discord-bot.js']);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $exception) {
            // If the bot is not running, start it
            $this->info('Discord bot is not running. Scheduling bot to run...');

            $process = new Process(['crontab', '-l']);
            $process->run();

            $cron = $process->getOutput();
            $cron .= "* * * * * /usr/bin/node /path/to/discord-bot.js\n";

            $process = new Process(['echo', $cron, '|', 'crontab', '-']);
            $process->run();

            $this->info('Discord bot scheduled successfully.');
        } catch (\Exception $exception) {
            $this->error('An error occurred while scheduling the Discord bot.');
        }
    }
}
