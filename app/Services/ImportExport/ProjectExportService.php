<?php

namespace App\Services\ImportExport;

use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

/**
 * Creates a .forgepkg archive for a project.
 */
final class ProjectExportService
{
    /**
     * @param array{include_attachments?:bool,scrub_pii?:bool,issue_state?:'all'|'open'|'done'} $options
     * @throws \JsonException
     */
    public function export(Project $project, array $options = []): string
    {
        $opts = array_merge([
            'include_attachments' => false,
            'scrub_pii' => false,
            'issue_state' => 'all',
        ], $options);

        $basename = sprintf('project-%s-%s.forgepkg', $project->slug, now()->format('Ymd-His'));
        $path = "exports/{$basename}";
        $abs  = storage_path("app/{$path}");

        if (!mkdir($concurrentDirectory = dirname($abs), 0775, true) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }

        $zip = new ZipArchive();
        if ($zip->open($abs, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Unable to create export archive.');
        }

        $zip->addFromString('manifest.json', json_encode([
            'schema_version' => '1.0',
            'exported_at' => now()->toIso8601String(),
            'project' => [
                'slug' => $project->slug,
                'external_id' => $project->external_id,
            ],
            'options' => $opts,
        ], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));

        $zip->addEmptyDir('data');

        // Minimal: project + issues (expand later)
        $zip->addFromString('data/project.json', json_encode([
            'external_id' => $project->external_id,
            'slug' => $project->slug,
            'name' => $project->name,
            'description' => $project->description,
        ], JSON_THROW_ON_ERROR));

        // issues.ndjson
        $buffer = '';
        $query = $project->issues()->with(['parent', 'assignee', 'reporter', 'tags']);
        if ($opts['issue_state'] === 'open') {
            $query->whereNull('done_at');
        } elseif ($opts['issue_state'] === 'done') {
            $query->whereNotNull('done_at');
        }
        foreach ($query->cursor() as $issue) {
            $buffer .= json_encode([
                    'external_id' => $issue->external_id,
                    'key' => $issue->key ?? null,
                    'title' => $issue->title,
                    'description' => $opts['scrub_pii'] ? null : $issue->description,
                    'status_slug' => $issue->status?->slug,
                    'type_slug' => $issue->type?->slug,
                    'priority_slug' => $issue->priority?->slug,
                    'parent_external_id' => $issue->parent?->external_id,
                    'assignee_email' => $issue->assignee?->email,
                    'reporter_email' => $issue->reporter?->email,
                    'labels' => $issue->tags->pluck('name')->values()->all(),
                    'created_at' => $issue->created_at?->toIso8601String(),
                    'updated_at' => $issue->updated_at?->toIso8601String(),
                    'done_at' => $issue->done_at?->toIso8601String(),
                ], JSON_THROW_ON_ERROR) ."\n";
        }
        $zip->addFromString('data/issues.ndjson', $buffer);

        $zip->close();

        return $abs; // absolute path used by job/controller
    }
}
