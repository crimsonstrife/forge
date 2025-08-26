<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssueStatus extends Model
{
    protected $fillable = [
        'name',
        'key',
        'order',
        'color',
        'is_done'
    ];

    protected $casts = [
        'is_done' => 'bool',
        'order' => 'int'
    ];

    public function scopeDone($q){
        return $q->where('is_done', true);
    }

    public function scopeOrdered($q){
        return $q->orderBy('order');
    }
}
