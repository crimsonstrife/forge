<?php

namespace App\Models\Issues;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\IsPermissible;

/**
 * Class IssueRelation
 *
 * This class represents the IssueRelation model, which is responsible for managing the relationships between issues.
 */
class IssueRelation extends Model
{
    use HasFactory;
    use IsPermissible;

    protected $fillable = [
        'issue_id',
        'type',
        'relation_id',
        'sort'
    ];

    /**
     * Get the issue associated with the issue relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'issue_id', 'id');
    }

    /**
     * Get the related issue for this relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function relation(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'relation_id', 'id');
    }
}
