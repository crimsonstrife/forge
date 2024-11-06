<?php

namespace App\Filament\Resources\ProjectTypeResource\Pages;

use App\Filament\Resources\ProjectTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditProjectType
 *
 * This class extends the EditRecord class and is used to handle the editing of project types within the application.
 *
 * @package App\Filament\Resources\ProjectTypeResource\Pages
 */
class EditProjectType extends EditRecord
{
    protected static string $resource = ProjectTypeResource::class;

    /**
     * Get the header actions for the Edit Project Type page.
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
