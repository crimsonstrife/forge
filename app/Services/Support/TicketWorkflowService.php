<?php

namespace App\Services\Support;

use App\Models\Issue;
use App\Models\Project;
use App\Models\ServiceProduct;
use App\Models\Ticket;
use App\Models\TicketStatus;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

final class TicketWorkflowService
{
    public function __construct(private ConvertTicketToIssue $converter) {}

    public function initialize(Ticket $ticket): ?Issue
    {
        $product = $ticket->product()->first();
        $updates = [];

        if ($product instanceof ServiceProduct) {
            if ($ticket->project_id === null && $product->default_project_id !== null) {
                $updates['project_id'] = $product->default_project_id;
            }

            if ($ticket->first_response_due_at === null && $product->first_response_target_minutes !== null) {
                $updates['first_response_due_at'] = $this->dueAt(
                    $ticket->created_at ?? CarbonImmutable::now(),
                    (int) $product->first_response_target_minutes,
                );
            }

            if ($ticket->resolve_due_at === null && $product->resolve_target_minutes !== null) {
                $updates['resolve_due_at'] = $this->dueAt(
                    $ticket->created_at ?? CarbonImmutable::now(),
                    (int) $product->resolve_target_minutes,
                );
            }
        }

        if ($ticket->last_customer_reply_at === null) {
            $updates['last_customer_reply_at'] = $ticket->created_at ?? CarbonImmutable::now();
        }

        if ($updates !== []) {
            $ticket->forceFill($updates);
            $ticket->saveQuietly();
        }

        $ticket->unsetRelation('product');
        $ticket->loadMissing(['product.defaultProject', 'project']);

        return $this->maybeAutoCreateIssue($ticket);
    }

    public function recordStaffReply(Ticket $ticket): void
    {
        $now = CarbonImmutable::now();
        $updates = [
            'last_staff_reply_at' => $now,
            'next_response_due_at' => null,
        ];

        if ($ticket->first_responded_at === null) {
            $updates['first_responded_at'] = $now;
        }

        $ticket->forceFill($updates);
        $ticket->saveQuietly();
    }

    public function recordCustomerReply(Ticket $ticket): void
    {
        /** @var ServiceProduct|null $product */
        $product = $ticket->product ?? $ticket->product()->first();
        $now = CarbonImmutable::now();
        $updates = [
            'last_customer_reply_at' => $now,
        ];

        if ($ticket->first_responded_at !== null && $product?->next_response_target_minutes !== null) {
            $updates['next_response_due_at'] = $this->dueAt(
                $now,
                (int) $product->next_response_target_minutes,
            );
        }

        $ticket->forceFill($updates);
        $ticket->saveQuietly();
    }

    public function syncResolutionState(Ticket $ticket): void
    {
        /** @var TicketStatus|null $status */
        $status = $ticket->status ?? $ticket->status()->first();
        /** @var ServiceProduct|null $product */
        $product = $ticket->product ?? $ticket->product()->first();
        $now = CarbonImmutable::now();

        if ($status === null) {
            return;
        }

        $updates = [];

        if ($status->is_done) {
            if ($ticket->resolved_at === null) {
                $updates['resolved_at'] = $now;
            }

            $updates['next_response_due_at'] = null;
        } else {
            if ($ticket->resolved_at !== null) {
                $updates['resolved_at'] = null;
            }

            if ($ticket->resolve_due_at === null && $product?->resolve_target_minutes !== null) {
                $updates['resolve_due_at'] = $this->dueAt(
                    $ticket->created_at ?? CarbonImmutable::now(),
                    (int) $product->resolve_target_minutes,
                );
            }
        }

        if ($updates === []) {
            return;
        }

        $ticket->forceFill($updates);
        $ticket->saveQuietly();
    }

    public function maybeAutoCreateIssue(Ticket $ticket): ?Issue
    {
        if ($ticket->issues()->exists()) {
            return null;
        }

        /** @var ServiceProduct|null $product */
        $product = $ticket->product ?? $ticket->product()->first();
        if ($product === null) {
            return null;
        }

        if ((int) ($product->auto_create_issue_for_ticket_type_id ?? 0) !== (int) $ticket->type_id) {
            return null;
        }

        /** @var Project|null $project */
        $project = $product->autoCreateIssueProject
            ?? $ticket->project
            ?? $product->defaultProject;

        if ($project === null) {
            return null;
        }

        if ($ticket->project_id !== $project->getKey()) {
            $ticket->forceFill(['project_id' => $project->getKey()]);
            $ticket->saveQuietly();
        }

        return $this->converter->convert($ticket, $project, moveTicketToWaitingOnSupport: false);
    }

    private function dueAt(CarbonInterface $from, int $minutes): CarbonImmutable
    {
        return CarbonImmutable::instance($from)->addMinutes($minutes);
    }
}
