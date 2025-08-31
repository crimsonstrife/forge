<?php
namespace App\Policies;

use App\Models\Goal;
use App\Models\User;

class GoalPolicy
{
    public function viewAny(User $user): bool { return $user->can('goals.view'); }
    public function view(User $user, Goal $goal): bool { return $user->can('goals.view'); }
    public function create(User $user): bool { return $user->can('goals.create'); }
    public function update(User $user, Goal $goal): bool { return $user->can('goals.update'); }
    public function delete(User $user, Goal $goal): bool { return $user->can('goals.delete'); }
}
