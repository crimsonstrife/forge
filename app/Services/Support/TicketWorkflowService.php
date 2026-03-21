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
        /** @var ServiceProduct|null $product */
        $product = $this->resolveWorkflowProduct($ticket, [
            'default_project_id',
            'first_response_target_minutes',
            'resolve_target_minutes',
        ]);
        $updates = [];
        $projectWasUpdated = false;

        if ($product instanceof ServiceProduct) {
            $product->loadMissing(['defaultProject', 'autoCreateIssueProject']);
            $ticket->setRelation('product', $product);

            if ($ticket->project_id === null && $product->default_project_id !== null) {
                $updates['project_id'] = $product->default_project_id;
                $projectWasUpdated = true;
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

        if ($projectWasUpdated) {
            $ticket->unsetRelation('project');
        }

        $ticket->loadMissing('project');

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
        $product = $this->resolveWorkflowProduct($ticket, ['next_response_target_minutes']);
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
        $product = $this->resolveWorkflowProduct($ticket, ['resolve_target_minutes']);
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
        /** @var ServiceProduct|null $product */
        $product = $this->resolveWorkflowProduct($ticket, [
            'default_project_id',
            'auto_create_issue_for_ticket_type_id',
            'auto_create_issue_project_id',
        ]);
        if ($product === null) {
            return null;
        }

        $product->loadMissing(['defaultProject', 'autoCreateIssueProject']);
        $ticket->setRelation('product', $product);

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

        $ticket->setRelation('project', $project);

        return $this->converter->convertIfUnlinked($ticket, $project, moveTicketToWaitingOnSupport: false);
    }

    private function dueAt(CarbonInterface $from, int $minutes): CarbonImmutable
    {
        return CarbonImmutable::instance($from)->addMinutes($minutes);
    }

    /**
     * Refresh a partially eager-loaded product before reading workflow configuration.
     *
     * @param  array<int, string>  $requiredAttributes
     */
    private function resolveWorkflowProduct(Ticket $ticket, array $requiredAttributes): ?ServiceProduct
    {
        /** @var ServiceProduct|null $product */
        $product = $ticket->relationLoaded('product')
            ? $ticket->getRelation('product')
            : null;

        if ($product instanceof ServiceProduct && $this->productHasAttributes($product, $requiredAttributes)) {
            return $product;
        }

        $product = $ticket->product()->first();

        if (! $product instanceof ServiceProduct) {
            return null;
        }

        $ticket->setRelation('product', $product);

        return $product;
    }

    /**
     * @param  array<int, string>  $requiredAttributes
     */
    private function productHasAttributes(ServiceProduct $product, array $requiredAttributes): bool
    {
        $attributes = $product->getAttributes();

        foreach ($requiredAttributes as $attribute) {
            if (! array_key_exists($attribute, $attributes)) {
                return false;
            }
        }

        return true;
    }
}
