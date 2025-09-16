<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookDelivery extends Model
{
    protected $guarded = []; // allow the controller to mass-assign

    protected function casts(): array
    {
        return [
            'headers' => 'array',
            'payload' => 'string',
        ];
    }
}
