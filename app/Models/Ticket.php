<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $id
 * @property string $key
 * @property string|null $access_token
 */
class Ticket extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    /** @return BelongsTo<ServiceProduct,Ticket> */
    public function product(): BelongsTo { return $this->belongsTo(ServiceProduct::class, 'service_product_id'); }

    /** @return BelongsTo<Project,Ticket> */
    public function project(): BelongsTo { return $this->belongsTo(Project::class); }

    /** @return BelongsTo<User,Ticket> */
    public function submitter(): BelongsTo { return $this->belongsTo(User::class, 'submitter_user_id'); }

    /** @return BelongsTo<User,Ticket> */
    public function assignee(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to_user_id'); }

    /** @return HasMany<TicketComment> */
    public function comments(): HasMany { return $this->hasMany(TicketComment::class)->latest(); }

    /** @return BelongsToMany<Issue> */
    public function issues(): BelongsToMany { return $this->belongsToMany(Issue::class, 'ticket_issue_links'); }

    protected static function booted(): void
    {
        static::creating(function (self $ticket): void {
            if (empty($ticket->id)) { $ticket->id = (string) str()->ulid(); }
            if (empty($ticket->key)) { $ticket->key = app(\App\Services\Support\TicketKeyService::class)->nextKey(); }
            if (empty($ticket->via)) { $ticket->via = 'portal'; }
        });
    }
}
