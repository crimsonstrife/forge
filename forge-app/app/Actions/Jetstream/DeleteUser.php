<?php

namespace App\Actions\Jetstream;

use App\Models\User;
use Laravel\Jetstream\Contracts\DeletesUsers;

/**
 * DeleteUser class.
 *
 * This class implements the DeletesUsers interface and is responsible for deleting a user.
 */
class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     */
    public function delete(User $user): void
    {
        $user->deleteProfilePhoto();
        $user->tokens->each->delete();
        $user->delete();
    }
}
