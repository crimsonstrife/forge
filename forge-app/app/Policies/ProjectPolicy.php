<?php

namespace App\Policies;

use App\Models\Projects\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class ProjectPolicy
 *
 * This class defines the authorization policies for the Project model.
 */
class ProjectPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->can('list-project');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Projects\Project $project
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Project $project)
    {
        return $user->can('read-project')
            && (
                $project->owner_id === $user->id
                ||
                $project->users()->where('users.id', $user->id)->count()
            );
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can('create-project');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Projects\Project $project
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Project $project)
    {
        return $user->can('update-project')
            && (
                $project->owner_id === $user->id
                ||
                $project->users()->where('users.id', $user->id)
                ->where('role', config('system.projects.affectations.roles.can_manage'))
                ->count()
            );
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Projects\Project $project
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Project $project)
    {
        return $user->can('delete-project');
    }

    /**
     * Determine if the given user can manage roles for the specified project.
     *
     * @param User $user The user instance.
     * @param Project $project The project instance.
     * @return bool
     */
    public function manageRoles(User $user, Project $project)
    {
        return $project->owner_id === $user->id || $user->isProjectAdmin($project);
    }
}
