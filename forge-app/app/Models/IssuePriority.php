<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class IssuePriority
 *
 * This class represents the IssuePriority model in the application.
 * It extends the base Model class and uses the HasFactory and SoftDeletes traits.
 *
 * @package App\Models
 */
class IssuePriority extends Model
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
     * Get the issues associated with this issue priority.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class, 'priority_id', 'id')->withTrashed();
    }

    /**
     * Scope a query to only include default issue priorities.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope a query to only include issue priorities that are not default.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNotDefault($query)
    {
        return $query->where('is_default', false);
    }
}
