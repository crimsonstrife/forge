<?php

namespace App\Livewire\Projects\Concerns;

trait DeterminesOrganizationScopeAccess
{
    protected function canManageAllOrganizations(): bool
    {
        $user = auth()->user();

        return $user?->hasPermissionTo('is-super-admin')
            || $user?->can('is-admin');
    }
}
