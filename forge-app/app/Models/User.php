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
use App\Traits\IsPermissible;
use App\Traits\HasAdvancedPermissions;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;
use ProtoneMedia\LaravelVerifyNewEmail\MustVerifyNewEmail;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\Models\Permission;
use App\Models\Auth\PermissionSet;
use App\Models\Auth\PermissionGroup;
use App\Models\Projects\Project;
use App\Models\Issues\Issue;
use App\Models\Issues\IssueHour;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;

/**
 * User Model
 *
 * Represents a user in the application.
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $creation_token
 * @property string $type
 * @property string $email_verified_at
 * @property string $discord_id
 * @property array $discord_roles
 * @property string $profile_photo_url
 * @package App\Models
 */
class User extends Authenticatable implements MustVerifyEmail, FilamentUser
{
    // Resolve the trait collision by aliasing methods
    use SanctumApiTokens, PassportApiTokens {
        // Alias conflicting methods from the Sanctum trait
        SanctumApiTokens::tokens as sanctumTokens;
        PassportApiTokens::tokens as passportTokens;
        SanctumApiTokens::tokenCan as sanctumTokenCan;
        PassportApiTokens::tokenCan as passportTokenCan;
        SanctumApiTokens::CreateToken as sanctumCreateToken;
        PassportApiTokens::CreateToken as passportCreateToken;
        SanctumApiTokens::withAccessToken as sanctumWithAccessToken;
        PassportApiTokens::withAccessToken as passportWithAccessToken;
    }
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasAdvancedPermissions, HasRoles {
        HasAdvancedPermissions::hasPermissionTo insteadof HasRoles;
    }
    use IsPermissible;
    use MustVerifyNewEmail;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'creation_token',
        'type',
        'email_verified_at',
        'discord_id',
        'discord_roles',
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
            'discord_roles' => 'array',
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
     * Find a user by their ID.
     *
     * @param int $id
     * @return User
     */
    public static function find($id)
    {
        return static::query()->findOrFail($id);
    }

    /**
     * Check if the user can do something.
     * Override the method from the Authorizable trait.
     *
     * @param string $ability
     * @param array<mixed> $arguments
     * @return bool
     */
    public function can($ability, $arguments = []): bool
    {
        // Check if the user has the 'is-super-admin' permission
        if ($this->isSuperAdmin()) {
            return true;
        }

        // check the supplied arguments
        if (empty($arguments)) {
            return $this->checkPermissionTo($ability, 'web');
        } else {
            // we have arguments, so we need to check the permission with the arguments
            return $this->checkPermissionTo($ability); // TODO: Implement logic to check if the user has the permission with the supplied arguments
        }
    }

    /**
     * Check if the user can access Filament.
     * @return bool
     */
    public function canAccessFilament(): bool
    {
        // Check if the user has the 'access-filament' permission
        return $this->hasPermissionTo('access-filament') || $this->isSuperAdmin();
    }

    /**
     * Check if the user can access the Filament panel.
     *
     * @param \Filament\Panel $panel
     * @return bool
     */
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        // Check if the user has the 'access-filament' permission
        if ($this->canAccessFilament()) {
            // Check if the user has the 'access-panel' permission for the given panel
            return true; // TODO: Implement logic to check if the user has access to a specific panel
        }

        return false;
    }

    /**
     * This method is required by Filament to return the user's display name.
     *
     * @return string
     */
    public function getFilamentName(): string
    {
        // Ensure this returns a valid string, like the user's username, full name, or email address
        return $this->username ?? $this->email ?? $this->getFullNameAttribute();
    }

    /**
     * This method is required by some vendors to return the user's email address.
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * This method is required by some vendors to return the user's username.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
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
     * Get the projects that the user is a member of.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_users', 'user_id', 'project_id')->withPivot(['role']);
    }

    /**
     * Get the projects that the user has starred (is a favorite).
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favoriteProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_favorites', 'user_id', 'project_id')->withTimestamps();
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
                return $this->hours()->sum('value'); // get the sum of all the hours logged by the user in the issue hours table
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
            return $this->hasPermissionTo($ability, $guard);
        } catch (PermissionDoesNotExist $e) {
            //Log the error
            Log::error('Permission does not exist: ' . $e->getMessage());

            return null;
        }
    }

    public function tokens()
    {
        // Try to get the tokens from Passport first
        $tokens = $this->passportTokens();

        // If there are no tokens found, or there is an error, try to get the tokens from Sanctum
        $tokensCollection = $tokens->get();
        if ($tokensCollection->isEmpty()) {
            $tokens = $this->sanctumTokens();
        }

        return $tokens;
    }

    public function tokenCan($ability)
    {
        // Try to check the token ability with Passport first
        $can = $this->passportTokenCan($ability);

        // If there is an error, or the token does not have the ability, try to check the token ability with Sanctum
        if (!$can) {
            $can = $this->sanctumTokenCan($ability);
        }

        return $can;
    }

    public function createToken($name, array $abilities = [])
    {
        // Try to create a token with Passport first
        $token = $this->passportCreateToken($name, $abilities);

        // If there is an error, or the token is not created, try to create a token with Sanctum
        if (empty($token)) {
            $token = $this->sanctumCreateToken($name, $abilities);
        }

        return $token;
    }

    public function withAccessToken($accessToken)
    {
        // Try to set the access token with Passport first
        $this->passportWithAccessToken($accessToken);

        // If there is an error, or the access token is not set, try to set the access token with Sanctum
        if ($this->accessToken === null) {
            $this->sanctumWithAccessToken($accessToken);
        }

        return $this;
    }

    /**
     * Check if the user is an admin.
     * @return bool
     */
    public function isAdmin(): bool
    {
        // Check if the user has the 'is-admin' permission
        return $this->hasPermissionTo('is-admin');
    }

    /**
     * Check if the user is a super admin.
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        // Check if the user has the 'is-super-admin' permission
        return $this->hasPermissionTo('is-super-admin');
    }

    /**
     * A user may be given various permissions.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_has_permissions', 'user_id', 'permission_id');
    }

    /**
     * A user may be assigned to various permission sets.
     */
    public function permissionSets(): BelongsToMany
    {
        return $this->belongsToMany(PermissionSet::class, 'permission_set_user', 'user_id', 'permission_set_id');
    }

    /**
     * A user may be assigned to various permission groups.
     */
    public function permissionGroups(): BelongsToMany
    {
        return $this->belongsToMany(PermissionGroup::class, 'permission_group_user', 'user_id', 'permission_group_id');
    }

    /**
     * Get the dashboards owned by the user.
     */
    public function dashboards()
    {
        return $this->belongsToMany(Dashboard::class, 'user_dashboard', 'user_id', 'dashboard_id')->withTimestamps();
    }

    /**
     * Get all reports accessible to the user (via dashboards).
     */
    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    /**
     * Get the full name of the user.
     * @return string
     */
    public function getFullNameAttribute(): string
    {
        return ($this->getFirstNameAttribute()) . ' ' . ($this->getLastNameAttribute());
    }

    /**
     * Get the first name of the user.
     * @return string
     */
    public function getFirstNameAttribute(): string
    {
        return ($this->attributes['first_name']);
    }

    /**
     * Get the last name of the user.
     * @return string
     */
    public function getLastNameAttribute(): string
    {
        return ($this->attributes['last_name']);
    }

    /**
     * Get the user's username.
     * @return string
     */
    public function getUsernameAttribute(): string
    {
        return ($this->attributes['username']);
    }

    /**
     * Get the user's email address.
     * @return string
     */
    public function getEmailAttribute(): string
    {
        return ($this->attributes['email']);
    }

    /**
     * Get the name attribute, used by some vendors.
     * @return string
     */
    public function getNameAttribute(): string
    {
        return $this->getFullNameAttribute() ?: $this->getUsernameAttribute() ?: $this->getEmailAttribute();
    }

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = [])
    {
        // Check if the user is being created
        if ($this->isCreating()) {
            // Check if the user is being created with a password
            if ($this->isCreatingWithPassword()) {
                // password should be hashed already, this is for additional tasks that need to be done before saving
            }
        }

        return parent::save($options);
    }

    /**
     * Check if the user is being created.
     * @return bool
     */
    public function isCreating(): bool
    {
        return $this->exists === false;
    }

    /**
     * Check if the user is being created with a password.
     * @return bool
     */
    public function isCreatingWithPassword(): bool
    {
        return $this->isCreating() && !empty($this->password);
    }


    /**
     * Get up to (X, default of 5) of the authenticated user's most recently viewed projects.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recentProjects($limit = 5)
    {
        return $this->belongsToMany(Project::class, 'project_views')
                ->withPivot('updated_at')
                ->orderByPivot('updated_at', 'desc');
    }
}
