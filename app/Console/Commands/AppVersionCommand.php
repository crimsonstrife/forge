<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AppVersionCommand extends Command
{
    public const SUCCESS = 0;
    public const FAILURE = 1;
    public const INVALID = 2;

    protected $signature = 'app:version {action : read|write} {--value= : Version value to write (for write action)}';
    protected $description = 'Read or write the application VERSION file';

    public function handle(): int
    {
        $path = base_path('VERSION');
        $action = $this->argument('action');

        if (!in_array($action, ['read', 'write'], true)) {
            $this->error('Invalid action. Use "read" or "write".');
            return self::INVALID;
        }

        if ($action === 'read') {
            return $this->readVersion($path);
        }

        if ($action === 'write') {
            return $this->writeVersion($path);
        }

        return self::INVALID;
    }

    private function readVersion(string $path): int
    {
        if (!is_file($path)) {
            $this->line('VERSION not found');
            return self::SUCCESS;
        }

        try {
            $contents = file_get_contents($path);
        } catch (\Exception $e) {
            $this->error('Failed to read VERSION file: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->line(trim($contents));
        return self::SUCCESS;
    }

    private function writeVersion(string $path): int
    {
        $value = (string) $this->option('value');
        if ($value === '') {
            $this->error('Please provide --value="<semver>".');
            return self::INVALID;
        }

        if (!preg_match('/^\d+\.\d+\.\d+$/', $value)) {
            $this->error('Invalid version format. Use semantic versioning (e.g., 1.2.3).');
            return self::INVALID;
        }

        if (file_put_contents($path, trim($value) . PHP_EOL) === false) {
            $this->error('Failed to write to VERSION file.');
            return self::FAILURE;
        }

        $this->info("Wrote VERSION: {$value}");
        return self::SUCCESS;
    }
}
