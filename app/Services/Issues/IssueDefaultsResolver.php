<?php

namespace App\Services\Issues;

use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Resolves default IDs for new Issues, per project if possible.
 */
final class IssueDefaultsResolver
{
    public function __construct(private Project $project)
    {
    }

    public static function for(Project $project): self
    {
        return new self($project);
    }

    public function typeId(): string
    {
        // Prefer a common "task" type; otherwise first alphabetically.
        $id = IssueType::query()->where('key', 'task')->value('id')
            ?? IssueType::query()->orderBy('name')->value('id');

        if (! $id) {
            throw new RuntimeException('No Issue Types are configured.');
        }

        return (string) $id;
    }

    public function statusId(): ?string
    {
        // Try a project-enabled, non-done status first
        $q = IssueStatus::query()
            ->whereIn('id', DB::table('project_issue_statuses')
                ->select('issue_status_id')
                ->where('project_id', $this->project->id))
            ->where('is_done', false);

        $id = $q->orderBy('name')->value('id')
            ?? IssueStatus::query()->where('is_done', false)->orderBy('name')->value('id')
            ?? IssueStatus::query()->orderBy('name')->value('id'); // last resort

        return $id ? (string) $id : null;
    }

    public function priorityId(): ?string
    {
        // Pick the first by name as a stable default (e.g., "Medium")
        $id = IssuePriority::query()->orderBy('name')->value('id');

        return $id ? (string) $id : null;
    }
}
