<?php

namespace App\Models\Issues;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\IsPermissable;
use App\Models\Icon;
use App\Models\PrioritySet;
use App\Models\PivotModels\PrioritySetPriorities;
use App\Models\PivotModels\PrioritySetDefault;
use Illuminate\Validation\ValidationException;

/**
 * Class IssuePriority
 *
 * This class represents the IssuePriority model in the application.
 * It extends the base Model class and uses the HasFactory and SoftDeletes traits.
 *
 * @package App\Models\Issues\IssuePriority
 */
class IssuePriority extends Model
{
    use HasFactory;
    use SoftDeletes;
    use IsPermissable;

    protected $fillable = [
        'name',
        'color',
        'icon',
        'is_default'
    ];

    /**
     * Boot the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Clear the cache when Issue Priorities are saved or deleted
        static::saved(function () {
            cache()->forget('issue_priorities.all');
        });

        // Clear the cache when Issue Priorities are saved or deleted
        static::deleted(function () {
            cache()->forget('issue_priorities.all');
        });

        static::saving(function ($model) {
            $query = static::where('name', $model->name)
                ->where('icon', $model->icon)
                ->where('color', $model->color);

            if ($model->exists) {
                $query->where('id', '!=', $model->id);
            }

            if ($query->exists()) {
                throw ValidationException::withMessages([
                    'name' => 'Issue priority with this name, icon, and color already exists, please make changes to avoid duplication.'
                ]);
            }
        });
    }

    /**
     * Relationship with the icon model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function icon()
    {
        return $this->belongsTo(Icon::class, 'icon', 'id');
    }

    /**
     * Get the default issue type.
     *
     * @return \Illuminate\Database\Eloquent\Builder|IssueType
     */
    public static function getDefault()
    {
        return self::where('is_default', true)->first();
    }

    /**
     * Get the issues associated with this issue priority.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class, 'issue_priority_id', 'id')->with(function ($query) {
            $query->withTrashed();
        });
    }

    /**
     * Get the priority sets that this priority is associated with.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function prioritySets()
    {
        return $this->belongsToMany(PrioritySet::class)->using(PrioritySetPriorities::class)->withPivot('order', 'is_default')->withTimestamps();
    }

    /**
     * Sets the default values for the priority.
     *
     * This method is responsible for initializing or resetting the default
     * priority values for an issue. It ensures that the priority is set to
     * a predefined state when called.
     *
     * @return void
     */
    public function prioritySetDefaults()
    {
        return $this->hasManyThrough(PrioritySetDefault::class, PrioritySetPriorities::class, 'issue_priority_id', 'priority_set_issue_pair');
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
