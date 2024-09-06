<?php

namespace App\Models;

use App\Notifications\UserCreatedNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens as SanctumApiTokens;
use Laravel\Passport\HasApiTokens as PassportApiTokens;
use App\Models\PermissionGroup;
use App\Traits\HasRoles;
use App\Traits\IsPermissable;
use App\Traits\HasPermissions as HasPermissions;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;
use ProtoneMedia\LaravelVerifyNewEmail\MustVerifyNewEmail;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

/**
 * User Model
 *
 * Represents a user in the application.
 * @property int $id
 * @property string $first-name
 * @property string $last-name
 * @property string $username
 * @package App\Models
 */
class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    use SanctumApiTokens;
    use PassportApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use IsPermissable;
    use HasPermissions;
    use MustVerifyNewEmail;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first-name',
        'last-name',
        'username',
        'email',
        'password',
        'creation_token',
        'type',
        'email_verified_at',
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
            // file deepcode ignore HardcodedPassword: <this is not a hardcoded password, it's a cast to a hashed value.>
            'password' => 'hashed',
        ];
    }

    /**
     * Add a boot for the model.
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function (User $item) {
            if ($item->type == 'db') {
                $item->password = bcrypt(uniqid());
                $item->creation_token = Uuid::uuid4()->toString();
            }
        });

        static::created(function (User $item) {
            if ($item->type == 'db') {
                $item->notify(new UserCreatedNotification($item));
            }
        });
    }

    /**
     * Check if the user can access Filament.
     * @return bool
     */
    public function canAccessFilament(): bool
    {
        return true; // return true if the user can access Filament TODO: implement this
    }

    /**
     * Check if the user can access the Filament panel.
     *
     * @param \Filament\Panel $panel
     * @return bool
     */
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return true; // return true if the user can access the panel TODO: implement this
    }

    /**
     * Get the permission groups that the user belongs to.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissionGroups()
    {
        return $this->belongsToMany(PermissionGroup::class, 'user_permission_group');
    }

    /**
     * Get the permissions that the user has.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_has_permission');
    }

    /**
     * Get the projects that the user has created.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function projectsOwning(): HasMany
    {
        return $this->hasMany(Project::class, 'owner_id', 'id');
    }

    /**
     * Get the projects that the user has affected/contributed to.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function projectsAffected(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_users', 'user_id', 'project_id')->withPivot(['role']);
    }

    /**
     * Get the projects that the user has starred (is a favorite).
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favoriteProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_favorites', 'user_id', 'project_id');
    }

    /**
     * Get the issues that the user has created.\
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function issuesOwned(): HasMany
    {
        return $this->hasMany(Issue::class, 'owner_id', 'id');
    }

    /**
     * Get the issues that the user is responsible for/has assigned to them.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function issuesResponsible(): HasMany
    {
        return $this->hasMany(Issue::class, 'responsible_id', 'id');
    }

    /**
     * Get the issue hours that the user has logged.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hours(): HasMany
    {
        return $this->hasMany(IssueHour::class, 'user_id', 'id');
    }

    /**
     * Get the total number of hours that the user has logged.
     * Returns a sum
     */
    public function totalLoggedInHours(): Attribute
    {
        return new Attribute(
            get: function () {
                return $this->hours->sum('value'); // get the sum of all the hours logged by the user in the issue hours table
            }
        );
    }

    /**
     * Get the user ID.
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Check if the user has permission to perform the given action.
     *
     * @param string $ability
     * @param mixed|null $guard
     * @return bool|null
     * @throws PermissionDoesNotExist
     */
    public function checkPermissionTo(string $ability, mixed $guard = null): ?bool
    {
        try {
            return HasPermissions::hasPermissionTo($ability, $guard);
        } catch (PermissionDoesNotExist $e) {
            // Log the error
            // Log::error($e->getMessage());

            return null;
        }
    }

    /**
     * Give the user permission to perform the given action.
     *
     * @param string $ability
     * @param mixed|null $guard
     * @return bool|null
     * @throws PermissionDoesNotExist
     */
    public function providePermissionTo(string $ability, mixed $guard = null): ?bool
    {
        try {
            // Check if the user already has the permission
            if ($this->checkPermissionTo($ability, $guard)) {
                return true;
            }

            // Assign the permission to the user
            return HasPermissions::givePermissionTo($ability, $guard);
        } catch (PermissionDoesNotExist $e) {
            // Log the error
            // Log::error($e->getMessage());

            return null;
        }
    }
}
