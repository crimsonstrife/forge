<?php

namespace App\Models\PivotModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Projects\Project;

/**
 *
 * Class ProjectFavorite
 *
 * @package App\Models
 *
 * @property int $id
 * @property int $user_id
 * @property int $project_id
 * @property-read \App\Models\Projects\Project $project
 * @property-read \App\Models\User $user
 */
class ProjectFavorite extends Pivot
{
    use HasFactory;

    protected $table = 'project_favorites';

    protected $fillable = [
        'user_id',
        'project_id',
    ];
}
