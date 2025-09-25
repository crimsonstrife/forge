<?php

namespace App\Services\ImportExport;

use App\Models\ImportExportRecord;
use App\Models\Issue;
use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Project;
use App\Models\User;
use App\Support\Keys\ProjectKeyGenerator;
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
    public function import(string $archivePath, ImportExportRecord $record, ?string $leadUserId = null): string
    {
        if (! is_file($archivePath)) {
            throw new RuntimeException("Archive not found at path: {$archivePath}");
        }
        if (! is_readable($archivePath)) {
            throw new RuntimeException("Archive not readable: {$archivePath}");
        }

        $zip = new ZipArchive();
        $openRes = $zip->open($archivePath);
        if ($openRes !== true) {
            throw new RuntimeException(
                "Cannot open archive ({$this->explainZipError((int) $openRes)}): {$archivePath}"
            );
        }

        $manifest = json_decode($zip->getFromName('manifest.json') ?: '{}', true, 512, JSON_THROW_ON_ERROR);
        if (($manifest['schema_version'] ?? null) !== '1.0') {
            throw new RuntimeException('Unsupported schema.');
        }

        $projectData = json_decode($zip->getFromName('data/project.json') ?: '{}', true, 512, JSON_THROW_ON_ERROR);

        return DB::transaction(function () use ($zip, $projectData, $record, $leadUserId): string {
            $incomingKey  = $projectData['key']  ?? null;
            $incomingSlug = $projectData['slug'] ?? null;
            $incomingName = $projectData['name'] ?? 'Imported Project';

            $allTypes     = IssueType::query()->get(['id','key'])->keyBy(fn ($t) => strtoupper($t->key));
            $defaultType  = IssueType::query()->where('is_default', true)->value('id')
                ?? $allTypes->first()?->id;

            $allStatuses  = IssueStatus::query()->get(['id','key','is_done','order'])->keyBy(fn ($s) => strtoupper($s->key));
            $defaultStatus = IssueStatus::query()->orderBy('order')->value('id') ?? $allStatuses->first()?->id;

            $allPriorities = IssuePriority::query()->get(['id','key','order'])->keyBy(fn ($p) => strtoupper($p->key));
            $defaultPriority = IssuePriority::query()->orderBy('order')->value('id') ?? $allPriorities->first()?->id;

            // cache users by email to avoid N+1
            $userByEmail = User::query()->get(['id','email'])
                ->keyBy(fn ($u) => strtolower($u->email));

            $seed = $incomingKey ?? $incomingSlug ?? $incomingName;

            $maxLen = defined(Project::class.'::KEY_MAX_LENGTH')
                ? Project::KEY_MAX_LENGTH
                : 8;

            /** @var ProjectKeyGenerator $keys */
            $keys = app(ProjectKeyGenerator::class);
            $uniqueKey = $keys->uniqueFromSeed($seed, $maxLen);

            // pick the lead: explicit > initiator > first user
            $lead = $leadUserId
                ?? $record->initiator_id
                ?? User::query()->orderBy('created_at')->value('id');

            if (! $lead) {
                throw new RuntimeException('Unable to assign project lead: no users found.');
            }

            $project = Project::query()->create([
                'name'        => $incomingName,
                'key'         => $uniqueKey,
                'description' => $projectData['description'] ?? null,
                'lead_id'     => $lead,
            ]);

            // Issues pass 1: create without parents
            $raw   = (string) $zip->getFromName('data/issues.ndjson');
            $lines = preg_split('/\R/', $raw) ?: [];
            $rows  = array_values(array_filter($lines, static fn ($l) => trim((string) $l) !== ''));

            foreach ($rows as $line) {
                /** @var array<string,mixed> $row */
                $row = json_decode($line, true, 512, JSON_THROW_ON_ERROR);

                // --- resolve type/status/priority
                $typeKey     = strtoupper((string) ($row['type_key'] ?? $row['type_slug'] ?? ''));
                $statusKey   = strtoupper((string) ($row['status_key'] ?? $row['status_slug'] ?? ''));
                $priorityKey = strtoupper((string) ($row['priority_key'] ?? $row['priority_slug'] ?? ''));

                $typeId     = $typeKey     && $allTypes->has($typeKey)     ? $allTypes[$typeKey]->id     : $defaultType;
                $statusId   = $statusKey   && $allStatuses->has($statusKey) ? $allStatuses[$statusKey]->id : $defaultStatus;
                $priorityId = $priorityKey && $allPriorities->has($priorityKey) ? $allPriorities[$priorityKey]->id : $defaultPriority;

                if (! $typeId || ! $statusId || ! $priorityId) {
                    throw new RuntimeException('Missing default IssueType/Status/Priority. Seed baseline data first.');
                }

                // --- resolve reporter/assignee
                $reporterId = null;
                if (!empty($row['reporter_email'])) {
                    $reporterId = optional($userByEmail->get(strtolower($row['reporter_email'])))->id;
                }
                $assigneeId = null;
                if (!empty($row['assignee_email'])) {
                    $assigneeId = optional($userByEmail->get(strtolower($row['assignee_email'])))->id;
                }

                $issue = $project->issues()->create([
                    'title'       => $row['title'] ?? 'Untitled',
                    'description' => $row['description'] ?? null,
                    'external_id' => $row['external_id'] ?? (string) Str::orderedUuid(),
                    'issue_type_id'     => $typeId,
                    'issue_status_id'   => $statusId,
                    'issue_priority_id' => $priorityId,
                    'reporter_id'       => $reporterId,
                    'assignee_id'       => $assigneeId,
                ]);

                // attach labels/tags if present (spatie/tags)
                if (!empty($row['labels']) && is_array($row['labels'])) {
                    // defaults to 'tags' type and default locale.
                    $issue->syncTags($row['labels']);
                }

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


    private function explainZipError(int $code): string
    {
        return match ($code) {
            ZipArchive::ER_EXISTS   => 'File already exists',
            ZipArchive::ER_INCONS   => 'Zip archive inconsistent/corrupt',
            ZipArchive::ER_INVAL    => 'Invalid argument',
            ZipArchive::ER_MEMORY   => 'Malloc failure',
            ZipArchive::ER_NOENT    => 'No such file',
            ZipArchive::ER_NOZIP    => 'Not a zip archive',
            ZipArchive::ER_OPEN     => 'Can\'t open file',
            ZipArchive::ER_READ     => 'Read error',
            ZipArchive::ER_SEEK     => 'Seek error',
            default                 => 'Unknown error '.$code,
        };
    }
}
