<?php

namespace App\Livewire\Support;

use App\Models\SupportIdentity;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

final class MyTickets extends Component
{
    public ?string $identityId = null;

    public function mount(?string $identityId = null): void
    {
        $this->identityId = $identityId;
    }

    public function render(): View
    {
        $identity = $this->resolveIdentity();

        $tickets = $identity?->tickets()
            ->latest()
            ->select(['id', 'key', 'subject', 'status_id', 'created_at'])
            ->with('status:id,name')
            ->get()
            ?? new Collection;

        return view('livewire.support.my-tickets', compact('tickets'));
    }

    private function resolveIdentity(): ?SupportIdentity
    {
        if (filled($this->identityId)) {
            return SupportIdentity::query()
                ->whereKey($this->identityId)
                ->whereNull('revoked_at')
                ->first();
        }

        if (app()->bound(SupportIdentity::class)) {
            /** @var SupportIdentity $identity */
            $identity = app(SupportIdentity::class);

            return $identity;
        }

        return null;
    }
}
