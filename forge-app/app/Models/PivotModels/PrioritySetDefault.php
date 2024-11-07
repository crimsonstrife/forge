<?php

namespace App\Models\PivotModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\PivotModels\PrioritySetPriorities;

/**
 * Class PrioritySetDefault
 *
 * This class represents the pivot model for the PrioritySetDefault.
 * It extends the base Pivot class provided by Laravel.
 *
 * @package App\Models\PivotModels
 */
class PrioritySetDefault extends Pivot
{
    use SoftDeletes;

    protected $table = 'priority_set_defaults';

    protected $fillable = ['priority_set_issue_pair', 'is_default'];

    /**
     * This method sets the priority for the priority set.
     *
     * @return BelongsTo
     */
    public function prioritySetPriority()
    {
        return $this->belongsTo(PrioritySetPriorities::class, 'priority_set_issue_pair');
    }
}
