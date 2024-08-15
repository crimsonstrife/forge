<?php

namespace App\Filament\Resources\IssuePriorityResource\Pages;

use App\Filament\Resources\IssuePriorityResource;
use App\Models\IssuePriority;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateIssuePriority
 *
 * This class represents a create issue priority page.
 */
class CreateIssuePriority extends CreateRecord
{
    protected static string $resource = IssuePriorityResource::class;

    /**
     * Perform actions before creating an issue priority.
     */
    protected function beforeCreate(): void
    {
        // Check if any issues are already set as default
        $isDefault = IssuePriority::where('is_default', true)->exists();

        // Confirm if the user wants to set the new issue priority as default
        if ($isDefault) {
            $this->record->is_default = $this->confirm('There is already an issue priority set as default. Do you want to set this issue priority as default?');
        }
    }

    /**
     * Perform actions after creating an issue priority.
     *
     * @return void
     */
    protected function afterCreate(): void
    {
        // If the new issue priority is set as default, then set all other issue priorities as not default
        if ($this->record->is_default) {
            IssuePriority::where('id', '<>', $this->record->id)->where('is_default', true)->update(['is_default' => false]);
        }
    }
}
