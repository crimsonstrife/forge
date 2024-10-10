<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Auth\PermissionGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 *
 * Class PermissionGroupPolicy
 *
 * This class defines the authorization policies for permission groups.
 */
class PermissionGroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->can('list-permission-set');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Auth\PermissionGroup $permissionGroup
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, PermissionGroup $permissionGroup)
    {
        return $user->can('read-permission-set', $permissionGroup);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->can('create-permission-set');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Auth\PermissionGroup  $permissionGroup
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, PermissionGroup $permissionGroup)
    {
        return $user->can('update-permission-set', $permissionGroup);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Auth\PermissionGroup  $permissionGroup
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, PermissionGroup $permissionGroup)
    {
        return $user->can('delete-permission-set', $permissionGroup);
    }
}
