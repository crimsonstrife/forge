<?php

namespace App\Models\Issues;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Issues\Issue;

/**
 * Class IssueType
 *
 * This class represents the IssueType model in the application.
 * It extends the base Model class and uses the HasFactory and SoftDeletes traits.
 *
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $color
 * @property string $icon
 * @property bool $is_default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Issues\Issue[] $issues
 * @property int|null $issues_count
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType newQuery()
 * @method static \Illuminate\Database\Query\Builder|IssueType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType query()
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereIsDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|IssueType withTrashed()
 * @method static \Illuminate\Database\Query\Builder|IssueType withoutTrashed()
 * @mixin \Eloquent
 */
class IssueType extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'color',
        'icon',
        'is_default'
    ];

    /**
     * Get the issues associated with the issue type.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class, 'type_id', 'id')->withTrashed();
    }
}
