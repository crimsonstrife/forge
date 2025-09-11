<?php

namespace App\Services\Deletion;

use App\Exceptions\CannotDeleteWithOpenChildren;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Throwable;

final class ProjectDeletionService
{
    public function __construct(
        private IssueDeletionService $issueDeletion,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function delete(Project $project): void
    {
        DB::transaction(function () use ($project): void {
            // Block if ANY issue in the project is NOT done
            $hasOpenIssues = $project->issues()
                ->whereHas('status', fn ($q) => $q->where('is_done', false))
                ->exists();

            if ($hasOpenIssues) {
                $label = $project->key ?? $project->name ?? (string) $project->getKey();
                throw CannotDeleteWithOpenChildren::forProject($label);
            }

            // Delete all issues (which — by guard — are done, and must have no open children)
            $project->issues()
                ->with(['status:id,is_done', 'children.status:id,is_done'])
                ->get()
                ->each(fn ($issue) => $this->issueDeletion->delete($issue));

            // Delete related repositories (local DB records)
            if (method_exists($project, 'repositories')) {
                $project->repositories()->delete();
            }

            // Finally delete the Project
            $project->delete();
        });
    }
}
