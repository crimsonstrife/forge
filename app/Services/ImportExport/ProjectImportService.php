<?php

namespace App\Services\ImportExport;

use App\Models\ImportExportRecord;
use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use ZipArchive;

/**
 * Imports a .forgepkg into a NEW project.
 */
final class ProjectImportService
{
    /** @var array<string,string> old-ext-id => new issue UUID (string) */
    private array $issueIdMap = [];

    /**
     * @throws \Throwable
     */
    public function import(string $archivePath, ImportExportRecord $record): int
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
            // Create project (avoid slug collisions)
            $slug = $projectData['slug'] ?? 'imported-project';
            $baseSlug = $slug;
            $i = 1;
            while (Project::query()->where('slug', $slug)->exists()) {
                $slug = $baseSlug.'-import-'.$i++;
            }

            $project = Project::query()->create([
                'name' => $projectData['name'] ?? 'Imported Project',
                'slug' => $slug,
                'description' => $projectData['description'] ?? null,
            ]);

            // Issues pass 1: create without parents
            $lines = preg_split('/\r\n|\r|\n/', (string) $zip->getFromName('data/issues.ndjson'));
            $rows  = array_values(array_filter($lines, fn ($l) => (string) $l !== ''));

            foreach ($rows as $line) {
                /** @var array<string,mixed> $row */
                $row = json_decode($line, true, 512, JSON_THROW_ON_ERROR);

                $issue = $project->issues()->create([
                    'title' => $row['title'] ?? 'Untitled',
                    'description' => $row['description'] ?? null,
                    'external_id' => $row['external_id'] ?? null,
                    // TODO: map status/type/priority by slug; map assignee by email
                ]);

                if (! empty($row['external_id'])) {
                    $this->issueIdMap[$row['external_id']] = $issue->id;
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
                'project_id' => $project->id,
                'status' => 'success',
                'report' => ['warnings' => []],
            ]);

            return $project->id;
        });
    }
}
