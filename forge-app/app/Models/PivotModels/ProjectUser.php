<?php

namespace App\Models\PivotModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\User;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectRole;
use App\Traits\IsPermissible;

/**
 * Class ProjectUser
 *
 * This class represents the pivot table model for the relationship between projects and users.
 * It extends the Pivot class provided by Laravel's Eloquent ORM.
 *
 * @package App\Models\PivotModels
 */
class ProjectUser extends Pivot
{
    use HasFactory;
    use IsPermissible;

    protected $table = 'project_users';

    protected $fillable = [
        'project_id',
        'user_id',
        'role_id',
    ];

    /**
     * Get the user associated with the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the project associated with the pivot model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the role associated with the ProjectUser.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(ProjectRole::class, 'role_id');
    }
}
