<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property string $issue_link_type_id
 * @property string $from_issue_id
 * @property string $to_issue_id
 * @property array|null $properties
 *
 * @property-read IssueLinkType $type
 * @property-read Issue $from
 * @property-read Issue $to
 */
class IssueLink extends Model
{
    use HasUuids;

    protected $table = 'issue_links';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'issue_link_type_id', 'from_issue_id', 'to_issue_id', 'created_by_id', 'properties',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(IssueLinkType::class, 'issue_link_type_id');
    }

    public function from(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'from_issue_id');
    }

    public function to(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'to_issue_id');
    }
}
