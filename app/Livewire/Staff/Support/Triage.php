<?php

namespace App\Livewire\Staff\Support;

use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketType;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Staff triage list.
 *
 * @property-read Paginator<array-key,Ticket> $rows
 */
final class Triage extends Component
{
    use AuthorizesRequests;
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public ?int $statusId = null;

    #[Url]
    public ?int $priorityId = null;

    #[Url]
    public ?int $typeId = null;

    public function mount(): void
    {
        $this->authorize('viewAny', Ticket::class);
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatusId(): void { $this->resetPage(); }
    public function updatingPriorityId(): void { $this->resetPage(); }
    public function updatingTypeId(): void { $this->resetPage(); }

    public function getRowsProperty(): LengthAwarePaginator
    {
        return Ticket::query()
            ->with(['status:id,name', 'priority:id,name', 'type:id,name', 'assignee:id,name', 'product:id,name'])
            ->when($this->search !== '', function ($q): void {
                $q->where(function ($w): void {
                    $w->where('key', 'like', '%' . $this->search . '%')
                        ->orWhere('subject', 'like', '%' . $this->search . '%')
                        ->orWhere('submitter_email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusId, fn($q) => $q->where('status_id', $this->statusId))
            ->when($this->priorityId, fn($q) => $q->where('priority_id', $this->priorityId))
            ->when($this->typeId, fn($q) => $q->where('type_id', $this->typeId))
            ->latest()
            ->paginate(20);
    }

    public function render(): View
    {
        return view('livewire.staff.support.triage', [
            'statuses'  => TicketStatus::query()->orderBy('name')->get(['id','name']),
            'priorities'=> TicketPriority::query()->orderBy('weight')->get(['id','name']),
            'types'     => TicketType::query()->orderBy('name')->get(['id','name']),
            'rows'      => $this->rows,
        ]);
    }
}
