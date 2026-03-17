<?php

namespace App\Console\Commands;

use App\Models\IssueExternalRef;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RepairIssueExternalRefNumbers extends Command
{
    protected $signature = 'forge:repair-issue-external-ref-numbers
                            {--repository= : Limit repairs to a single repository UUID}
                            {--dry-run : Show what would change without saving}
                            {--chunk=200 : Number of records to process per chunk}';

    protected $description = 'Repair issue_external_refs.number when it no longer matches payload.number';

    public function handle(): int
    {
        $repositoryId = $this->option('repository');
        $dryRun = (bool) $this->option('dry-run');
        $chunkSize = max((int) $this->option('chunk'), 1);

        $query = IssueExternalRef::query()
            ->select([
                'id',
                'repository_id',
                'issue_id',
                'external_issue_id',
                'number',
                'payload',
            ])
            ->whereNotNull('payload')
            ->whereRaw("JSON_EXTRACT(payload, '$.number') IS NOT NULL")
            ->when(
                filled($repositoryId),
                static fn ($query) => $query->where('repository_id', $repositoryId)
            )
            ->orderBy('id');

        $scanned = 0;
        $mismatched = 0;
        $repaired = 0;
        $skipped = 0;
        $conflicts = 0;

        $this->info($dryRun ? 'Running in dry-run mode.' : 'Applying repairs.');

        $query->chunk($chunkSize, function ($refs) use (
            &$scanned,
            &$mismatched,
            &$repaired,
            &$skipped,
            &$conflicts,
            $dryRun
        ): void {
            foreach ($refs as $ref) {
                $scanned++;

                $payloadNumber = $this->extractPayloadNumber($ref->payload);

                if ($payloadNumber === null) {
                    $skipped++;
                    continue;
                }

                if ((int) $ref->number === $payloadNumber) {
                    continue;
                }

                $mismatched++;

                $conflictingRefExists = IssueExternalRef::query()
                    ->where('repository_id', $ref->repository_id)
                    ->where('number', $payloadNumber)
                    ->where('id', '!=', $ref->id)
                    ->exists();

                if ($conflictingRefExists) {
                    $conflicts++;

                    $this->warn(sprintf(
                        'Conflict for ref %s in repository %s: current=%d payload=%d',
                        $ref->id,
                        $ref->repository_id,
                        (int) $ref->number,
                        $payloadNumber
                    ));

                    continue;
                }

                $this->line(sprintf(
                    '%s ref %s repo=%s issue=%s external_issue_id=%s %d -> %d',
                    $dryRun ? '[DRY-RUN]' : '[REPAIR]',
                    $ref->id,
                    $ref->repository_id,
                    $ref->issue_id,
                    $ref->external_issue_id,
                    (int) $ref->number,
                    $payloadNumber
                ));

                if ($dryRun) {
                    continue;
                }

                DB::transaction(function () use ($ref, $payloadNumber, &$repaired): void {
                    $lockedRef = IssueExternalRef::query()
                        ->whereKey($ref->id)
                        ->lockForUpdate()
                        ->first();

                    if (! $lockedRef instanceof IssueExternalRef) {
                        return;
                    }

                    $currentPayloadNumber = $this->extractPayloadNumber($lockedRef->payload);

                    if ($currentPayloadNumber === null) {
                        return;
                    }

                    if ((int) $lockedRef->number === $currentPayloadNumber) {
                        return;
                    }

                    $conflictingRefExists = IssueExternalRef::query()
                        ->where('repository_id', $lockedRef->repository_id)
                        ->where('number', $currentPayloadNumber)
                        ->where('id', '!=', $lockedRef->id)
                        ->lockForUpdate()
                        ->exists();

                    if ($conflictingRefExists) {
                        return;
                    }

                    $lockedRef->number = $currentPayloadNumber;
                    $lockedRef->save();

                    $repaired++;
                });
            }
        });

        $this->newLine();
        $this->table(
            ['Scanned', 'Mismatched', 'Repaired', 'Skipped', 'Conflicts'],
            [[
                $scanned,
                $mismatched,
                $repaired,
                $skipped,
                $conflicts,
            ]]
        );

        if ($dryRun) {
            $this->comment('No database changes were made.');
        }

        if ($conflicts > 0) {
            $this->warn('Some rows were not repaired because the target repository/number pair already exists.');
        }

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>|null  $payload
     */
    private function extractPayloadNumber(?array $payload): ?int
    {
        $number = data_get($payload, 'number');

        if (is_int($number)) {
            return $number > 0 ? $number : null;
        }

        if (is_string($number) && ctype_digit($number)) {
            $parsed = (int) $number;

            return $parsed > 0 ? $parsed : null;
        }

        return null;
    }
}
