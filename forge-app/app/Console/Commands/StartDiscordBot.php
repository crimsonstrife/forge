<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
        $isRunning = exec('pgrep -f discord-bot.js');

        // Start the bot with `pm2` if it is not already running
        if (!$isRunning) {
            exec('pm2 start /path/to/bot/discord-bot.js');
            $this->info('Discord bot started.');
        } else {
            $this->info('Discord bot is already running.');
        }
    }
}
