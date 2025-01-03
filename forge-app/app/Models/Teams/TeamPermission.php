<?php

namespace App\Models\Teams;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\IsPermissible;

/**
 * Class TeamPermission
 *
 * This class represents the permissions associated with a team.
 * It extends the base Model class provided by the framework.
 *
 * @package App\Models\Teams
 */
class TeamPermission extends Model
{
    use HasFactory;
    use IsPermissible;

    protected $fillable = ['team_id', 'name', 'description', 'guard_name'];

    /**
     * Get the team associated with the team permission.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
