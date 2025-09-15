<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class GoalLink extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['goal_id','linkable_type','linkable_id','weight'];
    protected $casts = ['weight' => 'int'];

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }
    public function linkable(): MorphTo
    {
        return $this->morphTo();
    }
}
