<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class StartDiscordBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discord:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the Discord bot';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Discord bot...');

        // Use the `pgrep` command to check if the bot is already running
        $process = new Process(['pgrep', '-f', 'discord-bot.js']);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $exception) {
            // If the bot is not running, start it
            $this->info('Discord bot is not running. Starting bot...');

            $process = new Process(['node', 'discord-bot.js']);
            $process->start();

            $this->info('Discord bot started successfully.');
        } catch (\Exception $exception) {
            $this->error('An error occurred while starting the Discord bot.');
        }
    }
}
