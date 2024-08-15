<?php

namespace App\Filament\Resources\IssuePriorityResource\Pages;

use App\Filament\Resources\IssuePriorityResource;
use App\Models\IssuePriority;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditIssuePriority
 *
 * This class represents an edit issue priority page.
 */
class EditIssuePriority extends EditRecord
{
    protected static string $resource = IssuePriorityResource::class;

    /**
     * Retrieve the available actions for editing an issue priority.
     *
     * @return array The array of available actions.
     */
    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Perform actions after saving the data.
     *
     * @return void
     */
    protected function afterSave(): void
    {
        if ($this->record->is_default) {
            IssuePriority::where('id', '<>', $this->record->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }
}
