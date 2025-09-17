<?php

namespace App\Livewire\Staff\Support;

use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketType;
use App\Models\User;
use App\Services\Support\ConvertTicketToIssue;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class ShowTicket extends Component
{
    use AuthorizesRequests;

    public Ticket $ticket;

    #[Validate('required|string|min:2|max:5000')]
    public string $internalNote = '';

    #[Validate('required|string|min:2|max:5000')]
    public string $publicReply = '';

    public ?int $statusId = null;
    public ?int $priorityId = null;
    public ?int $typeId = null;
    public ?string $assigneeId = null;

    /** Project to use when converting to an Issue (and to store on the ticket) */
    public ?string $projectId = null;

    public function mount(string $key): void
    {
        $this->ticket = Ticket::query()
            ->with(['status:id,name', 'priority:id,name', 'type:id,name', 'assignee:id,name', 'product.defaultProject:id,name'])
            ->where('key', $key)
            ->firstOrFail();

        $this->authorize('view', $this->ticket);

        $this->statusId   = $this->ticket->status_id;
        $this->priorityId = $this->ticket->priority_id;
        $this->typeId     = $this->ticket->type_id;
        $this->assigneeId = $this->ticket->assigned_to_user_id;

        // Prefer the ticket's project; else fall back to the ServiceProduct's default project (if any)
        $this->projectId  = $this->ticket->project_id ?? $this->ticket->product?->default_project_id;
    }

    public function saveMeta(): void
    {
        $this->authorize('manage', $this->ticket);

        $this->ticket->update([
            'status_id'             => $this->statusId,
            'priority_id'           => $this->priorityId,
            'type_id'               => $this->typeId,
            'assigned_to_user_id'   => $this->assigneeId,
            'project_id'            => $this->projectId,
        ]);

        $this->ticket->refresh();
        $this->dispatch('saved');
    }

    public function addInternalNote(): void
    {
        $this->authorize('manage', $this->ticket);
        $this->validateOnly('internalNote');

        $this->ticket->comments()->create([
            'user_id'       => auth()->id(),
            'body'          => $this->internalNote,
            'redacted_body' => $this->internalNote,
            'is_internal'   => true,
        ]);

        $this->reset('internalNote');
        $this->dispatch('note-added');
    }

    public function addPublicReply(): void
    {
        $this->authorize('manage', $this->ticket);
        $this->validateOnly('publicReply');

        $this->ticket->comments()->create([
            'user_id'       => auth()->id(),
            'body'          => $this->publicReply,
            'redacted_body' => $this->publicReply,
            'is_internal'   => false,
        ]);

        // TODO: notify customer

        $this->reset('publicReply');
        $this->dispatch('reply-added');
    }

    public function convertToIssue(ConvertTicketToIssue $converter): void
    {
        $this->authorize('convertToIssue', $this->ticket);

        // Resolve the project in priority order: selected → ticket.project → product.defaultProject
        $project = null;
        if ($this->projectId) {
            $project = Project::query()->find($this->projectId);
        }
        $project = $project ?? $this->ticket->project ?? $this->ticket->product?->defaultProject;

        if ($project === null) {
            // Guard-rail: require a project before converting
            $this->addError('projectId', 'Please select a project before converting to an Issue.');
            return;
        }

        $issue = $converter->convert($this->ticket, $project);

        $this->dispatch('converted', key: $issue->key ?? (string) $issue->getKey());
    }

    public function render(): View
    {
        $internal = $this->ticket->comments()
            ->where('is_internal', true)
            ->with('user:id,name,email,profile_photo_path')
            ->latest()
            ->get(['id','body','created_at','user_id']); // reference $ticket for submitter data

        $public = $this->ticket->comments()
            ->where('is_internal', false)
            ->with('user:id,name,email,profile_photo_path')
            ->latest()
            ->get(['id','body','created_at','user_id']);

        $relatedIssues = $this->ticket->issues()
            ->select(['id','key','summary','project_id'])
            ->with('project:id,name,key')
            ->get();

        return view('livewire.staff.support.show-ticket', [
            'statuses'   => TicketStatus::query()->orderBy('name')->get(['id','name']),
            'priorities' => TicketPriority::query()->orderBy('weight')->get(['id','name']),
            'types'      => TicketType::query()->orderBy('name')->get(['id','name']),
            'assignees'  => User::query()->orderBy('name')->get(['id','name']),
            'projects'   => Project::query()->orderBy('name')->get(['id','name','key']),
            'internal' => $internal,
            'public'   => $public,
            'relatedIssues' => $relatedIssues,
        ]);
    }
}
