<?php

namespace App\Policies;

use App\Models\Permission;
use App\Models\Project;
use App\Models\User;
use App\Services\RecordAccessService;
use Illuminate\Support\Facades\DB;

class ProjectPolicy
{
    public function __construct(private RecordAccessService $shares)
    {
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->hasPermissionTo('is-super-admin')) {
            return true;
        }

        if ($user->can('projects.view') && $project->isAccessibleBy($user)) {
            return true;
        }

        $level = $this->shares->levelFor($user, $project);
        return $level?->allows('view') ?? false;
    }

    public function create(User $user): bool
    {
        if ($user->hasPermissionTo('is-super-admin')) {
            return true;
        }

        if ($user->hasPermissionTo('projects.manage')) {
            return true;
        }

        return $this->hasManageViaRole($user);
    }

    public function update(User $user, Project $project): bool
    {
        if ($user->hasPermissionTo('is-super-admin') || $user->hasPermissionTo('projects.manage')) {
            return true;
        }

        if ($project->isAccessibleBy($user) && ($this->hasManageViaRole($user))) {
            return true;
        }

        $level = $this->shares->levelFor($user, $project);
        return $level?->allows('update') ?? false;
    }

    public function delete(User $user, Project $project): bool
    {
        if ($user->hasPermissionTo('is-super-admin') || $user->hasPermissionTo('projects.manage')) {
            return true;
        }

        if ($project->isAccessibleBy($user) && ($this->hasManageViaRole($user) || $user->hasPermissionTo('projects.delete'))) {
            return true;
        }

        $level = $this->shares->levelFor($user, $project);
        return $level?->allows('manage') ?? false;
    }

    public function share(User $user, Project $project): bool
    {
        if ($user->hasPermissionTo('is-super-admin') || $user->hasPermissionTo('projects.manage')) {
            return true;
        }

        $level = $this->shares->levelFor($user, $project);
        return $level?->allows('manage') ?? false;
    }

    /** Resolve once: does the user have projects.manage via any role? */
    private function hasManageViaRole(User $user): bool
    {
        static $permId = null;
        if ($permId === null) {
            $permId = Permission::query()->where('name', 'projects.manage')->value('id');
        }
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
