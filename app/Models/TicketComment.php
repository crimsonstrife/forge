<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TicketComment extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    /** @return BelongsTo<Ticket,TicketComment> */
    public function ticket(): BelongsTo { return $this->belongsTo(Ticket::class); }

    /** @return BelongsTo<User,TicketComment> */
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
