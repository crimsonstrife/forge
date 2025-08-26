<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssuePriority extends Model
{
    protected $fillable = [
        'name',
        'order',
        'color'
    ];

    protected $casts = [
        'order' => 'int',
    ];

    public function scopeOrdered($q){ return $q->orderBy('order'); }
}
