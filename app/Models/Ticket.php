<?php

namespace App\Models;

use App\Services\Support\TicketKeyService;
use App\Support\ActivityContext;
use App\Traits\IsPermissible;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * @property string $id
 * @property string $key
 * @property string|null $access_token
 */
class Ticket extends BaseModel
{
    use SoftDeletes;
    use HasUlids;
    use LogsActivity;
    use IsPermissible;

    protected $guarded = [];

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::ulid();
        });
    }

    /** @return BelongsTo<ServiceProduct,Ticket> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(ServiceProduct::class, 'service_product_id');
    }

    /** @return BelongsTo<Project,Ticket> */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /** @return BelongsTo<User,Ticket> */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitter_user_id');
    }

    /** @return BelongsTo<User,Ticket> */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /** @return HasMany<TicketComment> */
    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class)->latest();
    }

    /** @return BelongsToMany<Issue> */
    public function issues(): BelongsToMany
    {
        return $this->belongsToMany(Issue::class, 'ticket_issue_links');
    }

    /** @return BelongsTo<TicketStatus,Ticket> */
    public function status(): BelongsTo
    {
        return $this->belongsTo(TicketStatus::class, 'status_id');
    }

    /** @return BelongsTo<TicketPriority,Ticket> */
    public function priority(): BelongsTo
    {
        return $this->belongsTo(TicketPriority::class, 'priority_id');
    }

    /** @return BelongsTo<TicketType,Ticket> */
    public function type(): BelongsTo
    {
        return $this->belongsTo(TicketType::class, 'type_id');
    }

    protected static function booted(): void
    {
        static::creating(static function (self $ticket): void {
            if (empty($ticket->id)) {
                $ticket->id = (string) str()->ulid();
            }
            if (empty($ticket->key)) {
                $ticket->key = app(TicketKeyService::class)->nextKey();
            }
            if (empty($ticket->via)) {
                $ticket->via = 'portal';
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('forge.support.ticket')
            ->logOnly(['subject','key','body','submitter_email'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tapActivity(\Spatie\Activitylog\Contracts\Activity $activity): void
    {
        $ctx = ActivityContext::base();
        $activity->team_id = $ctx['team_id'];                 // persisted column
        $activity->properties = $activity->properties->merge([ // JSON props
            'actor_id' => $ctx['user_id'],
            'ip'       => $ctx['ip'],
            'ua'       => $ctx['user_agent'],
        ]);
        $activity->event = $activity->event ?: 'updated'; // create/update/delete auto-populate
        $activity->description = 'ticket.' . $activity->event;
    }
}
