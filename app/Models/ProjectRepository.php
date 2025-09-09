<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ProjectRepository extends Model
{
    use HasUuids;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $casts = [
        'id' => 'string',
        'token' => 'encrypted',
        'token_expires_at'           => 'datetime',
        'initial_import_started_at'  => 'datetime',
        'initial_import_finished_at' => 'datetime',
    ];

    protected $fillable = [
        'project_id',
        'repository_id',
        'integrator_user_id',
        'token',
        'token_type',
        'token_expires_at',
        'initial_import_started_at',
        'initial_import_finished_at',
        'last_sync_status',
        'last_sync_error',
    ];


    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function repository(): BelongsTo { return $this->belongsTo(Repository::class); }
    public function integrator(): BelongsTo { return $this->belongsTo(User::class, 'integrator_user_id'); }

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::uuid();
        });
    }
}
