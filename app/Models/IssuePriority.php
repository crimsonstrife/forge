<?php

namespace App\Models;

use App\Traits\IsPermissible;
use Illuminate\Database\Eloquent\Model;

class IssuePriority extends Model
{
    use IsPermissible;

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
