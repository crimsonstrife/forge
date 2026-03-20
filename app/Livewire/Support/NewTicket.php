<?php

namespace App\Livewire\Support;

use App\Models\ServiceProduct;
use App\Models\SupportIdentity;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketType;
use App\Services\Support\TextRedactor;
use App\Services\Support\TicketWorkflowService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class NewTicket extends Component
{
    /** @var array<int, array{id:string,name:string,description?:string,project?:string}> */
    public array $products = [];

    /** @var array<int, array{id:int,name:string,slug:string}> */
    public array $types = [];

    #[Validate('required|string|exists:service_products,id')]
    public string $productId = '';

    #[Validate('required|integer|exists:ticket_types,id')]
    public ?int $typeId = null;

    #[Validate('required|string|min:5|max:160')]
    public string $subject = '';

    #[Validate('required|string|min:10|max:5000')]
    public string $body = '';

    #[Validate('required|string|min:2|max:120')]
    public string $name = '';

    #[Validate('required|email:rfc')]
    public string $email = '';

    public function mount(): void
    {
        $this->products = ServiceProduct::query()
            ->with('defaultProject:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'default_project_id'])
            ->map(fn (ServiceProduct $p) => [
                'id' => $p->getKey(),
                'name' => $p->name,
                'description' => $p->description,
                'project' => $p->defaultProject?->name,
            ])
            ->all();

        $this->types = TicketType::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (TicketType $type) => [
                'id' => (int) $type->getKey(),
                'name' => $type->name,
                'slug' => Str::slug($type->name),
            ])
            ->all();

        $this->typeId = TicketType::query()->where('name', 'Bug')->value('id')
            ?? TicketType::query()->orderBy('name')->value('id');
    }

    public function submit(TextRedactor $redactor, TicketWorkflowService $workflow): void
    {
        $this->validate();

        $normalized = mb_strtolower(trim($this->email));
        $hash = hash('sha256', $normalized);

        $identity = SupportIdentity::query()->firstOrCreate(
            ['email_hash' => $hash],
            ['email_encrypted' => $normalized, 'token' => (string) str()->ulid()]
        );

        $statusId = (int) TicketStatus::query()->where('name', 'New')->value('id');
        $priorityId = (int) TicketPriority::query()->where('name', 'Medium')->value('id');
        /** @var ServiceProduct $product */
        $product = ServiceProduct::query()
            ->select(['id', 'organization_id', 'default_project_id'])
            ->findOrFail($this->productId);

        $ticket = Ticket::query()->create([
            'organization_id' => $product->organization_id,
            'service_product_id' => $product->getKey(),
            'project_id' => $product->default_project_id,
            'submitter_name' => $this->name,
            'submitter_email' => $normalized,
            'email_hash' => $hash,
            'subject' => $this->subject,
            'body' => $this->body,
            'redacted_body' => $redactor->redact($this->body),
            'status_id' => $statusId,
            'priority_id' => $priorityId,
            'type_id' => $this->typeId,
            'access_token' => (string) str()->ulid(),
            'via' => 'public',
        ]);

        $workflow->initialize($ticket);

        // Use Livewire redirect (no SPA navigate) to avoid client-side ResizeObserver churn
        $this->redirect(
            route('support.access.by-token', ['token' => $identity->token]),
            navigate: false
        );
    }

    public function render(): View
    {
        $selectedProduct = collect($this->products)->firstWhere('id', $this->productId);
        $selectedType = collect($this->types)->firstWhere('id', $this->typeId);

        return view('livewire.support.new-ticket', [
            'selectedProduct' => $selectedProduct,
            'typeTemplate' => $this->typeTemplateFor($selectedType['slug'] ?? null),
        ]);
    }

    /** @return array{intro:string,subject:string,body:string} */
    private function typeTemplateFor(?string $typeSlug): array
    {
        return match ($typeSlug) {
            'feature-request' => [
                'intro' => 'Tell us what outcome you want and how it fits into your workflow.',
                'subject' => 'Example: Bulk edit tags across multiple tickets',
                'body' => 'What are you trying to do today? What is missing, and what would a good solution look like?',
            ],
            'question' => [
                'intro' => 'Share the question plus any context that would help us answer it without a back-and-forth.',
                'subject' => 'Example: How do I map support tickets to a project?',
                'body' => 'What did you expect to find, what have you tried, and where are you blocked?',
            ],
            default => [
                'intro' => 'Describe the bug, what you expected, and how to reproduce it.',
                'subject' => 'Example: Portal submission fails after selecting a product',
                'body' => 'What happened, what should have happened instead, and what steps can we use to reproduce it?',
            ],
        };
    }
}
