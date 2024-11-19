<?php

namespace App\Models\PivotModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\IsPermissible;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class TeamMember
 *
 * This class represents a pivot model for the TeamMember relationship.
 * It extends the Pivot class provided by Laravel, which is used for
 * handling many-to-many relationships.
 *
 * @package App\Models\Teams
 */
class TeamMember extends Pivot
{
    use HasFactory;
    use IsPermissible;

    protected $table = 'team_members';

    protected $fillable = [
        'team_id',
        'user_id',
        'role_id',
    ];

    /**
     * Get the user record associated with the team member.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the team that the team member belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the role associated with the team member.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(TeamRole::class, 'role_id');
    }
}
