<?php

namespace App\Filament\Resources\IssueStatuses\Pages;

use App\Filament\Resources\IssueStatuses\IssueStatusResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewIssueStatus extends ViewRecord
{
    protected static string $resource = IssueStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
