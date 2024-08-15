<?php

namespace App\Filament\Resources\IssueTypeResource\Pages;

use App\Filament\Resources\IssueTypeResource;
use App\Models\IssueType;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateIssueType
 *
 * This class represents a create issue type page.
 */
class CreateIssueType extends CreateRecord
{
    protected static string $resource = IssueTypeResource::class;

    /**
     * Handles post-creation logic for the record.
     *
     * If the newly created record is set as the default, this method will
     * unset the 'is_default' flag for all other records.
     *
     * @return void
     */
    protected function afterCreate(): void
    {
        if ($this->record->is_default) {
            IssueType::where('id', '<>', $this->record->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }
    }
}
