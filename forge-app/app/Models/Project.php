<?php

namespace App\Models;

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

class Project extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;

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
        'has_repository',
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

    /**
     * Get the Owning User of the Project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }

    /**
     * Get the Status of the Project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(ProjectStatus::class, 'status_id', 'id')->withTrashed();
    }

    /**
     * Get the Users of the Project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_users', 'project_id', 'user_id')->withPivot('project_role');
    }

    /**
     * Get the Issues of the Project
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class, 'project_id', 'id');
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
        return $this->hasMany(ProjectStatus::class, 'project_id', 'id');
    }

    /**
     * Get the Epics of the Project
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function epics(): HasMany
    {
        return $this->hasMany(Epic::class, 'project_id', 'id');
    }

    /**
     * Get the Sprints of the Project
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sprints(): HasMany
    {
        return $this->hasMany(Sprint::class, 'project_id', 'id');
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
        return new Attribute(
            get: function () {
                $users = $this->users;
                $users->push($this->owner);
                return $users->unique('id');
            }
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
                $color = $this->type->getColor() ?? '#3f84f3'; //default color if the project type does not have a color

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
            get: fn() => $this->media('icon')?->first()?->getFullUrl()
                ??
                'https://ui-avatars.com/api/?background=' . $this->color . '&color=' . $this->font_color . '&name=' . $this->name
        );
    }

    /**
     * Get the Project Type of the Project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(ProjectType::class, 'type_id', 'id');
    }

    /**
     * Get if the Project has a Repository
     * @return bool
     */
    public function hasRepository(): bool
    {
        return $this->has_repository;
    }

    /**
     * Get the Repository of the Project, if the Project has a Repository.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne | null
     */
    public function repository(): HasOne | null
    {
        //check if the project has a repository
        if ($this->hasRepository()) {
            //return the repository
            return $this->hasOne(ProjectRepository::class, 'project_id', 'id');
        }

        //return null if the project does not have a repository
        return null;
    }

    /**
     * Get the Repository URL of the Project, if the Project has a Repository.
     * @return Attribute
     */
    public function repositoryUrl(): Attribute
    {
        //check if the project has a repository
        if ($this->hasRepository()) {
            //return the repository URL
            return new Attribute(
                get: function () {
                    return $this->repository->url;
                }
            );
        }

        //return null if the project does not have a repository
        return new Attribute(
            get: function () {
                return null;
            }
        );
    }

    /**
     * Get the Current Sprint of the Project
     * @return Attribute
     */
    public function currentSprint(): Attribute
    {
        return new Attribute(
            get: fn() => $this->sprints()
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
}
