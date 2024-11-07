<?php

namespace App\Models\PivotModels;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Issues\IssuePriority;
use App\Models\PrioritySet;
use App\Models\PivotModels\PrioritySetDefault;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Validation\ValidationException;

/**
 * Class PrioritySetPriorities
 *
 * This class represents the model for the priority set priorities.
 * It extends the base Model class provided by the framework.
 *
 * @package App\Models\PivotModels
 */
class PrioritySetPriorities extends Pivot
{
    use SoftDeletes;

    protected $table = 'issue_priority_priority_set';

    protected $fillable = ['priority_set_id', 'issue_priority_id', 'order'];

    protected $casts = [
        'is_default' => 'boolean',
        'order' => 'integer',
    ];

    // Indicate if the model should be timestamped
    public $timestamps = true;

    /**
     * The "booting" method of the model.
     * This method is called when the model is being initialized.
     * You can use this method to set up event listeners or perform other
     * initialization tasks.
     */
    protected static function boot()
    {
        parent::boot();
    }

    /**
     * Get the priority set that this priority belongs to.
     *
     * @return BelongsTo
     */
    public function prioritySet(): BelongsTo
    {
        return $this->belongsTo(PrioritySet::class);
    }

    /**
     * Get the issue priority that this priority set belongs to.
     *
     * @return BelongsTo
     */
    public function issuePriority(): BelongsTo
    {
        return $this->belongsTo(IssuePriority::class);
    }

    /**
     * This method is used to set the default priority.
     *
     * @return HasOne
     */
    public function default()
    {
        return $this->hasOne(PrioritySetDefault::class, 'priority_set_issue_pair');
    }

    /**
     * Sets the current priority as the default priority.
     *
     * This method updates the relevant records to mark the current priority
     * as the default one within the priority set.
     *
     * @return Model
     */
    public function setAsDefault()
    {
        return $this->default()->updateOrCreate(
            ['priority_set_issue_pair' => $this->id],
            ['is_default' => true]
        );
    }
}
