<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/**
 * Command to display an inspiring quote.
 *
 * This command will display an inspiring quote using the Inspiring class.
 * It can be executed hourly.
 *
 * @return void
 */
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
