<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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
        // Super admin wins
        if ($user->hasPermissionTo('is-super-admin')) {
            return true;
        }

        // Direct user permission (global or team-scoped) — fast path
        if ($user->hasPermissionTo('projects.manage')) {
            return true;
        }

        // Resolve the permission id once
        return $this->resolveThePermissionIdOnce($user);
    }

    public function update(User $user, Project $project): bool
    {
        // Keep update tied to access + manage
        if (! $project->isAccessibleBy($user)) {
            return false;
        }

        // Same cross-team logic as create, but reuse the quick checks
        if ($user->hasPermissionTo('is-super-admin') || $user->hasPermissionTo('projects.manage')) {
            return true;
        }

        return $this->resolveThePermissionIdOnce($user);
    }

    /**
     * @param User $user
     * @return bool
     */
    private function resolveThePermissionIdOnce(User $user): bool
    {
        $permId = Permission::query()->where('name', 'projects.manage')->value('id');
        if (! $permId) {
            return false;
        }

        return DB::table('model_has_roles as mhr')
            ->join('role_has_permissions as rhp', 'rhp.role_id', '=', 'mhr.role_id')
            ->where('mhr.model_type', $user->getMorphClass())
            ->where('mhr.model_id', $user->getKey())
            ->where('rhp.permission_id', $permId)
            ->exists();
    }
}
