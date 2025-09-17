<?php

namespace App\Livewire\Staff\Support\Products;

use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Project;
use App\Models\ServiceProduct;
use App\Models\ServiceProductPriorityMap;
use App\Models\ServiceProductStatusMap;
use App\Models\ServiceProductTypeMap;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketType;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    use AuthorizesRequests;

    public ServiceProduct $product;

    #[Validate('required|string|max:32')]
    public string $key = '';

    #[Validate('required|string|max:120')]
    public string $name = '';

    #[Validate('nullable|string|max:2000')]
    public ?string $description = null;

    public ?string $defaultProjectId = null;

    /** @var array<string> */
    public array $projectIds = [];

    /** @var array<int,int|null> ticket_type_id => issue_type_id */
    public array $typeMap = [];

    /** @var array<int,int|null> ticket_status_id => issue_status_id */
    public array $statusMap = [];

    /** @var array<int,int|null> ticket_priority_id => issue_priority_id */
    public array $priorityMap = [];

    public function mount(string $productId): void
    {
        $this->product = ServiceProduct::query()->with(['projects:id,name', 'defaultProject:id,name'])->findOrFail($productId);
        $this->authorize('support.manage');

        $this->key = $this->product->key;
        $this->name = $this->product->name;
        $this->description = $this->product->description;
        $this->defaultProjectId = $this->product->default_project_id;
        $this->projectIds = $this->product->projects()->pluck('projects.id')->map(fn($id) => (string)$id)->all();

        // preload maps
        $this->typeMap = $this->product->typeMaps()->pluck('issue_type_id','ticket_type_id')->map(fn($v)=> (int)$v)->all();
        $this->statusMap = $this->product->statusMaps()->pluck('issue_status_id','ticket_status_id')->map(fn($v)=> (int)$v)->all();
        $this->priorityMap = $this->product->priorityMaps()->pluck('issue_priority_id','ticket_priority_id')->map(fn($v)=> (int)$v)->all();
    }

    public function save(): void
    {
        $this->authorize('support.manage');
        $this->validate();

        $this->product->update([
            'key' => $this->key,
            'name' => $this->name,
            'description' => $this->description,
            'default_project_id' => $this->defaultProjectId,
        ]);

        $this->product->projects()->sync($this->projectIds);

        // upsert type maps
        foreach (TicketType::query()->pluck('id') as $ticketTypeId) {
            $issueTypeId = $this->typeMap[(int)$ticketTypeId] ?? null;
            ServiceProductTypeMap::query()->updateOrCreate(
                ['service_product_id' => $this->product->getKey(), 'ticket_type_id' => (int)$ticketTypeId],
                ['issue_type_id' => $issueTypeId]
            );
        }

        // upsert status maps
        foreach (TicketStatus::query()->pluck('id') as $ticketStatusId) {
            $issueStatusId = $this->statusMap[(int)$ticketStatusId] ?? null;
            ServiceProductStatusMap::query()->updateOrCreate(
                ['service_product_id' => $this->product->getKey(), 'ticket_status_id' => (int)$ticketStatusId],
                ['issue_status_id' => $issueStatusId]
            );
        }

        // upsert priority maps
        foreach (TicketPriority::query()->pluck('id') as $ticketPriorityId) {
            $issuePriorityId = $this->priorityMap[(int)$ticketPriorityId] ?? null;
            ServiceProductPriorityMap::query()->updateOrCreate(
                ['service_product_id' => $this->product->getKey(), 'ticket_priority_id' => (int)$ticketPriorityId],
                ['issue_priority_id' => $issuePriorityId]
            );
        }

        $this->dispatch('saved');
        $this->product->refresh();
    }

    public function render(): View
    {
        return view('livewire.staff.support.products.edit', [
            'allProjects'    => Project::query()->orderBy('name')->get(['id','name','key']),
            'ticketTypes'    => TicketType::query()->orderBy('name')->get(['id','name']),
            'ticketStatuses' => TicketStatus::query()->orderBy('name')->get(['id','name']),
            'ticketPriorities'=> TicketPriority::query()->orderBy('weight')->get(['id','name']),
            'issueTypes'     => IssueType::query()->orderBy('name')->get(['id','name']),
            'issueStatuses'  => IssueStatus::query()->orderBy('order')->get(['id','name']),
            'issuePriorities'=> IssuePriority::query()->orderBy('weight')->get(['id','name']),
        ]);
    }
}
