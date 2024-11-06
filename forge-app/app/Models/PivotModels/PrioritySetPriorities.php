<?php

namespace App\Models\PivotModels;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Issues\IssuePriority;
use App\Models\PrioritySet;

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

    /**
     * Get the priority set that this priority belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prioritySet()
    {
        return $this->belongsTo(PrioritySet::class, 'priority_set_id', 'id');
    }

    /**
     * Get the issue priority that this pivot belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function issuePriority()
    {
        return $this->belongsTo(IssuePriority::class, 'issue_priority_id', 'id');
    }
}
