<?php

namespace App\Console\Commands;

use App\Models\ProjectRepository;
use App\Models\SocialAccount;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Throwable;

/**
 * Normalizes sensitive token columns by:
 * - Accepting either plaintext or Laravel-encrypted payloads
 * - Re-encrypting plaintext values
 * - Nulling obviously masked placeholders (e.g., "********")
 *
 * Usage:
 *   php artisan tokens:normalize               # process both models
 *   php artisan tokens:normalize --only=project
 *   php artisan tokens:normalize --only=social
 *   php artisan tokens:normalize --dry
 *   php artisan tokens:normalize --chunk=500
 */
final class NormalizeTokens extends Command
{
    /** @var string */
    protected $signature = 'tokens:normalize
        {--only=both : project|social|both}
        {--dry : Dry-run; report but do not write}
        {--chunk=200 : Chunk size for batched processing}';

    /** @var string */
    protected $description = 'Normalize and encrypt tokens across models, removing masked placeholders.';

    public function handle(): int
    {
        $only    = strtolower((string) $this->option('only'));
        $dryRun  = (bool) $this->option('dry');
        $chunk   = max(50, (int) $this->option('chunk'));

        if (!in_array($only, ['project', 'social', 'both'], true)) {
            $this->error('Invalid --only option. Use project|social|both.');
            return self::INVALID;
        }

        $this->info($dryRun ? 'Dry run: no data will be written.' : 'Running: changes will be persisted.');
        $this->newLine();

        $totals = [
            'project' => ['total' => 0, 'updated' => 0, 'nullified' => 0, 'skipped' => 0, 'errors' => 0],
            'social'  => ['total' => 0, 'updated' => 0, 'nullified' => 0, 'skipped' => 0, 'errors' => 0],
        ];

        if ($only === 'project' || $only === 'both') {
            $this->section('ProjectRepository.token');
            $totals['project'] = $this->normalizeProjectRepositories($chunk, $dryRun);
            $this->summaryTable($totals['project']);
            $this->newLine();
        }

        if ($only === 'social' || $only === 'both') {
            $this->section('SocialAccount.token & refresh_token');
            $totals['social'] = $this->normalizeSocialAccounts($chunk, $dryRun);
            $this->summaryTable($totals['social']);
            $this->newLine();
        }

        $this->info('Done.');
        return ($totals['project']['errors'] + $totals['social']['errors']) > 0
            ? self::FAILURE
            : self::SUCCESS;
    }

    /**
     * @return array{total:int,updated:int,nullified:int,skipped:int,errors:int}
     */
    private function normalizeProjectRepositories(int $chunk, bool $dryRun): array
    {
        $stats = ['total' => 0, 'updated' => 0, 'nullified' => 0, 'skipped' => 0, 'errors' => 0];

        $query = ProjectRepository::query()->whereNotNull('token');
        $count = (int) $query->count();
        $stats['total'] = $count;

        if ($count === 0) {
            $this->line('No rows to process.');
            return $stats;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $query->orderBy('id')->chunkById($chunk, function ($rows) use (&$stats, $dryRun, $bar) {
            /** @var ProjectRepository $row */
            foreach ($rows as $row) {
                try {
                    $raw   = (string) $row->getRawOriginal('token');
                    $plain = $this->sanitize($row->token); // cast returns decrypted-or-plain

                    if ($plain === null || $plain === '') {
                        if (!$dryRun) {
                            $row->forceFill(['token' => null])->saveQuietly();
                        }
                        $stats['nullified']++;
                        $bar->advance();
                        continue;
                    }

                    if ($this->isMasked($plain)) {
                        if (!$dryRun) {
                            $row->forceFill(['token' => null])->saveQuietly();
                        }
                        $stats['nullified']++;
                        $bar->advance();
                        continue;
                    }

                    if ($this->rawDecryptsTo($raw, $plain)) {
                        $stats['skipped']++;
                        $bar->advance();
                        continue;
                    }

                    if (!$dryRun) {
                        // Setting to the same plaintext will encrypt via cast/custom cast.
                        $row->forceFill(['token' => $plain])->saveQuietly();
                    }
                    $stats['updated']++;
                } catch (Throwable $e) {
                    $stats['errors']++;
                    $this->output->writeln("\n<error>Row {$row->id} failed: {$e->getMessage()}</error>");
                } finally {
                    $bar->advance();
                }
            }
        });

        $bar->finish();
        $this->newLine();

        return $stats;
    }

    /**
     * @return array{total:int,updated:int,nullified:int,skipped:int,errors:int}
     */
    private function normalizeSocialAccounts(int $chunk, bool $dryRun): array
    {
        $stats = ['total' => 0, 'updated' => 0, 'nullified' => 0, 'skipped' => 0, 'errors' => 0];

        $columns = ['token', 'refresh_token'];

        foreach ($columns as $column) {
            $this->line("Processing column: {$column}");

            $query = SocialAccount::query()->whereNotNull($column);
            $count = (int) $query->count();
            $stats['total'] += $count;

            if ($count === 0) {
                $this->line('No rows to process.');
                continue;
            }

            $bar = $this->output->createProgressBar($count);
            $bar->start();

            $query->orderBy('id')->chunkById($chunk, function ($rows) use (&$stats, $dryRun, $bar, $column) {
                /** @var SocialAccount $row */
                foreach ($rows as $row) {
                    try {
                        $raw   = (string) $row->getRawOriginal($column);
                        $plain = $this->sanitize($row->{$column}); // cast returns decrypted-or-plain

                        if ($plain === null || $plain === '') {
                            if (!$dryRun) {
                                $row->forceFill([$column => null])->saveQuietly();
                            }
                            $stats['nullified']++;
                            $bar->advance();
                            continue;
                        }

                        if ($this->isMasked($plain)) {
                            if (!$dryRun) {
                                $row->forceFill([$column => null])->saveQuietly();
                            }
                            $stats['nullified']++;
                            $bar->advance();
                            continue;
                        }

                        if ($this->rawDecryptsTo($raw, $plain)) {
                            $stats['skipped']++;
                            $bar->advance();
                            continue;
                        }

                        if (!$dryRun) {
                            $row->forceFill([$column => $plain])->saveQuietly();
                        }
                        $stats['updated']++;
                    } catch (Throwable $e) {
                        $stats['errors']++;
                        $this->output->writeln("\n<error>Row {$row->id} failed: {$e->getMessage()}</error>");
                    } finally {
                        $bar->advance();
                    }
                }
            });

            $bar->finish();
            $this->newLine();
        }

        return $stats;
    }

    private function section(string $title): void
    {
        $this->newLine();
        $this->line("<info>{$title}</info>");
        $this->line(str_repeat('-', max(12, mb_strlen($title))));
    }

    /**
     * @param array{total:int,updated:int,nullified:int,skipped:int,errors:int} $data
     */
    private function summaryTable(array $data): void
    {
        $this->table(
            ['Total', 'Updated', 'Nullified', 'Skipped', 'Errors'],
            [[
                (string) $data['total'],
                (string) $data['updated'],
                (string) $data['nullified'],
                (string) $data['skipped'],
                (string) $data['errors'],
            ]]
        );
    }

    private function sanitize(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $v = preg_replace('/\s+/', '', trim($value));
        return $v === '' ? null : $v;
    }

    private function isMasked(?string $value): bool
    {
        if ($value === null) {
            return false;
        }

        $v = trim($value);
        return $v !== '' && (preg_match('/^\*+$/', $v) === 1 || str_starts_with($v, '***'));
    }

    private function rawDecryptsTo(?string $raw, ?string $expected): bool
    {
        if ($raw === null || $expected === null) {
            return false;
        }

        try {
            $dec = Crypt::decryptString($raw);
            return $this->sanitize($dec) === $this->sanitize($expected);
        } catch (Throwable $e) {
            return false;
        }
    }
}
