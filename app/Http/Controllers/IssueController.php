<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Project;
use App\Services\Deletion\IssueDeletionService;
use Illuminate\Http\RedirectResponse;

class IssueController extends Controller
{
    public function __construct(
        private IssueDeletionService $deleter,
    ) {
    }

    public function __invoke(Project $project, Issue $issue): RedirectResponse
    {
        $this->authorize('delete', $issue);

        // Optional: ensure the issue belongs to the given project
        if ((int) $issue->project_id !== (int) $project->getKey()) {
            abort(404);
        }

        $this->deleter->delete($issue);

        return redirect()
            ->route('projects.show', $project)
            ->with('status', __('Issue deleted.'));
    }
}
