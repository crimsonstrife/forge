<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class IssueExternalRef extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'issue_id','repository_id','provider','external_issue_id','number','url','state','payload'
    ];
    protected $casts = [
        'payload' => 'array',
        'id' => 'string',
    ];

    public function issue(): BelongsTo { return $this->belongsTo(Issue::class); }
    public function repository(): BelongsTo { return $this->belongsTo(Repository::class); }

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::uuid();
        });
    }
}
