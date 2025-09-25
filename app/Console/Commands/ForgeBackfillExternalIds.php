<?php

namespace App\Console\Commands;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\IssueLink;
use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Project;
use App\Models\TimeEntry;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Tags\Tag;

class ForgeBackfillExternalIds extends Command
{
    protected $signature = 'forge:backfill-external-ids {--chunk=500}';
    protected $description = 'Assign external_id UUIDs to legacy rows missing them';

    /** @var array<class-string<Model>> */
    private array $models = [
        Project::class,
        Issue::class,
        Comment::class,
        IssueLink::class,
        TimeEntry::class,
        Media::class,
        Tag::class,
        IssueStatus::class,
        IssueType::class,
        IssuePriority::class,
    ];

    public function handle(): int
    {
        $chunk = (int) $this->option('chunk');

        foreach ($this->models as $modelClass) {
            /** @var Model $m */
            $m = new $modelClass();
            if (! $m->getConnection()->getSchemaBuilder()->hasColumn($m->getTable(), 'external_id')) {
                $this->warn("Skipping {$modelClass}: no external_id column.");
                continue;
            }

            $this->info("Backfilling {$modelClass}...");
            $modelClass::query()
                ->whereNull('external_id')
                ->orderBy($m->getKeyName())
                ->chunkById($chunk, function ($rows) use ($modelClass) {
                    foreach ($rows as $row) {
                        $row->external_id = (string) Str::orderedUuid();
                        $row->saveQuietly();
                    }
                }, $m->getKeyName());
        }

        $this->info('Done.');
        return self::SUCCESS;
    }
}
