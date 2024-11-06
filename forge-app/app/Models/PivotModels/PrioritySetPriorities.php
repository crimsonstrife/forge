<?php

namespace App\Models\PivotModels;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Issues\IssuePriority;
use App\Models\PrioritySet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
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

    protected $fillable = ['priority_set_id', 'issue_priority_id', 'order', 'is_default'];

    protected $casts = [
        'is_default' => 'boolean',
        'order' => 'integer',
    ];

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
}
