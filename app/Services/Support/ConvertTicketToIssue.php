<?php

namespace App\Services\Support;

use App\Models\Issue;
use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\TicketType;
use Illuminate\Database\DatabaseManager;
use RuntimeException;
use Throwable;

final class ConvertTicketToIssue
{
    public function __construct(private DatabaseManager $db)
    {
    }

    /**
     * @throws Throwable
     */
    public function convert(Ticket $ticket, ?Project $project = null): Issue
    {
        $project = $project ?? $ticket->project ?? $ticket->product?->defaultProject;
        if ($project === null) {
            throw new RuntimeException('No project could be resolved for this ticket.');
        }

        $statusId   = (int) IssueStatus::query()->where('name', 'Backlog')->value('id') ?: (int) IssueStatus::query()->where('name', 'Open')->value('id');
        $typeId     = (int) IssueType::query()->where('name', 'Bug')->value('id');
        $priorityId = (int) IssuePriority::query()->orderByDesc('weight')->value('id');

        return $this->db->transaction(function () use ($ticket, $project, $statusId, $priorityId, $typeId): Issue {
            $issue = Issue::query()->create([
                'project_id'  => $project->getKey(),
                'summary'     => $ticket->subject,
                'description' => $ticket->body,
                'reporter_id' => auth()->id(),
                'issue_status_id'   => $statusId,
                'issue_priority_id' => $priorityId,
                'issue_type_id'     => $typeId,
            ]);

            $ticket->issues()->syncWithoutDetaching([$issue->getKey()]);
            $ticket->update(['status_id' => (int) TicketStatus::query()->where('name', 'Waiting on Support')->value('id')]);

            return $issue;
        });
    }
}
