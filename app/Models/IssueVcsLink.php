<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class IssueVcsLink extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    /** @var array<int,string> */
    protected $fillable = [
        'issue_id',
        'repository_id',
        'type',
        'external_id',
        'name',
        'number',
        'url',
        'state',
        'payload',
        'linked_by_user_id',
    ];

    /** @return array<string,string> */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'id' => 'string',
            'issue_id' => 'string',
            'repository_id' => 'string',
            'linked_by_user_id' => 'string',
            'number' => 'integer',
        ];
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }
    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class);
    }
    public function linkedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'linked_by_user_id');
    }
}
