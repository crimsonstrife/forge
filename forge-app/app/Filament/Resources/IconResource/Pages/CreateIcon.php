<?php

namespace App\Filament\Resources\IconResource\Pages;

use App\Models\Icon;
use App\Filament\Resources\IconResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateIcon
 *
 * This class extends the CreateRecord class and is used to handle the creation of icons within the Filament resource.
 *
 * @package App\Filament\Resources\IconResource\Pages
 */
class CreateIcon extends CreateRecord
{
    protected static ?string $model = Icon::class;

    protected static string $resource = IconResource::class;
}
