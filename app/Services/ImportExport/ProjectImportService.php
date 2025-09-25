<?php

namespace App\Services\ImportExport;

use App\Models\ImportExportRecord;
use App\Models\Issue;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use ZipArchive;

final class ProjectImportService
{
    /** @var array<string,string> old issue external_id => new issue PK (UUID string) */
    private array $issueIdMap = [];

    /**
     * @return string Project UUID
     * @throws \Throwable
     */
    public function import(string $archivePath, ImportExportRecord $record): string
    {
        $zip = new ZipArchive();
        if ($zip->open($archivePath) !== true) {
            throw new RuntimeException('Cannot open archive.');
        }

        $manifest = json_decode($zip->getFromName('manifest.json') ?: '{}', true, 512, JSON_THROW_ON_ERROR);
        if (($manifest['schema_version'] ?? null) !== '1.0') {
            throw new RuntimeException('Unsupported schema.');
        }

        $projectData = json_decode($zip->getFromName('data/project.json') ?: '{}', true, 512, JSON_THROW_ON_ERROR);

        return DB::transaction(function () use ($zip, $projectData, $record): string {
            // Keys-as-slugs: prefer incoming key; fallback to slug or name
            $incomingKey  = $projectData['key']  ?? null;
            $incomingSlug = $projectData['slug'] ?? null;
            $incomingName = $projectData['name'] ?? 'Imported Project';

            $code = $incomingKey ?? $incomingSlug ?? Str::slug($incomingName) ?? 'project';
            $uniqueKey = $code;
            $i = 1;
            while (Project::query()->where('key', $uniqueKey)->exists()) {
                $uniqueKey = $code . '-import-' . $i++;
            }

            $project = Project::query()->create([
                'name'        => $incomingName,
                'key'         => $uniqueKey, // <-- use key column in your schema
                'description' => $projectData['description'] ?? null,
            ]);

            // Issues pass 1: create without parents
            $raw   = (string) $zip->getFromName('data/issues.ndjson');
            $lines = preg_split('/\R/', $raw) ?: [];
            $rows  = array_values(array_filter($lines, static fn ($l) => trim((string) $l) !== ''));

            foreach ($rows as $line) {
                /** @var array<string,mixed> $row */
                $row = json_decode($line, true, 512, JSON_THROW_ON_ERROR);

                $issue = $project->issues()->create([
                    'title'       => $row['title'] ?? 'Untitled',
                    'description' => $row['description'] ?? null,
                    'external_id' => $row['external_id'] ?? (string) Str::orderedUuid(),
                    // TODO: map status/type/priority by slug; map assignee by email; attach labels
                ]);

                if (! empty($row['external_id'])) {
                    $this->issueIdMap[$row['external_id']] = (string) $issue->getKey();
                }
            }

            // Issues pass 2: attach parents
            foreach ($rows as $line) {
                $row = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
                $parentExt = $row['parent_external_id'] ?? null;
                if (! $parentExt) {
                    continue;
                }
                $childId  = $this->issueIdMap[$row['external_id']] ?? null;
                $parentId = $this->issueIdMap[$parentExt] ?? null;
                if ($childId && $parentId) {
                    Issue::query()->whereKey($childId)->update(['parent_id' => $parentId]);
                }
            }

            $record->update([
                'project_id' => (string) $project->getKey(),
                'status'     => 'success',
                'report'     => ['warnings' => []],
            ]);

            return (string) $project->getKey();
        });
    }
}
