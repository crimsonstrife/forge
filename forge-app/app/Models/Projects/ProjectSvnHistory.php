<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ProjectSvnHistory
 *
 * This class represents the ProjectSvnHistory model.
 * It extends the base Model class and uses the HasFactory trait.
 */
class ProjectSvnHistory extends Model
{
    use HasFactory;

    /**
     * Get the repository associated with the project SVN history.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function repository()
    {
        return $this->belongsTo(ProjectRepository::class);
    }
}
