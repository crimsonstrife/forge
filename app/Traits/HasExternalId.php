<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Automatically assigns a UUID to `external_id` on create.
 */
trait HasExternalId
{
    protected static function bootHasExternalId(): void
    {
        static::creating(function ($model): void {
            if (! array_key_exists('external_id', $model->getAttributes())) {
                return; // table has no column
            }
            if (empty($model->external_id)) {
                $model->external_id = (string) Str::orderedUuid();
            }
        });
    }
}
