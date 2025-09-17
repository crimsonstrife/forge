<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TicketComment extends Model
{
    use SoftDeletes;
    use HasUlids;

    public static function boot(): void
    {
        parent::boot();

        static::creating(static function ($model) {
            $model->id = Str::ulid();
        });
    }

    protected $guarded = [];

    /** @return BelongsTo<Ticket,TicketComment> */
    public function ticket(): BelongsTo { return $this->belongsTo(Ticket::class); }

    /** @return BelongsTo<User,TicketComment> */
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
