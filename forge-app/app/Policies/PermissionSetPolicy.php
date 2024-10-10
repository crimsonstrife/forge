<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Auth\PermissionSet;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 *
 * Class PermissionSetPolicy
 *
 * This class defines the authorization policies for permission sets.
 */
class PermissionSetPolicy
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
     * @param  \App\Models\Auth\PermissionSet $permissionSet
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, PermissionSet $permissionSet)
    {
        return $user->can('read-permission-set', $permissionSet);
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
     * @param  \App\Models\Auth\PermissionSet  $permissionSet
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, PermissionSet $permissionSet)
    {
        return $user->can('update-permission-set', $permissionSet);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Auth\PermissionSet  $permissionSet
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, PermissionSet $permissionSet)
    {
        return $user->can('delete-permission-set', $permissionSet);
    }
}
