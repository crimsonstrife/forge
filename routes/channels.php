<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', static function ($user, string $id): bool {
    return (string) $user->getAuthIdentifier() === (string) $id;
});
