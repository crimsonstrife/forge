<?php

namespace App\Livewire\Support;

use App\Models\Organization;
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
    #[Validate('required|string|min:5|max:160')]
    public string $subject = '';

    #[Validate('required|string|min:10|max:5000')]
    public string $body = '';

    #[Validate('required|string|min:2|max:120')]
    public string $name = '';

    #[Validate('required|email:rfc,dns')]
    public string $email = '';

    public function submit(TextRedactor $redactor): void
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
        $typeId = (int) TicketType::query()->where('name', 'Bug')->value('id');
        $organizationId = auth()->user()?->organization_id ?? (string) Organization::query()->value('id');

        $ticket = Ticket::query()->create([
            'organization_id' => $organizationId,
            'submitter_name'  => $this->name,
            'submitter_email' => $normalized,
            'email_hash'      => $hash,
            'subject'         => $this->subject,
            'body'            => $this->body,
            'redacted_body'   => $redactor->redact($this->body),
            'status_id'       => $statusId,
            'priority_id'     => $priorityId,
            'type_id'         => $typeId,
            'access_token'    => (string) str()->ulid(),
            'via'             => 'public',
        ]);

        // TODO: notify customer & staff

        $this->redirect(route('support.access.by-token', ['token' => $identity->token]), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.support.new-ticket');
    }
}
