<?php

namespace App\Livewire\Support;

use App\Models\ServiceProduct;
use App\Models\SupportIdentity;
use App\Models\Ticket;
use App\Models\TicketPriority;
use App\Models\TicketStatus;
use App\Models\TicketType;
use App\Services\Support\TextRedactor;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class NewTicket extends Component
{
    /** @var array<int, array{id:string,name:string,project?:string}> */
    public array $products = [];

    #[Validate('required|string|exists:service_products,id')]
    public string $productId = '';

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
            ->get(['id','name','default_project_id'])
            ->map(fn (ServiceProduct $p) => [
                'id'      => $p->getKey(),
                'name'    => $p->name . ($p->defaultProject?->name ? " (Project: {$p->defaultProject->name})" : ''),
                'project' => $p->defaultProject?->name,
            ])
            ->all();
    }

    public function submit(TextRedactor $redactor): void
    {
        $this->validate();

        $normalized = mb_strtolower(trim($this->email));
        $hash = hash('sha256', $normalized);

        $identity = SupportIdentity::query()->firstOrCreate(
            ['email_hash' => $hash],
            ['email_encrypted' => $normalized, 'token' => (string) str()->ulid()]
        );

        $statusId   = (int) TicketStatus::query()->where('name', 'New')->value('id');
        $priorityId = (int) TicketPriority::query()->where('name', 'Medium')->value('id');
        $typeId     = (int) TicketType::query()->where('name', 'Bug')->value('id');

        /** @var ServiceProduct $product */
        $product = ServiceProduct::query()
            ->select(['id','organization_id','default_project_id'])
            ->findOrFail($this->productId);

        Ticket::query()->create([
            'organization_id'    => $product->organization_id,
            'service_product_id' => $product->getKey(),
            'project_id'         => $product->default_project_id,
            'submitter_name'     => $this->name,
            'submitter_email'    => $normalized,
            'email_hash'         => $hash,
            'subject'            => $this->subject,
            'body'               => $this->body,
            'redacted_body'      => $redactor->redact($this->body),
            'status_id'          => $statusId,
            'priority_id'        => $priorityId,
            'type_id'            => $typeId,
            'access_token'       => (string) str()->ulid(),
            'via'                => 'public',
        ]);

        // Use Livewire redirect (no SPA navigate) to avoid client-side ResizeObserver churn
        $this->redirect(
            route('support.access.by-token', ['token' => $identity->token]),
            navigate: false
        );
    }

    public function render(): View
    {
        return view('livewire.support.new-ticket');
    }
}
