<?php

namespace App\Models;

use App\Traits\IsPermissible;
use Illuminate\Database\Eloquent\Model;

class IssueType extends Model
{
    use IsPermissible;

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
