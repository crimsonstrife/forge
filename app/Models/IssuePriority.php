<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssuePriority extends Model
{
    protected $fillable = [
        'name',
        'key',
        'order',
        'weight',
        'color',
        'icon'
    ];

    protected $casts = [
        'order' => 'int',
        'weight' => 'int',
    ];

    public function scopeOrdered($q)
    {
        return $q->orderBy('order');
    }

    public function scopeWeighting($q)
    {
        return $q->orderBy('weight');
    }
}
