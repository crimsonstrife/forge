<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TranslateLangFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:lang {locales}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a JSON translation file and do the translations for you.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
