<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IssueCodexLink extends Model
{
    use HasUuids;

    protected $fillable = [
        'issue_id',
        'codex_page_id',
        'codex_page_title',
        'codex_page_url',
        'codex_workspace_id',
        'codex_workspace_slug',
        'added_by_id',
    ];

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by_id');
    }
}
