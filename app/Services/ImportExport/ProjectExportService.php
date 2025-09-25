<?php

namespace App\Services\ImportExport;

use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use JsonException;
use ZipArchive;

final class ProjectExportService
{
    /**
     * @param array{include_attachments?:bool,scrub_pii?:bool,issue_state?:'all'|'open'|'done'} $options
     * @throws JsonException
     */
    public function export(Project $project, array $options = []): string
    {
        $opts = array_merge([
            'include_attachments' => false,
            'scrub_pii' => false,
            'issue_state' => 'all',
        ], $options);

        // --- Keys-as-slugs: prefer $project->key; fallback to slug or name
        $code = $project->key ?? $project->slug ?? Str::slug($project->name ?? 'project');
        // Ensure we never export a null external_id (even for legacy rows)
        if (empty($project->external_id)) {
            $project->forceFill(['external_id' => (string) Str::orderedUuid()])->saveQuietly();
        }

        $basename = sprintf('project-%s-%s.forgepkg', $code, now()->format('Ymd-His'));
        $relative = "exports/{$basename}";
        $abs      = storage_path("app/{$relative}");

        Storage::makeDirectory(dirname($relative));

        $zip = new ZipArchive();
        if ($zip->open($abs, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Unable to create export archive.');
        }

        // --- manifest.json: include key
        $zip->addFromString('manifest.json', json_encode([
            'schema_version' => '1.0',
            'exported_at'    => now()->toIso8601String(),
            'project'        => [
                'id'          => (string) $project->getKey(),
                'key'         => $project->key,
                'slug'        => $project->slug ?? null, // optional
                'external_id' => $project->external_id,
            ],
            'options' => $opts,
        ], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $zip->addEmptyDir('data');

        // --- data/project.json
        $zip->addFromString('data/project.json', json_encode([
            'id'          => (string) $project->getKey(),
            'external_id' => $project->external_id,
            'key'         => $project->key,
            'slug'        => $project->slug ?? null,
            'name'        => $project->name,
            'description' => $project->description,
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));

        // --- data/issues.ndjson
        $buffer = '';
        $query = $project->issues()->with(['parent', 'assignee', 'reporter', 'tags', 'status', 'type', 'priority']);
        if ($opts['issue_state'] === 'open') {
            $query->whereNull('done_at');
        } elseif ($opts['issue_state'] === 'done') {
            $query->whereNotNull('done_at');
        }

        foreach ($query->cursor() as $issue) {
            // ensure issue external_id too (legacy rows)
            if (empty($issue->external_id)) {
                $issue->forceFill(['external_id' => (string) Str::orderedUuid()])->saveQuietly();
            }

            $buffer .= json_encode([
                    'external_id'         => $issue->external_id,
                    'key'                 => $issue->key ?? null,
                    'title'               => $issue->title,
                    'description'         => $opts['scrub_pii'] ? null : $issue->description,
                    'status_key'          => $issue->status?->key,
                    'type_key'            => $issue->type?->key,
                    'priority_key'        => $issue->priority?->key,
                    'parent_external_id'  => $issue->parent?->external_id,
                    'assignee_email'      => $issue->assignee?->email,
                    'reporter_email'      => $issue->reporter?->email,
                    'labels'              => $issue->tags->pluck('name')->values()->all(),
                    'created_at'          => optional($issue->created_at)->toIso8601String(),
                    'updated_at'          => optional($issue->updated_at)->toIso8601String(),
                    'done_at'             => optional($issue->done_at)->toIso8601String(),
                ], JSON_THROW_ON_ERROR) . "\n";
        }

        $zip->addFromString('data/issues.ndjson', $buffer);
        $zip->close();

        return $abs; // absolute path;
    }
}
