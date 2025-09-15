<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class GoalCheckin extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::uuid();
        });
    }

    protected $fillable = ['goal_key_result_id','created_by','value','note'];
    protected $casts = ['value' => 'float'];

    public function keyResult(): BelongsTo { return $this->belongsTo(GoalKeyResult::class, 'goal_key_result_id'); }
}
