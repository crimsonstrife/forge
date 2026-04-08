<?php

namespace App\Models;

use App\Traits\HasPermissionSets;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasPermissions;
    use HasPermissionSets;
    use HasProfilePhoto;
    use HasRoles;
    use HasTeams;
    use HasUuids;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'id' => 'string',
        ];
    }

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_user', 'user_id', 'project_id')
            ->withPivot(['role'])
            ->withTimestamps();
    }

    public function isCurrentTeam($team): bool
    {
        if ($team === null || $this->currentTeam === null) {
            return false;
        }

        return (string) $team->getKey() === (string) $this->currentTeam->getKey();
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function tourStates(): HasMany
    {
        return $this->hasMany(UserTourState::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasAnyPermission(
            ['is-super-admin', 'filament.access', 'is-admin', 'is-panel-user', 'admin.panel.access'],
            filament()->getAuthGuard()
        );
    }

    public function notifications(): MorphMany
    {
        /** @phpstan-ignore-next-line */
        return $this->morphMany(DatabaseNotification::class, 'notifiable');
    }

    public function followedIssues(): BelongsToMany
    {
        return $this->belongsToMany(Issue::class, 'issue_followers')
            ->withTimestamps();
    }

    public function issueNotificationPreference(): HasOne
    {
        return $this->hasOne(IssueNotificationPreference::class);
    }

    public function dashboardPreference(): HasOne
    {
        return $this->hasOne(DashboardPreference::class);
    }

    public function broadcastChannelName(): string
    {
        return 'App.Models.User.'.$this->getKey();
    }
}
