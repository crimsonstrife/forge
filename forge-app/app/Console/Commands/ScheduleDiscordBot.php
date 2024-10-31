<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Log;

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
    $this->info('Checking if Discord bot is already running...');

    // Use `pgrep` to check if the bot is running by looking for the process name 'discord-bot.js'
    $process = new Process(['pgrep', '-f', 'discord-bot.js']);

    try {
        $process->setTimeout(60);  // Set a timeout of 60 seconds
        $process->mustRun();

        // If the process exists, `mustRun` would not throw an exception, so we know the bot is already running
        $this->info('Discord bot is already running.');
    } catch (ProcessFailedException $exception) {
        // If the process does not exist, `mustRun` will throw a ProcessFailedException
        $this->info('Discord bot is not running. Scheduling bot to run every minute...');

        // Get the current crontab
        $crontabProcess = new Process(['crontab', '-l']);
        $crontabProcess->run();
        $currentCron = $crontabProcess->isSuccessful() ? $crontabProcess->getOutput() : '';

        // Define the new cron entry safely
        $nodePath = escapeshellarg('/usr/bin/node');
        $botScriptPath = escapeshellarg(base_path('bot/discord-bot.js'));
        $newCronEntry = "* * * * * {$nodePath} {$botScriptPath}";

        // Append the new cron entry if it's not already present
        if (strpos($currentCron, $newCronEntry) === false) {
            $updatedCron = $currentCron . PHP_EOL . $newCronEntry . PHP_EOL;

            // Write the updated cron configuration to a temporary file
            $tempCronFile = tempnam(sys_get_temp_dir(), 'cron');
            file_put_contents($tempCronFile, $updatedCron);

            // Install the new crontab from the temporary file
            $installCronProcess = new Process(['crontab', $tempCronFile]);
            $installCronProcess->run();

            // Clean up the temporary file
            unlink($tempCronFile);

            $this->info('Discord bot scheduled successfully.');
        } else {
            $this->info('Discord bot schedule already exists in crontab.');
        }
    } catch (\Exception $exception) {
        $this->error('An error occurred while scheduling the Discord bot: ' . $exception->getMessage());
        Log::error('Error scheduling Discord bot: ' . $exception->getMessage());
    }
}
}
