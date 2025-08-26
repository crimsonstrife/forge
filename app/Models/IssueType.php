<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IssueType extends Model
{
    protected $fillable = [
        'name',
        'key',
        'is_hierarchical',
        'is_default',
        'icon'
    ];

    protected $casts = [
        'is_hierarchical'=>'bool',
        'is_default' => 'bool'
    ];
}
