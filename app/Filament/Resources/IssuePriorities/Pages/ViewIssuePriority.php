<?php

namespace App\Filament\Resources\IssuePriorities\Pages;

use App\Filament\Resources\IssuePriorities\IssuePriorityResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewIssuePriority extends ViewRecord
{
    protected static string $resource = IssuePriorityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
