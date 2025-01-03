<?php

namespace App\Models\Issues;

use App\Notifications\IssueCreated;
use App\Notifications\IssueStatusUpdated;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Projects\Project;
use App\Models\Comment;
use App\Models\Tags;
use App\Models\DesignElements;
use App\Models\Sprint;
use App\Models\Epic;
use App\Models\Issues\IssueType;
use App\Models\Issues\IssueStatus;
use App\Models\Issues\IssuePriority;
use App\Traits\IsPermissible;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth as Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use WpOrg\Requests\Auth as WpOrgAuth;
use Xetaio\Mentions\Models\Traits\HasMentionsTrait as HasMentions;

/**
 * Class Issue
 * Model for the Issue table
 * @package App\Models\Issues\Issues
 */
class Issue extends Model implements HasMedia
{
    use HasFactory;
    use SoftDeletes;
    use InteractsWithMedia;
    use HasMentions;
    use IsPermissible;

    protected $fillable = [
        'title',
        'description',
        'content',
        'owner_id',
        'status_id',
        'responsible_id',
        'project_id',
        'issue_type_id',
        'issue_status_id',
        'priority_id',
        'slug',
        'estimation',
        'epic_id',
        'sprint_id',
    ];


    /**
     * Boot the model and its traits.
     *
     * This method is called when the model is initialized. It can be used to
     * register any event listeners or perform any setup required for the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        //creating a new issue
        static::creating(function (Issue $issue) {
            $project = (new Project())->where('id', $issue->project_id)->first();
            $count = Issue::where('project_id', $project->id)->count();
            $order = $project->issues?->last()?->order ?? -1;
            $issue->code = $project->ticket_prefix . '-' . ($count + 1);
            $issue->order = $order + 1;
        });

        //issue was created
        static::created(function (Issue $issue) {
            if ($issue->sprint_id && $issue->sprint->epic_id) {
                Issue::where('id', $issue->id)->update(['epic_id' => $issue->sprint->epic_id]);
            }
            foreach ($issue->watchers as $user) {
                $user->notify(new IssueCreated($issue));
            }
        });

        //updating an issue
        static::updating(function (Issue $issue) {
            $old = Issue::where('id', $issue->id)->first();

            // Issue activity based on status
            $oldStatus = $old->status_id;
            if ($oldStatus != $issue->status_id) {
                $issueActivity = new IssueActivity();
                $issueActivity->create([
                    'issue_id' => $issue->id,
                    'old_status_id' => $oldStatus,
                    'new_status_id' => $issue->status_id,
                    'user_id' => Auth::user()->id,
                ]);

                // notify watchers
                foreach ($issue->watchers as $user) {
                    $user->notify(new IssueStatusUpdated($issue));
                }
            }

            // Issue sprint update
            $oldSprint = $old->sprint_id;
            if ($oldSprint && !$issue->sprint_id) {
                Issue::where('id', $issue->id)->update(['epic_id' => null]);
            } elseif ($issue->sprint_id && $issue->sprint->epic_id) {
                Issue::where('id', $issue->id)->update(['epic_id' => $issue->sprint->epic_id]);
            }
        });
    }

    /**
     * Get the owner of the issue.
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the assignee of the issue.
     * @return BelongsTo
     */
    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    /**
     * Get the project of the issue.
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'id')->with(function ($query) {
            $query->withTrashed();
        });
    }

    /**
     * Get the type of the issue.
     * @return BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(IssueType::class, 'issue_id', 'id')->with(function ($query) {
            $query->withTrashed();
        });
    }

    /**
     * Get the status of the issue.
     * @return BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(IssueStatus::class, 'issue_id', 'id')->with(function ($query) {
            $query->withTrashed();
        });
    }

    /**
     * Get the priority of the issue.
     * @return BelongsTo
     */
    public function priority(): BelongsTo
    {
        return $this->belongsTo(IssuePriority::class, 'issue_id', 'id')->with(function ($query) {
            $query->withTrashed();
        });
    }

    /**
     * Get the epic of the issue.
     * @return BelongsTo
     */
    public function epic(): BelongsTo
    {
        return $this->belongsTo(Epic::class, 'epic_id', 'id');
    }

    /**
     * Get the sprint of the issue.
     * @return BelongsTo
     */
    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class, 'sprint_id', 'id');
    }

    /**
     * Get the activities of the issue.
     * @return HasMany
     */
    public function activities(): HasMany
    {
        return $this->hasMany(IssueActivity::class, 'issue_id', 'id');
    }

    /**
     * Get the comments of the issue.
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'issue_id', 'id');
    }

    /**
     * Get the subscribers of the issue.
     * @return BelongsToMany
     */
    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'issue_subscribers', 'issue_id', 'user_id');
    }

    /**
     * Get the tags of the issue.
     * @return HasMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'issue_has_tags', 'issue_id', 'tag_id')->withTimestamps();
    }

    /**
     * Get the Design Elements that have this issue.
     * @return BelongsToMany
     */
    public function designElements(): BelongsToMany
    {
        return $this->belongsToMany(DesignElements::class, 'design_element_has_issues', 'issue_id', 'design_element_id');
    }

    /**
     * Get the relations of the issue. i.e. issues that are related to this issue.
     * @return HasMany
     */
    public function relations(): HasMany
    {
        return $this->hasMany(IssueRelation::class, 'issue_id', 'id');
    }

    /**
     * Get the watchers of the issue.
     * @return Attribute
     */
    public function watchers(): Attribute
    {
        return new Attribute(
            get: function () {
                $users = $this->project->users;
                $users->push($this->owner);
                if ($this->responsible) {
                    $users->push($this->responsible);
                }
                return $users->unique('id');
            }
        );
    }

    /**
     * Get the sprints that this issue is part of.
     * @return BelongsTo
     */
    public function sprints(): BelongsTo
    {
        return $this->belongsTo(Sprint::class, 'sprint_id');
    }

    /**
     * Get the hours spent on the issue.
     * @return HasMany
     */
    public function hours(): HasMany
    {
        return $this->hasMany(IssueHour::class, 'issue_id');
    }

    /**
     * Get the attachments of the issue.
     * @return BelongsToMany
     */
    public function attachments(): BelongsToMany
    {
        return $this->BelongsToMany(Issue::class, 'issue_has_attachments', 'issue_id', 'attachment_id');
    }

    /**
     * Get the total logged hours on the issue.
     * @return Attribute
     */
    public function totalLoggedHours(): Attribute
    {
        return new Attribute(
            get: function () {
                $seconds = $this->hours->sum('value') * 3600;
                return CarbonInterval::seconds($seconds)->cascade()->forHumans();
            }
        );
    }

    /**
     * Get the total logged seconds on the issue.
     * @return Attribute
     */
    public function totalLoggedSeconds(): Attribute
    {
        return new Attribute(
            get: function () {
                return $this->hours->sum('value') * 3600;
            }
        );
    }

    /**
     * Get the total LoggedIn Hours on the issue.
     * @return Attribute
     */
    public function totalLoggedInHours(): Attribute
    {
        return new Attribute(
            get: function () {
                return $this->hours->sum('value');
            }
        );
    }

    /**
     * Get the estimation for human readability.
     * @return Attribute
     */
    public function estimationForHumans(): Attribute
    {
        return new Attribute(
            get: function () {
                return CarbonInterval::seconds($this->estimationInSeconds)->cascade()->forHumans();
            }
        );
    }

    /**
     * Get the estimation in seconds.
     * @return Attribute
     */
    public function estimationInSeconds(): Attribute
    {
        return new Attribute(
            get: function () {
                return $this->estimation * 3600;
            }
        );
    }

    /**
     * Get the estimation progress
     * @return Attribute
     */
    public function estimationProgress(): Attribute
    {
        return new Attribute(
            get: function () {
                if ($this->estimationInSeconds == 0) {
                    return 0;
                }
                return round(($this->totalLoggedSeconds / $this->estimationInSeconds ?? 1) * 100);
            }
        );
    }

    /**
     * Get the completed percentage of the issue.
     * @return Attribute
     */
    public function completedPercentage(): Attribute
    {
        return new Attribute(
            get: fn () => $this->estimationProgress
        );
    }

    /**
     * Get the time remaining for the issue.
     * @return Attribute
     */
    public function timeRemaining(): Attribute
    {
        return new Attribute(
            get: function () {
                $remaining = $this->estimationInSeconds - $this->totalLoggedSeconds;
                return CarbonInterval::seconds($remaining)->cascade()->forHumans();
            }
        );
    }

    /**
     * Get the time remaining in seconds.
     * @return Attribute
     */
    public function timeRemainingInSeconds(): Attribute
    {
        return new Attribute(
            get: function () {
                return $this->estimationInSeconds - $this->totalLoggedSeconds;
            }
        );
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
}
