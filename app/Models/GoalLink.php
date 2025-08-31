<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GoalLink extends Model
{
    protected $fillable = ['goal_id','linkable_type','linkable_id','weight'];
    protected $casts = ['weight' => 'int'];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }
    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }
}
