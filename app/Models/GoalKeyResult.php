<?php

namespace App\Models;

use App\Enums\KRAutomation;
use App\Enums\KRDirection;
use App\Enums\MetricUnit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoalKeyResult extends Model
{
    protected $fillable = [
        'goal_id','name','unit','direction','initial_value','current_value',
        'target_min','target_max','target_value','automation','weight',
    ];

    protected $casts = [
        'unit' => MetricUnit::class,
        'direction' => KRDirection::class,
        'automation' => KRAutomation::class,
        'initial_value' => 'float',
        'current_value' => 'float',
        'target_min' => 'float',
        'target_max' => 'float',
        'target_value' => 'float',
        'weight' => 'int',
    ];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }
    public function checkins(): HasMany
    {
        return $this->hasMany(GoalCheckin::class);
    }

    public function percentComplete(): float
    {
        $dir = $this->direction;
        $cur = $this->current_value;
        $from = $this->initial_value;

        if ($dir === KRDirection::MaintainBetween) {
            if ($this->target_min === null || $this->target_max === null) {
                return 0.0;
            }
            if ($cur < $this->target_min) {
                return 0.0;
            }
            if ($cur > $this->target_max) {
                return 0.0;
            }
            return 100.0;
        }

        if ($this->target_value === null) {
            return 0.0;
        }

        $distance = max(abs($this->target_value - $from), 1e-8);
        $progress = match ($dir) {
            KRDirection::IncreaseTo => ($cur - $from) / $distance,
            KRDirection::DecreaseTo => ($from - $cur) / $distance,
            KRDirection::HitExact   => 1.0 - min(abs($cur - $this->target_value) / $distance, 1.0),
            default => 0.0,
        };

        return max(0.0, min(100.0, $progress * 100.0));
    }
}
