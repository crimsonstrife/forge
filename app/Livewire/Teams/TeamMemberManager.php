<?php

namespace App\Livewire\Teams;

use Laravel\Jetstream\Http\Livewire\TeamMemberManager as JetstreamTeamMemberManager;
use Laravel\Jetstream\Jetstream;

class TeamMemberManager extends JetstreamTeamMemberManager
{
    /**
     * Allow the given user's role to be managed, even if their membership role is missing.
     *
     * @param  int  $userId
     */
    public function manageRole($userId): void
    {
        $this->currentlyManagingRole = true;
        $this->managingRoleFor = Jetstream::findUserByIdOrFail($userId);
        $this->currentRole = $this->managingRoleFor->teamRole($this->team)?->key ?? $this->defaultRoleKey();
    }

    public function roleName(?string $roleKey): ?string
    {
        if ($roleKey === null || $roleKey === '') {
            return null;
        }

        return Jetstream::findRole($roleKey)?->name;
    }

    protected function defaultRoleKey(): string
    {
        return (string) collect($this->roles)->first()?->key;
    }
}
