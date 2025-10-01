<?php

namespace App\Http\Controllers;

use App\Http\Requests\Issues\TransitionIssueStatusRequest;
use App\Models\Issue;
use App\Models\Project;
use App\Services\Issues\IssueStatusTransitionService;
use Illuminate\Http\RedirectResponse;

final class TransitionStatusController
{
    public function __construct(
        private IssueStatusTransitionService $transitions
    ) {}

    public function __invoke(TransitionIssueStatusRequest $request, Project $project, Issue $issue): RedirectResponse
    {
        // Safety: ensure route-models are consistent
        if ($issue->project_id !== $project->id) {
            abort(404);
        }

        $to = (int) $request->input('to_status_id');

        if (!$this->transitions->canTransition($issue, $to)) {
            return back()->with('error', 'That transition is not allowed for this issue.');
        }

        $issue->issue_status_id = $to;
        $issue->save();

        return back()->with('success', 'Status updated.');
    }
}
