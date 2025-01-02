<?php

namespace App\Models\Issues;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Activity;
use App\Models\Issues\Issue;
use App\Traits\IsPermissible;

/**
 * Class IssueHour
 *
 * @package App\Models
 *
 * @property int $id
 * @property int $user_id
 * @property int $issue_id
 * @property float $value
 * @property string|null $comment
 * @property int $activity_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 *
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Issues\Issue $issue
 * @property-read \App\Models\Activity $activity
 * @property-read \Illuminate\Support\CarbonInterval $forHumans
 */
class IssueHour extends Model
{
    use HasFactory;
    use IsPermissible;

    protected $fillable = [
        'user_id',
        'issue_id',
        'value',
        'comment',
        'activity_id'
    ];

    /**
     * Get the user associated with the IssueHour.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the issue associated with the issue hour.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'issue_id', 'id');
    }

    /**
     * Get the activity associated with the issue hour.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'id');
    }

    /**
     * Converts the value of the IssueHour to a human-readable format.
     *
     * @return Attribute The Attribute object containing the converted value.
     */
    public function forHumans(): Attribute
    {
        return new Attribute(
            get: function () {
                $seconds = $this->value * 3600;
                return CarbonInterval::seconds($seconds)->cascade()->forHumans();
            }
        );
    }
}
