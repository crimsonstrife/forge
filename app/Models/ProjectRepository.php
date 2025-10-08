<?php

namespace App\Models;

use App\Utilities\TokenUtils;
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


    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class);
    }
    public function integrator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'integrator_user_id');
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::uuid();
        });
    }

    /**
     * Ignore masked or whitespace-only values from forms; trim valid input.
     */
    public function setTokenAttribute(?string $value): void
    {
        if ($value === null) {
            $this->attributes['token'] = null;
            return;
        }

        $v = trim($value);

        if (TokenUtils::isMaskedToken($v)) {
            return;
        }

        $this->attributes['token'] = $v;
    }

    /**
     * Basic GitHub token shape check.
     */
    public function isLikelyGithubToken(?string $token): bool
    {
        if (!$token) {
            return false;
        }

        $t = trim($token);

        return (bool) preg_match('/^(github_pat|ghp|gho|ghu|ghs|ghr)_/i', $t) && strlen($t) >= 20;
    }

    /**
     * Select a usable token, ignoring stale installation tokens and masked junk.
     */
    public function effectiveToken(string $provider): ?string
    {
        $primary = $this->token ? trim((string) $this->token) : null;

        if ($this->token_type === 'installation' && $this->token_expires_at !== null && now()->greaterThan($this->token_expires_at->subMinutes(2))) {
            $primary = null; // stale; do not use
        }

        if ($this->isLikelyGithubToken($primary)) {
            return $primary;
        }

        $fallback = trim((string) optional(
            $this->integrator?->socialAccounts->firstWhere('provider', $provider)
        )->token);

        return $this->isLikelyGithubToken($fallback) ? $fallback : null;
    }
}
