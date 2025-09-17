<?php

namespace App\Livewire\Support;

use App\Models\SupportIdentity;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class MyTickets extends Component
{
    public function render(SupportIdentity $identity): View
    {
        $tickets = $identity->tickets()
            ->latest()
            ->select(['id','key','subject','status_id','created_at'])
            ->with('status:id,name')
            ->get();

        return view('livewire.support.my-tickets', compact('tickets'));
    }
}
