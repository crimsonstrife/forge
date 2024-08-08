<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueRelation extends Model
{
    use HasFactory;

    protected $fillable = [
        'issue_id',
        'type',
        'relation_id',
        'sort'
    ];

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'issue_id', 'id');
    }

    public function relation(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'relation_id', 'id');
    }
}
