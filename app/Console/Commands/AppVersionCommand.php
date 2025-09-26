<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AppVersionCommand extends Command
{
    protected $signature = 'app:version {action : read|write} {--value= : Version value to write (for write action)}';

    protected $description = 'Read or write the application VERSION file';

    public function handle(): int
    {
        $path = base_path('VERSION');

        if ($this->argument('action') === 'read') {
            $this->line(is_file($path) ? trim((string) @file_get_contents($path)) : 'VERSION not found');
            return self::SUCCESS;
            if (is_file($path)) {
                $contents = file_get_contents($path);
                if ($contents === false) {
                    $this->error('Failed to read VERSION file.');
                    return self::FAILURE;
                }
                $this->line(trim((string) $contents));
            } else {
                $this->line('VERSION not found');
            }

        if ($this->argument('action') === 'write') {
            $value = (string) $this->option('value');
            if ($value === '') {
                $this->error('Please provide --value="<semver>"');
                return self::INVALID;
            }

            file_put_contents($path, trim($value) . PHP_EOL);
            $this->info("Wrote VERSION: {$value}");
            return self::SUCCESS;
        }

        $this->error('Unknown action. Use read|write.');
        return self::INVALID;
    }
}
