<?php

namespace App\Filament\Resources\IconResource\Pages;

use App\Filament\Resources\IconResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * The EditIcon class is responsible for providing functionalities to edit an Icon record.
 * It extends the EditRecord class, utilizing the properties and methods required for working with icons.
 */
class EditIcon extends EditRecord
{
    protected static ?string $model = Icon::class;

    protected static string $resource = IconResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
