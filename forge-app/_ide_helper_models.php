<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */

namespace App\Models{
    /**
     * Class Activity
     *
     * This class represents the Activity model in the application.
     * It extends the base Model class and uses the HasFactory and SoftDeletes traits.
     *
     * @package App\Models
     * @property int $id
     * @property string $name
     * @property string $description
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @method static \Illuminate\Database\Eloquent\Builder|Activity newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Activity newQuery()
     * @method static \Illuminate\Database\Query\Builder|Activity onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|Activity query()
     * @method static \Illuminate\Database\Eloquent\Builder|Activity whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Activity whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Activity whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Activity whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Activity whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Activity whereUpdatedAt($value)
     * @method static \Illuminate\Database\Query\Builder|Activity withTrashed()
     * @method static \Illuminate\Database\Query\Builder|Activity withoutTrashed()
     */
    class Activity extends \Eloquent
    {
    }
}

namespace App\Models\Auth{
    /**
     * PermissionGroup Model
     *
     * This model represents a permission set group, which is a collection of permission sets that can be assigned to users, roles, or other entities. Permission set groups can be used to group permission sets together and assign them to multiple entities at once.
     * Permission set groups can have many permission sets, and a permission set group can belong to many permission sets. These relationships are defined in the permission_set_group_permission_sets table.
     * Permissions may be assigned to a permission set group directly, and these permissions can only be granted, but can be overridden by the permissions assigned to the permission set if they are muted in the set.
     * Permission sets must be in the same guard as the permission set group to be assigned to it.
     * Permissions must be in the same guard as the permission set group to be assigned to it.
     *
     * @property ?\Illuminate\Support\Carbon $created_at
     * @property ?\Illuminate\Support\Carbon $updated_at
     * @property int $id
     * @property string $name
     * @property string|null $description
     * @property string $guard_name
     * @property string|null $deleted_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Auth\PermissionSet> $permissionSets
     * @property-read int|null $permission_sets_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
     * @property-read int|null $permissions_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Auth\Role> $roles
     * @property-read int|null $roles_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
     * @property-read int|null $users_count
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup whereGuardName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionGroup whereUpdatedAt($value)
     */
    class PermissionGroup extends \Eloquent
    {
    }
}

namespace App\Models\Auth{
    /**
     * PermissionSet Model
     *
     * This model represents a permission set, which is a collection of permissions that can be assigned to users, roles, or other entities. Permission sets can be used to group permissions together and assign them to multiple entities at once.
     * Permission sets can have many permissions, and a permission set can belong to many permissions. These relationships are defined in the permission_set_permissions table.
     * Permission sets can also mark individual permissions as granted or denied for a group, by setting the muted column to true.
     * Permissions must be in the same guard as the permission set to be assigned to it.
     *
     * @property ?\Illuminate\Support\Carbon $created_at
     * @property ?\Illuminate\Support\Carbon $updated_at
     * @property int $id
     * @property string $name
     * @property string|null $description
     * @property string $guard_name
     * @property string|null $deleted_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
     * @property-read int|null $permissions_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Auth\Role> $roles
     * @property-read int|null $roles_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
     * @property-read int|null $users_count
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionSet newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionSet newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionSet query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionSet whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionSet whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionSet whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionSet whereGuardName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionSet whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionSet whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionSet whereUpdatedAt($value)
     */
    class PermissionSet extends \Eloquent
    {
    }
}

namespace App\Models\Auth{
    /**
     *
     *
     * @property ?\Illuminate\Support\Carbon $created_at
     * @property ?\Illuminate\Support\Carbon $updated_at
     * @property int $id
     * @property string $name
     * @property string $guard_name
     * @property int $protected
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Auth\PermissionGroup> $permissionGroups
     * @property-read int|null $permission_groups_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Auth\PermissionSet> $permissionSets
     * @property-read int|null $permission_sets_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
     * @property-read int|null $permissions_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, Role> $roles
     * @property-read int|null $roles_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
     * @property-read int|null $users_count
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role permission($permissions, $without = false)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role role($roles, $guard = null, $without = false)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereGuardName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereProtected($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role withoutPermission($permissions)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Role withoutRole($roles, $guard = null)
     */
    class Role extends \Eloquent implements \App\Contracts\Role
    {
    }
}

namespace App\Models{
    /**
     * Class Board
     * Model for the Board table
     *
     * @package App\Models
     * @property int $id
     * @property string $name
     * @property string $description
     * @property string $slug
     * @property string $uuid
     * @property string $type
     * @property int $is_public
     * @property int $project_id
     * @property int $created_by
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Board newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Board newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Board query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Board whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Board whereCreatedBy($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Board whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Board whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Board whereIsPublic($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Board whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Board whereProjectId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Board whereSlug($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Board whereType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Board whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Board whereUuid($value)
     */
    class Board extends \Eloquent
    {
    }
}

namespace App\Models{
    /**
     * Class Calendar
     * Model for the Calendar table
     *
     * @package App\Models
     * @property int $id
     * @property string $slug
     * @property string $uuid
     * @property string $name
     * @property string|null $description
     * @property int|null $user_id
     * @property int|null $project_id
     * @property int $is_project_calendar
     * @property int $is_user_calendar
     * @property int $is_shared
     * @property string|null $deleted_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereIsProjectCalendar($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereIsShared($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereIsUserCalendar($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereProjectId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereSlug($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Calendar whereUuid($value)
     */
    class Calendar extends \Eloquent
    {
    }
}

namespace App\Models{
    /**
     * Class Comment
     * Model for the Comment table
     *
     * @package App\Models
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Comment query()
     */
    class Comment extends \Eloquent
    {
    }
}

namespace App\Models{
    /**
     * Class DesignElements
     * Model for the DesignElements table
     *
     * @package App\Models
     * @property int $id
     * @property string $name
     * @property int $project_id
     * @property int $parent_id
     * @property int $created_by
     * @property string|null $deleted_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property float|null $estimation
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignElements newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignElements newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignElements query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignElements whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignElements whereCreatedBy($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignElements whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignElements whereEstimation($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignElements whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignElements whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignElements whereParentId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignElements whereProjectId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DesignElements whereUpdatedAt($value)
     */
    class DesignElements extends \Eloquent
    {
    }
}

namespace App\Models{
    /**
     *
     *
     * @property int $id
     * @property int $enabled
     * @property string $guild_id
     * @property string $client_id
     * @property string $client_secret
     * @property string $bot_token
     * @property string $redirect_uri
     * @property array|null $role_mappings
     * @property array|null $channel_mappings
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordConfig newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordConfig newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordConfig query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordConfig whereBotToken($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordConfig whereChannelMappings($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordConfig whereClientId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordConfig whereClientSecret($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordConfig whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordConfig whereEnabled($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordConfig whereGuildId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordConfig whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordConfig whereRedirectUri($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordConfig whereRoleMappings($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordConfig whereUpdatedAt($value)
     */
    class DiscordConfig extends \Eloquent
    {
    }
}

namespace App\Models{
    /**
     * Class Epic
     * Model for the Epic table
     *
     * @package App\Models
     * @property int $id
     * @property string $name
     * @property string|null $description
     * @property int $project_id
     * @property string|null $start_date
     * @property string|null $end_date
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property int|null $parent_id
     * @property-read \Illuminate\Database\Eloquent\Collection<int, Epic> $children
     * @property-read int|null $children_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Issues\Issue> $issues
     * @property-read int|null $issues_count
     * @property-read Epic|null $parent
     * @property-read \App\Models\Projects\Project|null $project
     * @property-read \App\Models\Sprint|null $sprint
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Story> $stories
     * @property-read int|null $stories_count
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Epic newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Epic newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Epic onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Epic query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Epic whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Epic whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Epic whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Epic whereEndDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Epic whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Epic whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Epic whereParentId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Epic whereProjectId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Epic whereStartDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Epic whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Epic withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Epic withoutTrashed()
     */
    class Epic extends \Eloquent
    {
    }
}

namespace App\Models{
    /**
     *
     *
     * @property int $id
     * @property string $name
     * @property string $type
     * @property string|null $style
     * @property int|null $is_builtin Indicates if the icon is built-in or user-uploaded
     * @property string $prefix
     * @property string|null $set
     * @property string|null $class
     * @property string|null $svg_code
     * @property string|null $svg_file_path
     * @property int|null $created_by
     * @property int|null $updated_by
     * @property int|null $deleted_by
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $deleted_at
     * @property-read mixed $svg_url
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Icon newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Icon newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Icon query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Icon whereClass($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Icon whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Icon whereCreatedBy($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Icon whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Icon whereDeletedBy($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Icon whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Icon whereIsBuiltin($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Icon whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Icon wherePrefix($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Icon whereSet($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Icon whereStyle($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Icon whereSvgCode($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Icon whereSvgFilePath($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Icon whereType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Icon whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Icon whereUpdatedBy($value)
     */
    class Icon extends \Eloquent
    {
    }
}

namespace App\Models\Issues{
    /**
     * Class Issue
     * Model for the Issue table
     *
     * @package App\Models\Issues\Issues
     * @property int $id
     * @property string $title
     * @property string|null $description
     * @property string $content
     * @property int|null $owner_id
     * @property int|null $responsible_id
     * @property int $project_id
     * @property int $issue_type_id
     * @property int $issue_status_id
     * @property string|null $created_by
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property int $order
     * @property int $priority_id
     * @property float|null $estimation
     * @property int|null $epic_id
     * @property int|null $sprint_id
     * @property string|null $started_at
     * @property string|null $ended_at
     * @property int|null $related_story_id
     * @property string $story_relation_type
     * @property int $story_sort
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Issues\IssueActivity> $activities
     * @property-read int|null $activities_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, Issue> $attachments
     * @property-read int|null $attachments_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Comment> $comments
     * @property-read int|null $comments_count
     * @property-read mixed $completed_percentage
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DesignElements> $designElements
     * @property-read int|null $design_elements_count
     * @property-read \App\Models\Epic|null $epic
     * @property-read mixed $estimation_for_humans
     * @property-read mixed $estimation_in_seconds
     * @property-read mixed $estimation_progress
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Issues\IssueHour> $hours
     * @property-read int|null $hours_count
     * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
     * @property-read int|null $media_count
     * @property-read \App\Models\User|null $owner
     * @property-read \App\Models\Issues\IssuePriority|null $priority
     * @property-read \App\Models\Projects\Project $project
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Issues\IssueRelation> $relations
     * @property-read int|null $relations_count
     * @property-read \App\Models\User|null $responsible
     * @property-read \App\Models\Sprint|null $sprint
     * @property-read \App\Models\Sprint|null $sprints
     * @property-read \App\Models\Issues\IssueStatus|null $status
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $subscribers
     * @property-read int|null $subscribers_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tags> $tags
     * @property-read int|null $tags_count
     * @property-read mixed $time_remaining
     * @property-read mixed $time_remaining_in_seconds
     * @property-read mixed $total_logged_hours
     * @property-read mixed $total_logged_in_hours
     * @property-read mixed $total_logged_seconds
     * @property-read \App\Models\Issues\IssueType|null $type
     * @property-read mixed $watchers
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereContent($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereCreatedBy($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereEndedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereEpicId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereEstimation($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereIssueStatusId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereIssueTypeId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereOrder($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereOwnerId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue wherePriorityId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereProjectId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereRelatedStoryId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereResponsibleId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereSprintId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereStartedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereStoryRelationType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereStorySort($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Issue withoutTrashed()
     */
    class Issue extends \Eloquent implements \Spatie\MediaLibrary\HasMedia
    {
    }
}

namespace App\Models\Issues{
    /**
     * Class IssueActivity
     *
     * This class represents the IssueActivity model, which is responsible for storing and retrieving issue activity data.
     *
     * @package App\Models\Issues\Issues
     * @property int $id
     * @property int $issue_id
     * @property int $user_id
     * @property int $old_status_id
     * @property int $new_status_id
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\Issues\Issue|null $issue
     * @property-read \App\Models\Issues\IssueStatus|null $newStatus
     * @property-read \App\Models\Issues\IssueStatus|null $oldStatus
     * @property-read \App\Models\User|null $user
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueActivity newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueActivity newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueActivity query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueActivity whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueActivity whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueActivity whereIssueId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueActivity whereNewStatusId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueActivity whereOldStatusId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueActivity whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueActivity whereUserId($value)
     */
    class IssueActivity extends \Eloquent
    {
    }
}

namespace App\Models\Issues{
    /**
     * Represents a model for an issue git branch.
     *
     * @package App\Models\Issues\Issues
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueGitBranch newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueGitBranch newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueGitBranch query()
     */
    class IssueGitBranch extends \Eloquent
    {
    }
}

namespace App\Models\Issues{
    /**
     * Represents a Git commit associated with an issue.
     *
     * @package App\Models\Issues\Issues
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueGitCommit newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueGitCommit newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueGitCommit query()
     */
    class IssueGitCommit extends \Eloquent
    {
    }
}

namespace App\Models\Issues{
    /**
     * Represents the IssueGitPullRequest model.
     *
     * This model extends the base Model class and uses the HasFactory trait.
     * It is used to interact with the "issue_git_pull_requests" table in the database.
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueGitPullRequest newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueGitPullRequest newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueGitPullRequest query()
     */
    class IssueGitPullRequest extends \Eloquent
    {
    }
}

namespace App\Models\Issues{
    /**
     * Class IssueHour
     *
     * @package App\Models
     * @property int $id
     * @property int $user_id
     * @property int $issue_id
     * @property float $value
     * @property string|null $comment
     * @property int $activity_id
     * @property \Illuminate\Support\Carbon $created_at
     * @property \Illuminate\Support\Carbon $updated_at
     * @property-read \App\Models\User $user
     * @property-read \App\Models\Issues\Issue $issue
     * @property-read \App\Models\Activity $activity
     * @property-read \Illuminate\Support\CarbonInterval $forHumans
     * @property-read mixed $for_humans
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueHour newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueHour newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueHour query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueHour whereActivityId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueHour whereComment($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueHour whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueHour whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueHour whereIssueId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueHour whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueHour whereUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueHour whereValue($value)
     */
    class IssueHour extends \Eloquent
    {
    }
}

namespace App\Models\Issues{
    /**
     * Class IssuePriority
     *
     * This class represents the IssuePriority model in the application.
     * It extends the base Model class and uses the HasFactory and SoftDeletes traits.
     *
     * @package App\Models\Issues\Issues
     * @property int $id
     * @property string $name
     * @property string|null $icon
     * @property string $color
     * @property int $is_default
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Issues\Issue> $issues
     * @property-read int|null $issues_count
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssuePriority default()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssuePriority newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssuePriority newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssuePriority notDefault()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssuePriority onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssuePriority query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssuePriority whereColor($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssuePriority whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssuePriority whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssuePriority whereIcon($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssuePriority whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssuePriority whereIsDefault($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssuePriority whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssuePriority whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssuePriority withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssuePriority withoutTrashed()
     */
    class IssuePriority extends \Eloquent
    {
    }
}

namespace App\Models\Issues{
    /**
     * Class IssueRelation
     *
     * This class represents the IssueRelation model, which is responsible for managing the relationships between issues.
     *
     * @property int $id
     * @property int $issue_id
     * @property int $related_issue_id
     * @property int $issue_relation_type
     * @property int $issue_sort
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\Issues\Issue $issue
     * @property-read \App\Models\Issues\Issue|null $relation
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueRelation newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueRelation newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueRelation query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueRelation whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueRelation whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueRelation whereIssueId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueRelation whereIssueRelationType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueRelation whereIssueSort($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueRelation whereRelatedIssueId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueRelation whereUpdatedAt($value)
     */
    class IssueRelation extends \Eloquent
    {
    }
}

namespace App\Models\Issues{
    /**
     * The IssueStatus model represents the status of an issue in a project.
     *
     * @property int $id
     * @property string $name
     * @property string $color
     * @property string|null $description
     * @property int $is_default
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property int $order
     * @property int|null $project_id
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Issues\Issue> $issues
     * @property-read int|null $issues_count
     * @property-read \App\Models\Projects\Project|null $project
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueStatus newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueStatus newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueStatus onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueStatus query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueStatus whereColor($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueStatus whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueStatus whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueStatus whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueStatus whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueStatus whereIsDefault($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueStatus whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueStatus whereOrder($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueStatus whereProjectId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueStatus whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueStatus withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueStatus withoutTrashed()
     */
    class IssueStatus extends \Eloquent
    {
    }
}

namespace App\Models\Issues{
    /**
     * IssueSubscriber Model
     *
     * Represents a subscriber to an issue in the application.
     *
     * @property int $id
     * @property int $user_id
     * @property int $issue_id
     * @property \Illuminate\Support\Carbon $created_at
     * @property \Illuminate\Support\Carbon $updated_at
     * @property-read \App\Models\User $user
     * @property-read \App\Models\Issues\Issue $issue
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueSubscriber newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueSubscriber newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueSubscriber query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueSubscriber whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueSubscriber whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueSubscriber whereIssueId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueSubscriber whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueSubscriber whereUserId($value)
     */
    class IssueSubscriber extends \Eloquent
    {
    }
}

namespace App\Models\Issues{
    /**
     * Class IssueType
     *
     * This class represents the IssueType model in the application.
     * It extends the base Model class and uses the HasFactory and SoftDeletes traits.
     *
     * @package App\Models
     * @property int $id
     * @property string $name
     * @property string $color
     * @property string $icon
     * @property bool $is_default
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Issues\Issue[] $issues
     * @property int|null $issues_count
     * @method static \Illuminate\Database\Eloquent\Builder|IssueType newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|IssueType newQuery()
     * @method static \Illuminate\Database\Query\Builder|IssueType onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|IssueType query()
     * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereColor($value)
     * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereIcon($value)
     * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereIsDefault($value)
     * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|IssueType whereUpdatedAt($value)
     * @method static \Illuminate\Database\Query\Builder|IssueType withTrashed()
     * @method static \Illuminate\Database\Query\Builder|IssueType withoutTrashed()
     * @mixin \Eloquent
     * @property string|null $description
     * @method static \Illuminate\Database\Eloquent\Builder<static>|IssueType whereDescription($value)
     */
    class IssueType extends \Eloquent
    {
    }
}

namespace App\Models{
    /**
     * Milestone Model
     *
     * Represents a milestone in the application.
     *
     * @property int $id
     * @property string $name
     * @property string|null $start_date
     * @property string|null $end_date
     * @property string|null $description
     * @property int $project_id
     * @property int|null $sprint_id
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone whereEndDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone whereProjectId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone whereSprintId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone whereStartDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Milestone whereUpdatedAt($value)
     */
    class Milestone extends \Eloquent
    {
    }
}

namespace App\Models{
    /**
     * Class PersonalAccessToken
     *
     * This class represents a personal access token model.
     * It extends the base Model class and uses the HasFactory trait.
     *
     * @property int $id
     * @property string $tokenable_type
     * @property int $tokenable_id
     * @property string $name
     * @property string $token
     * @property string|null $abilities
     * @property string|null $last_used_at
     * @property string|null $expires_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken whereAbilities($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken whereExpiresAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken whereLastUsedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken whereToken($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken whereTokenableId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken whereTokenableType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|PersonalAccessToken whereUpdatedAt($value)
     */
    class PersonalAccessToken extends \Eloquent
    {
    }
}

namespace App\Models\Projects{
    /**
     * Class ProjectFavorite
     *
     * @package App\Models
     * @property int $id
     * @property int $user_id
     * @property int $project_id
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \App\Models\Projects\Project $project
     * @property-read \App\Models\User $user
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFavorite newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFavorite newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFavorite query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFavorite whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFavorite whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFavorite whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFavorite whereProjectId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFavorite whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectFavorite whereUserId($value)
     */
    class ProjectFavorite extends \Eloquent
    {
    }
}

namespace App\Models\Projects{
    /**
     * Represents a model for Project Git History.
     *
     * This class extends the Laravel Model class and uses the HasFactory trait.
     * It is used to interact with the "project_git_histories" table in the database.
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectGitHistory newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectGitHistory newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectGitHistory query()
     */
    class ProjectGitHistory extends \Eloquent
    {
    }
}

namespace App\Models\Projects{
    /**
     * ProjectRepository Model
     *
     * This model represents a repository for a project. It extends the base Model class and uses the HasFactory trait.
     *
     * @property int $id The unique identifier of the repository
     * @property int $remote_id The remote identifier of the repository
     * @property string $name The name of the repository
     * @property string $description The description of the repository
     * @property string $slug The slug of the repository
     * @property string $http_url The HTTP URL of the repository
     * @property string $ssh_url The SSH URL of the repository
     * @property string $scm_type The SCM type of the repository
     * @property string $main_branch The main branch of the repository
     * @property int $project_id The ID of the associated project
     * @property array $metadata The metadata of the repository
     * @property array $history The history of the repository
     * @property \App\Models\Projects\Project $project The associated project
     * @method bool verifyAndFetchMetadata() Verify the repository URL, and fetch the metadata if the connection is successful
     * @method bool updateMetadata() Update the metadata of the repository
     * @package App\Models\Projects
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectRepository newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectRepository newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectRepository query()
     */
    class ProjectRepository extends \Eloquent
    {
    }
}

namespace App\Models\Projects{
    /**
     * Represents a project role in the application.
     *
     * This class extends the Laravel Model class and uses the HasFactory trait.
     * It is used to interact with the project_roles table in the database.
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectRole newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectRole newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectRole query()
     */
    class ProjectRole extends \Eloquent
    {
    }
}

namespace App\Models\Projects{
    /**
     * Class ProjectStatus
     *
     * This class represents the model for project statuses in the application.
     * It extends the base Model class and uses the HasFactory and SoftDeletes traits.
     *
     * @package App\Models\Projects
     * @property int $id
     * @property string $name
     * @property string $color
     * @property string|null $description
     * @property int $is_default
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Projects\Project> $projects
     * @property-read int|null $projects_count
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectStatus newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectStatus newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectStatus onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectStatus query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectStatus whereColor($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectStatus whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectStatus whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectStatus whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectStatus whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectStatus whereIsDefault($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectStatus whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectStatus whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectStatus withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectStatus withoutTrashed()
     */
    class ProjectStatus extends \Eloquent
    {
    }
}

namespace App\Models\Projects{
    /**
     * Class ProjectSvnHistory
     *
     * This class represents the ProjectSvnHistory model.
     * It extends the base Model class and uses the HasFactory trait.
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectSvnHistory newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectSvnHistory newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectSvnHistory query()
     */
    class ProjectSvnHistory extends \Eloquent
    {
    }
}

namespace App\Models\Projects{
    /**
     * ProjectType Model
     *
     * Represents a project type in the application.
     *
     * @property int $id
     * @property string $name
     * @property string $color
     * @property string $description
     * @property bool $is_default
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property \Illuminate\Database\Eloquent\Collection|\App\Models\Projects\Project[] $projects
     * @method static \Illuminate\Database\Eloquent\Builder|ProjectType newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|ProjectType newQuery()
     * @method static \Illuminate\Database\Query\Builder|ProjectType onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|ProjectType query()
     * @method static \Illuminate\Database\Eloquent\Builder|ProjectType whereColor($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProjectType whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProjectType whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProjectType whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProjectType whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProjectType whereIsDefault($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProjectType whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|ProjectType whereUpdatedAt($value)
     * @method static \Illuminate\Database\Query\Builder|ProjectType withTrashed()
     * @method static \Illuminate\Database\Query\Builder|ProjectType withoutTrashed()
     * @property-read int|null $projects_count
     */
    class ProjectType extends \Eloquent
    {
    }
}

namespace App\Models\Projects{
    /**
     * ProjectUser Model
     *
     * Represents the relationship between a project, a user, and a role.
     *
     * @property int $id
     * @property int $project_id
     * @property int $user_id
     * @property int|null $role_id
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \App\Models\Projects\ProjectRole|null $role
     * @property-read \App\Models\User $user
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUser newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUser newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUser query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUser whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUser whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUser whereProjectId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUser whereRoleId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUser whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|ProjectUser whereUserId($value)
     */
    class ProjectUser extends \Eloquent
    {
    }
}

namespace App\Models{
    /**
     * Sprint Model
     *
     * Represents a sprint in the application.
     *
     * @package App\Models
     * @property int $id
     * @property string $name
     * @property string $description
     * @property \Illuminate\Support\Carbon $start_date
     * @property \Illuminate\Support\Carbon $end_date
     * @property string $status
     * @property int $project_id
     * @property \Illuminate\Support\Carbon $created_at
     * @property \Illuminate\Support\Carbon $updated_at
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property-read \App\Models\Projects\Project $project
     * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Issues\Issue[] $issues
     * @property-read int|null $issues_count
     * @property-read \App\Models\Epic $epic
     * @property-read \Illuminate\Support\Carbon|null $remaining
     * @property-read \Illuminate\Support\Carbon|null $progress
     * @method static \Illuminate\Database\Eloquent\Builder|Sprint newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder|Sprint newQuery()
     * @method static \Illuminate\Database\Query\Builder|Sprint onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder|Sprint query()
     * @method static \Illuminate\Database\Eloquent\Builder|Sprint whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Sprint whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Sprint whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Sprint whereEndDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Sprint whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Sprint whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Sprint whereProjectId($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Sprint whereStartDate($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Sprint whereStatus($value)
     * @method static \Illuminate\Database\Eloquent\Builder|Sprint whereUpdatedAt($value)
     * @method static \Illuminate\Database\Query\Builder|Sprint withTrashed()
     * @method static \Illuminate\Database\Query\Builder|Sprint withoutTrashed()
     * @property int|null $epic_id
     * @property \Illuminate\Support\Carbon|null $started_at
     * @property \Illuminate\Support\Carbon|null $ended_at
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Sprint whereEndedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Sprint whereEpicId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Sprint whereStartedAt($value)
     */
    class Sprint extends \Eloquent
    {
    }
}

namespace App\Models{
    /**
     * Represents a Story model.
     *
     * @package App\Models
     * @property int $id
     * @property string $title
     * @property string|null $description
     * @property string $content
     * @property int $owner_id
     * @property int|null $responsible_id
     * @property int $project_id
     * @property int $issue_type_id
     * @property int $issue_status_id
     * @property string|null $deleted_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property int|null $related_issue_id
     * @property string $issue_relation_type
     * @property int $issue_sort
     * @property int|null $related_story_id
     * @property string $story_relation_type
     * @property int $story_sort
     * @property int|null $epic_id
     * @property int|null $sprint_id
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereContent($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereEpicId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereIssueRelationType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereIssueSort($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereIssueStatusId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereIssueTypeId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereOwnerId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereProjectId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereRelatedIssueId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereRelatedStoryId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereResponsibleId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereSprintId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereStoryRelationType($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereStorySort($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereTitle($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Story whereUpdatedAt($value)
     */
    class Story extends \Eloquent
    {
    }
}

namespace App\Models{
    /**
     * Class Tags
     *
     * This class represents the Tags model.
     * It extends the base Model class and uses the HasFactory trait.
     *
     * @package App\Models
     * @property int $id
     * @property string $name
     * @property string $slug
     * @property string|null $color
     * @property string|null $icon
     * @property int $display_only_on_item_cards
     * @property int $project_id
     * @property int $created_by
     * @property string|null $deleted_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereColor($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereCreatedBy($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereDisplayOnlyOnItemCards($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereIcon($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereProjectId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereSlug($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Tags whereUpdatedAt($value)
     */
    class Tags extends \Eloquent
    {
    }
}

namespace App\Models{
    /**
     * Class Team
     *
     * This class represents a team model in the application.
     * It extends the base Model class and uses the HasFactory and HasPermissions traits.
     *
     * @property int $id
     * @property string $uuid
     * @property string $name
     * @property string $slug
     * @property string|null $description
     * @property int $owner_user_id
     * @property string|null $deleted_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Auth\PermissionGroup> $permissionGroups
     * @property-read int|null $permission_groups_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Auth\PermissionSet> $permissionSets
     * @property-read int|null $permission_sets_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
     * @property-read int|null $permissions_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Auth\Role> $roles
     * @property-read int|null $roles_count
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team permission($permissions, $without = false)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team role($roles, $guard = null, $without = false)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereDescription($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereOwnerUserId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereSlug($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team whereUuid($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team withoutPermission($permissions)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Team withoutRole($roles, $guard = null)
     */
    class Team extends \Eloquent
    {
    }
}

namespace App\Models{
    /**
     * Class TeamMember
     *
     * This class represents a team member in the application.
     * It extends the Model class and uses the HasFactory trait.
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|TeamMember query()
     */
    class TeamMember extends \Eloquent
    {
    }
}

namespace App\Models{
    /**
     * Timesheet Model
     *
     * This class represents the Timesheet model in the application.
     * It extends the base Model class and uses the HasFactory trait.
     *
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Timesheet newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Timesheet newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|Timesheet query()
     */
    class Timesheet extends \Eloquent
    {
    }
}

namespace App\Models{
    /**
     * User Model
     *
     * Represents a user in the application.
     *
     * @property int $id
     * @property string $first-name
     * @property string $last-name
     * @property string $username
     * @package App\Models
     * @property string $first_name
     * @property string $last_name
     * @property string $email
     * @property \Illuminate\Support\Carbon|null $email_verified_at
     * @property string $password
     * @property string|null $two_factor_secret
     * @property string|null $two_factor_recovery_codes
     * @property string|null $two_factor_confirmed_at
     * @property string|null $remember_token
     * @property int|null $current_team_id
     * @property string|null $profile_photo_path
     * @property \Illuminate\Support\Carbon|null $deleted_at
     * @property \Illuminate\Support\Carbon|null $created_at
     * @property \Illuminate\Support\Carbon|null $updated_at
     * @property string|null $creation_token
     * @property string|null $discord_id
     * @property array|null $discord_roles
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Client> $clients
     * @property-read int|null $clients_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Projects\Project> $favoriteProjects
     * @property-read int|null $favorite_projects_count
     * @property-read string $full_name
     * @property-read string $name
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Issues\IssueHour> $hours
     * @property-read int|null $hours_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Issues\Issue> $issuesOwned
     * @property-read int|null $issues_owned_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Issues\Issue> $issuesResponsible
     * @property-read int|null $issues_responsible_count
     * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
     * @property-read int|null $notifications_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Passport\Token> $passportTokens
     * @property-read int|null $passport_tokens_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Auth\PermissionGroup> $permissionGroups
     * @property-read int|null $permission_groups_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Auth\PermissionSet> $permissionSets
     * @property-read int|null $permission_sets_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
     * @property-read int|null $permissions_count
     * @property-read string $profile_photo_url
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Projects\Project> $projectsAffected
     * @property-read int|null $projects_affected_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Projects\Project> $projectsOwning
     * @property-read int|null $projects_owning_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Auth\Role> $roles
     * @property-read int|null $roles_count
     * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PersonalAccessToken> $sanctumTokens
     * @property-read int|null $sanctum_tokens_count
     * @property-read mixed $total_logged_in_hours
     * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreationToken($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCurrentTeamId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDiscordId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDiscordRoles($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhotoPath($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed()
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
     * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
     */
    class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail, \Filament\Models\Contracts\FilamentUser
    {
    }
}
