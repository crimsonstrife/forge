<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

final class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('support.view') || $user->can('is_admin') || $user->can('is_superadmin');
    }

    public function view(User $user, Ticket $ticket): bool
    {
        return $this->viewAny($user);
    }

    public function manage(User $user, Ticket $ticket): bool
    {
        return $user->can('support.manage') || $user->can('is_admin') || $user->can('is_superadmin');
    }

    public function convertToIssue(User $user, Ticket $ticket): bool
    {
        return $user->can('support.convert_to_issue') || $user->can('is_admin') || $user->can('is_superadmin');
    }
}
