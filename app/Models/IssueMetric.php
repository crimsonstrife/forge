<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $issue_id
 * @property string $project_id
 * @property Carbon|null $first_started_at
 * @property Carbon|null $first_done_at
 * @property int $lead_time_min
 * @property int $cycle_time_min
 * @property int $age_min
 * @property int|null $current_status_id
 * @property bool $is_done
 */
class IssueMetric extends Model
{
    protected $table = 'issue_metrics';
    protected $primaryKey = 'issue_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            'first_started_at' => 'datetime',
            'first_done_at' => 'datetime',
            'is_done' => 'bool',
        ];
    }

    protected $fillable = [
        'issue_id','project_id',
        'first_started_at','first_done_at',
        'lead_time_min','cycle_time_min','age_min',
        'current_status_id','is_done',
    ];
}
