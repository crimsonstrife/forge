<?php

namespace App\Models;

use App\Enums\GoalCadence;
use App\Enums\GoalHealth;
use App\Enums\GoalStatus;
use App\Enums\GoalType;
use App\Observers\GoalObserver;
use App\Traits\IsPermissible;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
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
        'confidence' => 'int',
        'health' => GoalHealth::class,
        'cadence' => GoalCadence::class,
        'next_checkin_at' => 'datetime',
    ];

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }
    public function keyResults(): HasMany
    {
        return $this->hasMany(GoalKeyResult::class);
    }
    public function links(): HasMany
    {
        return $this->hasMany(GoalLink::class);
    }

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

    public function blockers(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'goal_dependencies', 'goal_id', 'depends_on_goal_id');
    }

    public function blocks(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'goal_dependencies', 'depends_on_goal_id', 'goal_id');
    }

    /** Quick access to all check-ins across KRs */
    public function checkins(): HasManyThrough
    {
        return $this->hasManyThrough(GoalCheckin::class, GoalKeyResult::class, 'goal_id', 'goal_key_result_id');
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

    /**
     * Recompute progress from KRs (weighted average 0..100).
     */
    public function recomputeProgress(): float
    {
        $krs = $this->keyResults()->get();
        $total = max($krs->sum('weight'), 1);
        $progress = $krs->sum(fn ($kr) => $kr->percentComplete() * ($kr->weight / $total));

        $this->forceFill(['progress' => round($progress, 2)])->saveQuietly();

        return (float) $this->progress;
    }

    /** Prevent cycles like A → B and B → A (or longer loops) */
    public function wouldCreateDependencyCycle(self $other): bool
    {
        if ($this->is($other)) {
            return true;
        }
        // DFS: does $other ultimately depend on $this ?
        $stack = [$other];
        $seen = [];
        while ($g = array_pop($stack)) {
            if ($g->id === $this->id) {
                return true;
            }
            if (isset($seen[$g->id])) {
                continue;
            }
            $seen[$g->id] = true;
            $g->loadMissing('blockers');
            foreach ($g->blockers as $b) {
                $stack[] = $b;
            }
        }
        return false;
    }

    public function setCycleFromDates(?DateTimeInterface $start, ?DateTimeInterface $end, string $fyStart = '01-01'): void
    {
        $this->cycle_start = $start?->format('Y-m-d');
        $this->cycle_end   = $end?->format('Y-m-d');

        // Derive cycle label like '2025-Q3' based on FY start month
        if ($start) {
            [$fyMonth, $fyDay] = explode('-', $fyStart); // e.g. '04-01' for April FY start
            $fyStartThisYear = Carbon::create($start->format('Y'), (int)$fyMonth, (int)$fyDay);
            if (Carbon::parse($start)->lt($fyStartThisYear)) {
                $fyStartThisYear->subYear();
            }
            $offsetMonths = Carbon::parse($start)->diffInMonths($fyStartThisYear);
            $quarter = intdiv($offsetMonths, 3) + 1;
            $fy = (int) $fyStartThisYear->format('Y');
            $this->cycle_label = sprintf('%d-Q%d', $fy + intdiv($offsetMonths, 12), min(max($quarter, 1), 4));
        }
    }

    public function scopeInCycle(Builder $q, string $label): Builder
    {
        return $q->where('cycle_label', $label);
    }

    /** Move next_checkin_at forward based on cadence */
    public function bumpNextCheckinAt(): void
    {
        $now = now();
        $next = match ($this->cadence) {
            GoalCadence::Weekly   => $now->copy()->addWeek(),
            GoalCadence::Biweekly => $now->copy()->addWeeks(2),
            GoalCadence::Monthly  => $now->copy()->addMonth(),
            default               => $now->copy()->addWeek(),
        };
        $this->forceFill(['next_checkin_at' => $next])->saveQuietly();
    }
}
