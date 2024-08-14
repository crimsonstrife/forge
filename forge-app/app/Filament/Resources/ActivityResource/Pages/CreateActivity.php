<?php

namespace App\Filament\Resources\ActivityResource\Pages;

use App\Filament\Resources\ActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateActivity
 *
 * This class represents a create activity page.
 */
class CreateActivity extends CreateRecord
{
    protected static string $resource = ActivityResource::class;
}
