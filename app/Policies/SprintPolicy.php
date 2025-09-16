<?php
namespace App\Policies;

use App\Models\Project;
use App\Models\Sprint;
use App\Models\User;

final class SprintPolicy
{
    public function viewAny(User $user, Project $project): bool
    {
        return $user->can('projects.view') || $user->can('is-admin') || $user->can('is-super-admin');
    }

    public function create(User $user, Project $project): bool
    {
        return $user->can('sprints.manage') || $user->can('projects.manage') || $user->can('is-admin') || $user->can('is-super-admin');
    }

    public function update(User $user, Sprint $sprint): bool
    {
        return $user->can('sprints.manage') || $user->can('projects.manage') || $user->can('is-admin') || $user->can('is-super-admin');
    }

    public function start(User $user, Sprint $sprint): bool
    {
        return $this->update($user, $sprint);
    }

    public function close(User $user, Sprint $sprint): bool
    {
        return $this->update($user, $sprint);
    }
}
