<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;

final class OrganizationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('is-admin') || $user->can('is-super-admin');
    }

    public function view(User $user, Organization $organization): bool
    {
        return $user->can('is-admin') || $user->can('is-super-admin');
    }

    public function create(User $user): bool
    {
        return $user->can('is-admin') || $user->can('is-super-admin');
    }

    public function update(User $user, Organization $organization): bool
    {
        return $user->can('is-admin') || $user->can('is-super-admin');
    }

    public function delete(User $user, Organization $organization): bool
    {
        return $user->can('is-super-admin');
    }

    public function restore(User $user, Organization $organization): bool
    {
        return $user->can('is-super-admin');
    }

    public function forceDelete(User $user, Organization $organization): bool
    {
        return false;
    }
}
