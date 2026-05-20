<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackMagicLink extends Model
{
    use HasFactory;
    use HasUlids;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'expires_at' => 'immutable_datetime',
            'consumed_at' => 'immutable_datetime',
        ];
    }

    /** @return BelongsTo<FeedbackIdentity,FeedbackMagicLink> */
    public function identity(): BelongsTo
    {
        return $this->belongsTo(FeedbackIdentity::class, 'identity_id');
    }

    /** @return BelongsTo<ServiceProduct,FeedbackMagicLink> */
    public function serviceProduct(): BelongsTo
    {
        return $this->belongsTo(ServiceProduct::class, 'service_product_id');
    }
}
