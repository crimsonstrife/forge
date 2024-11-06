<?php

namespace App\Filament\Resources\IconResource\Pages;

use App\Filament\Resources\IconResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\Icon;

/**
 * Class EditIcon
 *
 * This class extends the EditRecord class and is used to handle the editing of icons within the application.
 *
 * @package App\Filament\Resources\IconResource\Pages
 */
class EditIcon extends EditRecord
{
    protected static ?string $model = Icon::class;

    protected static string $resource = IconResource::class;

    /**
     * Get the header actions for the EditIcon page.
     *
     * @return array The array of header actions.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
