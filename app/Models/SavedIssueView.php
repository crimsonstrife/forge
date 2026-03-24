<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SavedIssueView extends Model
{
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'team_id',
        'name',
        'slug',
        'query',
        'filters',
        'sort',
        'is_shared',
    ];

    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'is_shared' => 'bool',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /** @return Builder<static> */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $query->where(function (Builder $builder) use ($user): void {
            $builder->where('user_id', $user->id);

            if ($user->current_team_id) {
                $builder->orWhere(function (Builder $shared) use ($user): void {
                    $shared
                        ->where('is_shared', true)
                        ->where('team_id', $user->current_team_id);
                });
            }
        });
    }

    public function isOwnedBy(User $user): bool
    {
        return (string) $this->user_id === (string) $user->id;
    }
}
