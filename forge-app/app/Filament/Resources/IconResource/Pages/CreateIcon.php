<?php

namespace App\Filament\Resources\IconResource\Pages;

use App\Models\Icon;
use App\Filament\Resources\IconResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateIcon extends CreateRecord
{
    protected static ?string $model = Icon::class;

    protected static string $resource = IconResource::class;
}
