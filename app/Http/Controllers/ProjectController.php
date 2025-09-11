<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\Deletion\ProjectDeletionService;
use Illuminate\Http\RedirectResponse;

class ProjectController extends Controller
{
    public function __construct(
        private ProjectDeletionService $deleter,
    ) {
    }

    public function __invoke(Project $project): RedirectResponse
    {
        $this->authorize('delete', $project);

        $this->deleter->delete($project);

        return redirect()
            ->route('dashboard')
            ->with('status', __('Project deleted.'));
    }
}
