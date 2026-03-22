<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

final class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAny([
            'support.view',
            'tickets.view',
            'tickets.manage',
            'is-admin',
            'is-super-admin',
        ]);
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $this->viewAny($user);
    }

    public function manage(User $user, Ticket $ticket): bool
    {
        return $user->canAny([
            'support.manage',
            'tickets.manage',
            'tickets.update',
            'is-admin',
            'is-super-admin',
        ]);
    }

    public function convertToIssue(User $user, Ticket $ticket): bool
    {
        return $user->canAny([
            'support.convert_to_issue',
            'tickets.manage',
            'issues.create',
            'is-admin',
            'is-super-admin',
        ]);
    }
}
