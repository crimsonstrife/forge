<?php

namespace App\Filament\Resources\IssueTypeResource\Pages;

use App\Filament\Resources\IssueTypeResource;
use App\Models\IssueType;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

/**
 * Class EditIssueType
 *
 * This class represents an edit issue type page.
 */
class EditIssueType extends EditRecord
{
    protected static string $resource = IssueTypeResource::class;

    /**
     * Retrieves an array of action objects.
     *
     * @return array An array containing the action objects.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Executes actions after saving the record.
     *
     * This method ensures that only one record is marked as default.
     * If the current record is marked as default, it updates all other records
     * to set the 'is_default' attribute to false.
     *
     * @return void
     */
    protected function afterSave(): void
    {
        if ($this->record->is_default) {
            IssueType::where('id', '<>', $this->record->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }
}
