<?php

namespace App\Filament\Resources\ProjectStatusResource\Pages;

use App\Filament\Resources\ProjectStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditProjectStatus
 *
 * This class extends the EditRecord class and is used to handle the editing of project status records
 * within the Filament resource management system.
 *
 * @package App\Filament\Resources\ProjectStatusResource\Pages
 */
class EditProjectStatus extends EditRecord
{
    protected static string $resource = ProjectStatusResource::class;

    /**
     * Get the header actions for the Edit Project Status page.
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
