<?php

namespace App\Livewire\Staff\Support\Products;

use App\Models\Organization;
use App\Models\Project;
use App\Models\ServiceProduct;
use App\Models\TicketType;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    use AuthorizesRequests;

    #[Validate('required|string|max:32')]
    public string $key = '';

    #[Validate('required|string|max:120')]
    public string $name = '';

    #[Validate('nullable|string|max:2000')]
    public ?string $description = null;

    #[Validate('nullable|integer|min:1|max:43200')]
    public ?int $firstResponseTargetMinutes = null;

    #[Validate('nullable|integer|min:1|max:43200')]
    public ?int $nextResponseTargetMinutes = null;

    #[Validate('nullable|integer|min:1|max:43200')]
    public ?int $resolveTargetMinutes = null;

    #[Validate('nullable|integer|exists:ticket_types,id')]
    public ?int $autoCreateIssueForTicketTypeId = null;

    #[Validate('nullable|uuid|exists:projects,id')]
    public ?string $autoCreateIssueProjectId = null;

    /** Default routing target for new tickets */
    #[Validate('nullable|uuid|exists:projects,id')]
    public ?string $defaultProjectId = null;

    /** @var array<int,string> */
    #[Validate('array')]
    public array $projectIds = [];

    public function mount(): void
    {
        $this->authorize('support.manage'); // adapt to your gate/permission
    }

    public function save(): void
    {
        $this->authorize('support.manage');
        $this->validate(); // base rules first

        // Resolve organization_id:
        // 1) from chosen default project
        // 2) from the first selected associated project
        // 3) from current user
        // 4) fallback to the first organization
        $organizationId = null;

        if ($this->defaultProjectId) {
            $organizationId = (string) Project::query()->whereKey($this->defaultProjectId)->value('organization_id');
        }

        if ($organizationId === null && ! empty($this->projectIds)) {
            $organizationId = (string) Project::query()->whereKey($this->projectIds[0])->value('organization_id');
        }

        $organizationId = $organizationId
            ?? (string) auth()->user()?->organization_id
            ?? (string) Organization::query()->value('id');

        // Validate unique key within the organization
        $this->validate([
            'key' => [
                'required', 'string', 'max:32',
                Rule::unique('service_products', 'key')->where('organization_id', $organizationId),
            ],
        ]);

        $product = ServiceProduct::query()->create([
            'organization_id' => $organizationId,
            'key' => $this->key,
            'name' => $this->name,
            'description' => $this->description,
            'default_project_id' => $this->defaultProjectId,
            'first_response_target_minutes' => $this->firstResponseTargetMinutes,
            'next_response_target_minutes' => $this->nextResponseTargetMinutes,
            'resolve_target_minutes' => $this->resolveTargetMinutes,
            'auto_create_issue_for_ticket_type_id' => $this->autoCreateIssueForTicketTypeId,
            'auto_create_issue_project_id' => $this->autoCreateIssueProjectId,
        ]);

        // Ensure the default project is included in associations
        $syncIds = collect($this->projectIds)
            ->when($this->defaultProjectId, fn ($c) => $c->push($this->defaultProjectId))
            ->unique()
            ->all();

        $product->projects()->sync($syncIds);

        session()->flash('status', 'Product created. You can now configure mappings.');
        $this->redirectRoute('support.staff.products.edit', ['productId' => $product->getKey()], navigate: true);
    }

    public function render(): View
    {
        $projects = Project::query()->orderBy('name')->get(['id', 'name', 'key']);
        $ticketTypes = TicketType::query()->orderBy('name')->get(['id', 'name']);

        return view('livewire.staff.support.products.create', compact('projects', 'ticketTypes'));
    }
}
