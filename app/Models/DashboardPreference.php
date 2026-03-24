<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardPreference extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'landing_workspace',
        'active_workspace',
        'hidden_widgets',
        'widget_order',
    ];

    protected function casts(): array
    {
        return [
            'id' => 'string',
            'user_id' => 'string',
            'hidden_widgets' => 'array',
            'widget_order' => 'array',
        ];
    }

    protected $attributes = [
        'landing_workspace' => 'overview',
        'active_workspace' => 'overview',
    ];

    protected $keyType = 'string';

    public $incrementing = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
