<?php

namespace App\Policies;

use App\Models\SocialAccount;
use App\Models\User;

final class SocialAccountPolicy
{
    public function delete(User $user, SocialAccount $account): bool
    {
        return $account->user_id === $user->id;
    }
}
