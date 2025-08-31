<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoalCheckin extends Model
{
    protected $fillable = ['goal_key_result_id','created_by','value','note'];
    protected $casts = ['value' => 'float'];

    public function keyResult(): BelongsTo { return $this->belongsTo(GoalKeyResult::class, 'goal_key_result_id'); }
}
