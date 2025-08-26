<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar;

class ProjectPolicy
{
    public function view(User $user, Project $project): bool
    {
        if (! $user->can('projects.view')) {
            return false;
        }
        return $project->isAccessibleBy($user);
    }

    public function create(User $user): bool
    {
        if ($user->can('is-super-admin')) {
            return true;
        }

        // Try current team first (fast-path)
        if ($user->can('projects.manage')) {
            return true;
        }

        // Check across all teams
        $registrar = app(PermissionRegistrar::class);
        $origTeam  = $registrar->getPermissionsTeamId();

        foreach ($user->allTeams() as $team) {
            $registrar->setPermissionsTeamId($team->id);
            if ($user->can('projects.manage')) {
                $registrar->setPermissionsTeamId($origTeam);
                return true;
            }
        }

        // Also check global (null team) grants
        $registrar->setPermissionsTeamId(null);
        $ok = $user->can('projects.manage');
        $registrar->setPermissionsTeamId($origTeam);

        return $ok;
    }

    public function update(User $user, Project $project): bool
    {
        return $user->can('projects.manage') && $project->isAccessibleBy($user);
    }
}
