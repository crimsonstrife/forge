<?php

namespace App\Filament\Resources\IconResource\Pages;

use App\Models\Icon;
use App\Filament\Resources\IconResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * The CreateIcon class is responsible for handling the creation of Icon records.
 *
 * It extends the CreateRecord class and utilizes the Icon and IconResource
 * classes as its model and resource respectively.
 */
class CreateIcon extends CreateRecord
{
    protected static ?string $model = Icon::class;

    protected static string $resource = IconResource::class;
}
