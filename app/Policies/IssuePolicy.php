<?php

namespace App\Policies;

use App\Models\Issue;
use App\Models\User;
use App\Services\RecordAccessService;

class IssuePolicy
{
    public function __construct(private RecordAccessService $shares) {
    }

    public function view(User $user, Issue $issue): bool
    {
        if ($user->hasPermissionTo('is-super-admin')) {
            return true;
        }

        $level = $this->shares->levelFor($user, $issue);
        if ($level?->allows('view') === true) {
            return true;
        }

        if (! $user->can('issues.view')) {
            return false;
        }

        return $issue->isAccessibleBy($user);
    }

    public function update(User $user, Issue $issue): bool
    {
        if ($user->hasPermissionTo('is-super-admin') || $user->hasPermissionTo('issues.manage')) {
            return true;
        }

        $level = $this->shares->levelFor($user, $issue);
        if ($level?->allows('update') === true) {
            return true;
        }

        if (! $issue->isAccessibleBy($user)) {
            return false;
        }

        return $user->hasPermissionTo('issues.update') || $user->hasPermissionTo('issues.manage');
    }

    public function delete(User $user, Issue $issue): bool
    {
        if ($user->hasPermissionTo('is-super-admin') || $user->hasPermissionTo('issues.manage')) {
            return true;
        }

        $level = $this->shares->levelFor($user, $issue);
        if ($level?->allows('manage') === true) {
            return true;
        }

        if (! $issue->isAccessibleBy($user)) {
            return false;
        }

        return $user->hasPermissionTo('issues.delete') || $user->hasPermissionTo('issues.manage');
    }

    public function share(User $user, Issue $issue): bool
    {
        if ($user->hasPermissionTo('is-super-admin') || $user->hasPermissionTo('issues.manage')) {
            return true;
        }

        $level = $this->shares->levelFor($user, $issue);
        return $level?->allows('manage') === true;
    }
}
