<?php

namespace App\Models;

use App\Traits\IsPermissible;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class IssueStatus extends Model
{
    use IsPermissible;

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

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_issue_statuses')
            ->withTimestamps();
    }
}
