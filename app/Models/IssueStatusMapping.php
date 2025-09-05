<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueStatusMapping extends Model
{
    protected $fillable = ['repository_id','provider','external_state','issue_status_id'];
    protected $casts = ['id' => 'string'];

    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class);
    }
    public function status(): BelongsTo
    {
        return $this->belongsTo(IssueStatus::class, 'issue_status_id');
    }
}
