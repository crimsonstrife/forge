<?php

namespace App\Filament\Resources\IssuePriorityResource\Pages;

use App\Filament\Resources\IssuePriorityResource;
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
