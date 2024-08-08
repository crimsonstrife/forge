<?php

namespace App\Models;

use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueHour extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'issue_id',
        'value',
        'comment',
        'activity_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'issue_id', 'id');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'id');
    }

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
