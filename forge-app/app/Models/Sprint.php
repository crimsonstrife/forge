<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Sprint extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'project_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    public static function boot()
    {
        parent::boot();

        static::created(function (Sprint $item) {
            $epic = Epic::create([
                'name' => $item->name,
                'start_date' => $item->start_date,
                'end_date' => $item->end_date,
                'project_id' => $item->project_id
            ]);
            $item->epic_id = $epic->id;
            $item->save();
        });
    }

    /**
     * Sprint belongs to a Project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Sprint has many Issues
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    /**
     * Sprint belongs to an Epic
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function epic(): BelongsTo
    {
        return $this->belongsTo(Epic::class);
    }

    /**
     * Get the remaining days of the sprint
     * @return Attribute | null
     */
    public function remaining(): Attribute
    {
        return new Attribute(
            get: function () {
                if ($this->start_date && $this->end_date && $this->started_at && !$this->ended_at) {
                    return $this->end_date->diffInDays(now()) + 1;
                }
                return null;
            }
        );
    }

    /**
     * Get the progress of the sprint
     * @return Attribute | null
     */
    public function progress(): Attribute
    {
        return new Attribute(
            get: function () {
                if ($this->start_date && $this->end_date && $this->started_at && !$this->ended_at) {
                    $totalDays = $this->start_date->diffInDays($this->end_date) + 1;
                    $remainingDays = $this->end_date->diffInDays(now()) + 1;
                    return 100 - ($remainingDays / $totalDays) * 100;
                }
                return null;
            }
        );
    }
}
