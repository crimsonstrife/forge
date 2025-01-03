<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a model for Project Git History.
 *
 * This class extends the Laravel Model class and uses the HasFactory trait.
 * It is used to interact with the "project_git_histories" table in the database.
 */
class ProjectGitHistory extends Model
{
    use HasFactory;

    /**
     * Get the repository associated with the project.
     *
     * @return \App\Models\Repository
     */
    public function repository()
    {
        return $this->belongsTo(ProjectRepository::class);
    }
}
