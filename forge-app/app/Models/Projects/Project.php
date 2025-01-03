<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Xetaio\Mentions\Models\Traits\HasMentionsTrait as HasMentions;
use App\Models\User;
use App\Models\Issues\Issue;
use App\Models\Story;
use App\Models\Epic;
use App\Models\Sprint;
use App\Models\PrioritySet as IssuePrioritySet;
use App\Models\Projects\ProjectStatus;
use App\Models\Projects\ProjectType;
use App\Models\Projects\ProjectRepository;
use App\Models\PivotModels\ProjectFavorite;
use App\Models\PivotModels\ProjectUser;
use App\Models\Tag;
use App\Traits\IsPermissible;
use App\Services\CrucibleService;

/**
 * Class Project
 * Model for the Project table
 * @property string name
 * @property string description
 * @property int status_id
 * @property int owner_id
 * @property string issue_prefix
 * @property string status_type
 * @property string type
 * @property string start_date
 * @property string end_date
 * @property int user_id
 * @property int repository_id
 * @property int view_count
 * @package App\Models\Projects
 */
class Project extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;
    use HasMentions;
    use IsPermissible;

    protected $fillable = [
        'name',
        'description',
        'status_id',
        'owner_id',
        'issue_prefix',
        'status_type',
        'type',
        'start_date',
        'end_date',
        'user_id',
        'repository_id',
        'view_count'
    ];

    protected $dates = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    protected $appends = [
        'icon',
        'color',
        'font_color',
    ];

    protected $with = [
        'owner',
        'status',
        'type',
        'repository',
        'users',
        'prioritySet',
    ];

    protected $casts = [
        'view_count' => 'integer',
    ];

    /**
     * Get the Owning User of the Project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the Status of the Project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(ProjectStatus::class, 'status_id')->withDefault()->withTrashed();
    }

    /**
     * Get the Project Type of the Project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(ProjectType::class, 'type_id');
    }

    /**
     * Get the Repository of the Project, if the Project has a Repository.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne | null
     */
    public function repository(): HasOne
    {
        return $this->hasOne(ProjectRepository::class);
    }

    /**
     * Get the Users of the Project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_users')
            ->using(ProjectUser::class)
            ->withPivot(['role_id'])
            ->withTimestamps();
    }

    /**
     * Check if the project has a specific user.
     *
     * @param int $userId The ID of the user to check.
     * @return bool True if the user is associated with the project, false otherwise.
     */
    public function hasUser($userId): bool
    {
        return $this->users()->where('user_id', $userId)->exists();
    }

    /**
     * Get the Issues of the Project
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    /**
     * Get the Priority Set of the Project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prioritySet()
    {
        return $this->belongsTo(PrioritySet::class);
    }

    /**
     * Get the Priorities available to Issues of the Project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function availablePriorities()
    {
        return $this->prioritySet ? $this->prioritySet->priorities() : collect();
    }

    /**
     * Get the Default Priority for Issues of the Project
     * Default Priority is the Priority that is set as the default in the Priority Set
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function defaultPriority()
    {
        return $this->prioritySet ? $this->prioritySet->defaultPriority() : null;
    }

    /**
     * Get the Stories of the Project
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stories(): HasMany
    {
        return $this->hasMany(Story::class, 'project_id', 'id');
    }

    /**
     * Get the Statuses of the Project
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statuses(): HasMany
    {
        return $this->hasMany(ProjectStatus::class);
    }

    /**
     * Get the Epics of the Project
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function epics(): HasMany
    {
        return $this->hasMany(Epic::class);
    }

    /**
     * Get the Sprints of the Project
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sprints(): HasMany
    {
        return $this->hasMany(Sprint::class);
    }

    /**
     * Get the first date of all the Epics.
     * @return Attribute
     */
    public function getEpicFirstDate(): Attribute
    {
        return new Attribute(
            get: function () {
                $firstEpic = $this->epics()->orderBy('start_date')->first();
                if ($firstEpic) {
                    return $firstEpic->start_date;
                }
                return now();
            }
        );
    }

    /**
     * Get the last date of all the Epics.
     * @return Attribute
     */
    public function getEpicLastDate(): Attribute
    {
        return new Attribute(
            get: function () {
                $firstEpic = $this->epics()->orderBy('end_date', 'desc')->first();
                if ($firstEpic) {
                    return $firstEpic->end_date;
                }
                return now();
            }
        );
    }

    /**
     * Get the Contributors of the Project
     * @return Attribute
     */
    public function contributors(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->users->push($this->owner)->unique('id') // Add the owner to the contributors list
        );
    }

    /**
     * Get the Repository URL of the Project, if the Project has a Repository.
     * @return Attribute
     */
    public function repositoryUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->repository?->url
        );
    }

    /**
     * Get the View Count of the Project
     * @return Attribute
     */
    public function viewCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->view_count
        );
    }

    /**
     * Get the Color of the Project based on the Project Type
     * @return Attribute
     */
    public function color(): Attribute
    {
        return new Attribute(
            get: function () {
                //local variable to store the color
                $color = '';

                //get the project type color
                $color = optional($this->type)->getColor() ?? '#3f84f3'; //default color if the project type does not have a color

                //trim the pound sign/hash (#) from the color, if it is a hexcode
                $color = ltrim($color, '#');

                //set the attribute value
                $this->attributes['color'] = $color;

                //return the color
                return $color;
            }
        );
    }

    /**
     * Get the Icon/Avatar of the Project
     * If the Project has an icon, return the icon else return a generated avatar from the project name and the type color
     * @return Attribute
     */
    public function icon(): Attribute
    {
        //get the color from the project type
        $this->color();

        //local variable to store font color
        $fontColor = '';

        //get the project color
        $color = $this->attributes['color'];

        //get the font color based on the background color, to make the text readable (experimental)
        if (hexdec($color) > 0xffffff / 2) {
            $fontColor = '#000000';
        } else {
            $fontColor = '#ffffff';
        }

        //trim the pound sign/hash (#) from the color, if it is a hexcode
        $color = ltrim($color, '#');
        $fontColor = ltrim($fontColor, '#');

        //set the attribute value for font color
        $this->attributes['font_color'] = $fontColor;

        //make sure the color attribute is still set
        $this->attributes['color'] = $color;

        return new Attribute(
            //get the project icon
            get: fn () => $this->media('icon')?->first()?->getFullUrl()
                ??
                'https://ui-avatars.com/api/?background=' . $this->color . '&color=' . $this->font_color . '&name=' . $this->name
        );
    }

    /**
     * Get the Current Sprint of the Project
     * @return Attribute
     */
    public function currentSprint(): Attribute
    {
        return new Attribute(
            get: fn () => $this->sprints()
                ->whereNotNull('started_at')
                ->whereNull('ended_at')
                ->first()
        );
    }

    /**
     * Get the Next Sprint of the Project
     * @return Attribute | null
     */
    public function nextSprint(): Attribute | null
    {
        return new Attribute(
            get: function () {
                if ($this->currentSprint) {
                    return $this->sprints()
                        ->whereNull('started_at')
                        ->whereNull('ended_at')
                        ->where('start_date', '>=', $this->currentSprint->ends_at)
                        ->orderBy('start_date')
                        ->first();
                }
                return null;
            }
        );
    }

    /**
     * Get the Previous Sprint of the Project
     * @return Attribute | null
     */
    public function previousSprint(): Attribute | null
    {
        return new Attribute(
            get: function () {
                if ($this->currentSprint) {
                    return $this->sprints()
                        ->whereNotNull('ended_at')
                        ->where('ended_at', '<=', $this->currentSprint->started_at)
                        ->orderBy('start_date', 'desc')
                        ->first();
                }
                return null;
            }
        );
    }

    /**
     * Get the Project's Progress
     * @return Attribute | float
     */
    public function progress(): Attribute | float
    {
        return new Attribute(
            get: function () {
                $totalStories = $this->stories()->count();
                $completedStories = $this->stories()->where('status_id', 3)->count(); // 3 is the ID of the Completed status, you can change this to the ID of your completed status
                if ($totalStories > 0) {
                    return ($completedStories / $totalStories) * 100;
                }
                return 0;
            }
        );
    }

    /**
     * Get the Project's Progress in Percentage
     * @return Attribute | string
     */
    public function progressPercentage(): Attribute | string
    {
        return new Attribute(
            get: function () {
                //local variable to store the progress
                $progress = 0;

                //get the project progress
                $progress = $this->progress;

                //make sure the progress is a whole number, and not a decimal/fraction
                $progress = round($progress);

                //convert to a string
                $progress = (string) $progress;

                //append the percentage sign and return the progress
                return $progress . '%';
            }
        );
    }

    /** Crucible-Related Methods **/

    /**
     * Sync repository with Crucible.
     *
     * @param string $url
     * @return bool
     */
    public function syncRepository(string $url): bool
    {
        $crucibleService = app(CrucibleService::class);
        $repositoryData = $crucibleService->fetchAndCacheRepository($url);

        if ($repositoryData) {
            $this->repository()->updateOrCreate([], $repositoryData->toArray());
            return true;
        }

        return false;
    }

    /**
     * Check if repository exists in Crucible.
     *
     * @return bool
     */
    public function hasValidRepository(): bool
    {
        return !is_null($this->repository) && app(CrucibleService::class)->verifyRepository($this->repository->url);
    }

    /**
     * Get the users who have favorited this project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'project_favorites', 'project_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Check if the project is favorited by the given user.
     *
     * @param User $user The user to check against.
     * @return bool True if the project is favorited by the user, false otherwise.
     */
    public function isFavoritedBy(User $user): bool
    {
        return $this->favoritedBy()->where('user_id', $user->id)->exists();
    }

    /**
     * Get the tags associated with the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'project_has_tags', 'project_id', 'tag_id')->withTimestamps();
    }

    /**
     * Scope a query to include projects with specific tags.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array|string $tags
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithTags($query, $tags)
    {
        return $query->whereHas('tags', function ($q) use ($tags) {
            $q->whereIn('id', $tags);
        });
    }

    /**
     * Get the teams that belong to the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'project_teams', 'project_id', 'team_id')
            ->withTimestamps();
    }

    /**
     * Check if the project has a team with the given team ID.
     *
     * @param int $teamId The ID of the team to check.
     * @return bool True if the team is associated with the project, false otherwise.
     */
    public function hasTeam($teamId): bool
    {
        return $this->teams()->where('team_id', $teamId)->exists();
    }

    /**
     * Get the roles associated with the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roles()
    {
        return $this->hasMany(ProjectRole::class);
    }

    /**
     * Get the permissions associated with the project.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permissions()
    {
        return $this->hasMany(ProjectPermission::class);
    }
}
