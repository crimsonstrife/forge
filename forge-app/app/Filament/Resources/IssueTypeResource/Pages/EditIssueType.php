<?php

namespace App\Filament\Resources\IssueTypeResource\Pages;

use App\Filament\Resources\IssueTypeResource;
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
