<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class IssueType extends Model
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
        return $this->hasMany(Issue::class, 'type_id', 'id')->withTrashed();
    }
}
