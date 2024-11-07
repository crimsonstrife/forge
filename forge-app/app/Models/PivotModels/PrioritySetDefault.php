<?php

namespace App\Models\PivotModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\PivotModels\PrioritySetPriorities;
use Illuminate\Support\Facades\Log;

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

    /**
     * This method sets the priority for the priority set.
     *
     * @return BelongsTo
     */
    public function prioritySetPriorities()
    {
        return $this->belongsTo(PrioritySetPriorities::class, 'priority_set_issue_pair');
    }

    /**
     * Sets the default value for a given priority set issue pair.
     *
     * @param int $prioritySetIssuePairId The ID of the priority set issue pair.
     * @return void
     */
    public static function setDefaultForPrioritySet($prioritySetId, $issuePriorityId)
    {
        Log::info('Setting default', ['priority_set_id' => $prioritySetId, 'issue_priority_id' => $issuePriorityId]);

        // Remove existing defaults for this priority set
        self::whereHas('prioritySetPriorities', function ($query) use ($prioritySetId) {
            $query->where('priority_set_id', $prioritySetId);
        })->delete();

        // Add the new default
        self::create([
            'priority_set_issue_pair' => $issuePriorityId,
            'is_default' => true,
        ]);
    }

    /**
     * Update or create a default for a given priority set issue pair.
     *
     * @param int $prioritySetId The ID of the priority set.
     * @param int $issuePriorityId The ID of the issue priority.
     * @return void
     */
    public static function updateOrCreate($prioritySetId, $issuePriorityId, $isDefault = true)
    {
        self::updateOrCreate(
            ['priority_set_issue_pair' => $prioritySetId],
            ['issue_priority_id' => $issuePriorityId],
            ['is_default' => $isDefault]
        );
    }
}
