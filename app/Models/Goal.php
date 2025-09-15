<?php

namespace App\Models;

use App\Enums\GoalStatus;
use App\Enums\GoalType;
use App\Observers\GoalObserver;
use App\Traits\IsPermissible;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * @property-read Collection<int, GoalKeyResult> $keyResults
 */
#[ObservedBy([GoalObserver::class])]
class Goal extends Model
{
    use HasFactory;
    use IsPermissible;

    protected $keyType = 'string';
    public $incrementing = false;

    /** @return array<class-string<EloquentModel>> */
    public static function allowedLinkableTypes(): array
    {
        return [
            Project::class,
            Issue::class,
            // Extend here later (e.g., Repository::class, Milestone::class, etc.)
        ];
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function (self $model): void {
            $model->id = $model->id ?: (string) Str::uuid();
        });
    }

    protected $fillable = [
        'owner_type','owner_id','parent_id','name','description','goal_type','status',
        'start_date','due_date','progress','created_by','meta',
    ];

    protected $casts = [
        'goal_type' => GoalType::class,
        'status' => GoalStatus::class,
        'start_date' => 'date',
        'due_date' => 'date',
        'meta' => 'array',
        'progress' => 'float',
    ];

    public function owner(): MorphTo { return $this->morphTo(); }
    public function parent(): BelongsTo { return $this->belongsTo(self::class, 'parent_id'); }
    public function children(): HasMany { return $this->hasMany(self::class, 'parent_id'); }
    public function keyResults(): HasMany { return $this->hasMany(GoalKeyResult::class); }
    public function links(): HasMany { return $this->hasMany(GoalLink::class); }

    /** @return MorphToMany<Project> */
    public function projects(): MorphToMany
    {
        return $this->morphedByMany(Project::class, 'linkable', 'goal_links');
    }

    /** @return MorphToMany<Issue> */
    public function issues(): MorphToMany
    {
        return $this->morphedByMany(Issue::class, 'linkable', 'goal_links');
    }

    /**
     * Link any allowed model to this goal.
     *
     * @param EloquentModel $model
     * @param int $weight
     * @return GoalLink
     */
    public function linkTo(EloquentModel $model, int $weight = 0): GoalLink
    {
        abort_unless(in_array($model::class, self::allowedLinkableTypes(), true), 400, 'Unsupported link type.');

        /** @var GoalLink $link */
        $link = $this->links()
            ->firstOrCreate(
                ['linkable_type' => $model::class, 'linkable_id' => (string) $model->getKey()],
                ['weight' => $weight]
            );

        return $link;
    }

    /**
     * Unlink a model from this goal.
     *
     * @param EloquentModel $model
     * @return int
     */
    public function unlinkFrom(EloquentModel $model): int
    {
        return $this->links()
            ->where('linkable_type', $model::class)
            ->where('linkable_id', $model->getKey())
            ->delete();
    }
}
