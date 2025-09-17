<?php

namespace App\Livewire\Support;

use App\Models\SupportIdentity;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Services\Support\TextRedactor;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class ShowTicket extends Component
{
    public Ticket $ticket;

    #[Validate('required|string|min:2|max:5000')]
    public string $reply = '';

    public function mount(SupportIdentity $identity, string $key): void
    {
        $this->ticket = Ticket::query()
            ->where('key', $key)
            ->where('email_hash', $identity->email_hash)
            ->with(['status:id,name','product:id,name'])
            ->firstOrFail();
    }

    public function postReply(TextRedactor $redactor): void
    {
        $this->validate();

        $this->ticket->comments()->create([
            'body'          => $this->reply,
            'redacted_body' => $redactor->redact($this->reply),
            'is_internal'   => false,
        ]);

        // TODO: notify staff

        $this->reset('reply');
    }

    public function render(): View
    {
        $comments = $this->ticket->comments()
            ->where('is_internal', false)
            ->with('user:id,name,email,profile_photo_path')
            ->latest()
            ->get(['id','redacted_body','created_at','user_id']);

        return view('livewire.support.show-ticket', compact('comments'));
    }

}
