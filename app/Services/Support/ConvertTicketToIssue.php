<?php

namespace App\Services\Support;

use App\Models\Issue;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketStatus;
use Illuminate\Database\DatabaseManager;
use RuntimeException;

final class ConvertTicketToIssue
{
    public function __construct(private DatabaseManager $db) {}

    public function convert(Ticket $ticket, ?Project $project = null, bool $moveTicketToWaitingOnSupport = true): Issue
    {
        return $this->db->transaction(
            fn (): Issue => $this->persistConversion($ticket, $project, $moveTicketToWaitingOnSupport)
        );
    }

    public function convertIfUnlinked(Ticket $ticket, ?Project $project = null, bool $moveTicketToWaitingOnSupport = true): ?Issue
    {
        return $this->db->transaction(function () use ($ticket, $project, $moveTicketToWaitingOnSupport): ?Issue {
            $lockedTicket = Ticket::query()
                ->with(['product.defaultProject', 'project'])
                ->lockForUpdate()
                ->findOrFail($ticket->getKey());

            if ($lockedTicket->issues()->exists()) {
                return null;
            }

            return $this->persistConversion($lockedTicket, $project, $moveTicketToWaitingOnSupport);
        });
    }

    private function persistConversion(Ticket $ticket, ?Project $project, bool $moveTicketToWaitingOnSupport): Issue
    {
        $project = $project ?? $ticket->project ?? $ticket->product?->defaultProject;
        if ($project === null) {
            throw new RuntimeException('No project could be resolved for this ticket.');
        }

        $product = $ticket->product;

        // Resolve mapped attributes with safe fallbacks
        $typeId = $product?->resolveIssueTypeId((int) $ticket->type_id, $project) ?? $project->defaultTypeId();
        $priorityId = $product?->resolveIssuePriorityId((int) $ticket->priority_id, $project) ?? $project->defaultPriorityId();
        $statusId = $product?->resolveIssueStatusId((int) $ticket->status_id, $project) ?? $project->initialStatusId();

        $issue = Issue::query()->create([
            'project_id' => $project->getKey(),
            'summary' => $ticket->subject,
            'description' => $ticket->body,
            'reporter_id' => auth()->id(),
            'issue_status_id' => $statusId,
            'issue_priority_id' => $priorityId,
            'issue_type_id' => $typeId,
        ]);

        $ticket->issues()->syncWithoutDetaching([$issue->getKey()]);

        if ($moveTicketToWaitingOnSupport) {
            $ticket->update([
                'status_id' => (int) TicketStatus::query()
                    ->where('name', 'Waiting on Support')
                    ->value('id'),
            ]);
        }

        return $issue;
    }
}
