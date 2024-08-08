<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class IssuePriority extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'color',
        'icon',
        'is_default'
    ];

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class, 'priority_id', 'id')->withTrashed();
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeNotDefault($query)
    {
        return $query->where('is_default', false);
    }
}
