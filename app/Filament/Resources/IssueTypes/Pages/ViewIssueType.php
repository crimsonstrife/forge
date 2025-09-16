<?php

namespace App\Filament\Resources\IssueTypes\Pages;

use App\Filament\Resources\IssueTypes\IssueTypeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewIssueType extends ViewRecord
{
    protected static string $resource = IssueTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
