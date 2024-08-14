<?php

use Illuminate\Support\Facades\Broadcast;

/**
 * Define the broadcasting channel for the specified user.
 *
 * @param  \App\Models\User  $user The user instance.
 * @param  int  $id The user ID.
 * @return bool Returns true if the user ID matches the authenticated user's ID, false otherwise.
 */
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
