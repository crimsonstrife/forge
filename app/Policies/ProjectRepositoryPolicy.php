<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectRepositoryPolicy
{
    public function connect(User $user, Project $project): bool
    {
        return $user->can('projects.manage', $project)
            && $project->issues()->count() === 0;
    }
}
