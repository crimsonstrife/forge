<?php

namespace App\Listeners\Issues;

use App\Domain\Issues\Events\IssueAssigneeChanged;
use App\Models\Issue;
use App\Models\User;
use App\Notifications\IssueAssigned;
use App\Services\Issues\IssueCollaborationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/**
 * Sends IssueAssigned (DB + Mail + Broadcast) to the new assignee.
 */
final class SendIssueAssignedNotification
{
    public function handle(IssueAssigneeChanged $event): void
    {
        $collaboration = app(IssueCollaborationService::class);

        // Self-assignment: don't notify the actor about their own action
        if ($event->actorId !== null && (string) $event->actorId === (string) $event->newAssigneeId) {
            return;
        }

        $issue = Issue::query()->select(['id','key','summary','project_id'])->find($event->issueId);
        $user  = User::query()->select(['id','name','email'])->find($event->newAssigneeId);
        if (! $issue || ! $user) {
            return;
        }

        $collaboration->follow($issue, $user);

        if (! $collaboration->preferences($user)->enabledFor('assignment')) {
            return;
        }

        // ---- DEDUPE: same issue to same user, unread, very recent
        $query = $user->notifications()
            ->where('type', IssueAssigned::class)
            ->whereNull('read_at')
            ->where('created_at', '>=', now()->subMinutes(2));

        // JSON filter by driver
        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            $query->whereRaw("data->>'issue_id' = ?", [(string) $issue->getKey()]);
        } elseif ($driver === 'mysql') {
            $query->where('data->issue_id', (string) $issue->getKey());
        } else {
            // fallback: simple LIKE (SQLite/dev)
            $needle = '"issue_id":"' . $issue->getKey() . '"';
            // Escape %, _, and \ for LIKE pattern
            $escapedNeedle = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $needle);
            $likePattern = '%' . $escapedNeedle . '%';
            $query->whereRaw("data LIKE ? ESCAPE '\\'", [$likePattern]);
        }

        if ($query->exists()) {
            return; // skip duplicate
        }
        // ---- /DEDUPE

        $url = Route::has('issues.show')
            ? route('issues.show', ['project' => $issue->project, 'issue' => $issue])
            : url('projects/' . $issue->project_id . '/issues/' . $issue->key);

        $user->notify(new IssueAssigned(issue: $issue, url: $url));
    }
}
