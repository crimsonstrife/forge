<?php

namespace App\Listeners\Issues;

use App\Domain\Issues\Events\IssueAssigneeChanged;
use App\Models\Issue;
use App\Models\User;
use App\Notifications\IssueAssigned;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Route;

/**
 * Sends IssueAssigned (DB + Mail + Broadcast) to the new assignee.
 */
final class SendIssueAssignedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /** Ensure the listener runs after the DB transaction commits. */
    public bool $afterCommit = true;

    public function handle(IssueAssigneeChanged $event): void
    {
        /** @var Issue|null $issue */
        $issue = Issue::query()
            ->select(['id', 'summary', 'project_id'])
            ->find($event->issueId);

        /** @var User|null $user */
        $user = User::query()
            ->select(['id', 'name', 'email'])
            ->find($event->newAssigneeId);

        if (! $issue || ! $user) {
            return;
        }

        $url = Route::has('issues.show')
            ? route('issues.show', ['project' => $issue->project, 'issue' => $issue])
            : url('projects' . $issue->project()->id . '/issues/' . $issue->getKey());

        $user->notify(new IssueAssigned(issue: $issue, url: $url));
    }
}
