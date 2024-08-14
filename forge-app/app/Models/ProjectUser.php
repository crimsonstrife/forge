<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ProjectUser Model
 *
 * Represents the relationship between a project, a user, and a role.
 */
class ProjectUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'role_id'
    ];

    /**
     * Get the project associated with the ProjectUser.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the role that owns Project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(ProjectRole::class, 'role_id', 'id'); //TODO: create ProjectRole model/tables
    }
}
