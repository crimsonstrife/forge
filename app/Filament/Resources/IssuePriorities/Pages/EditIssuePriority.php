<?php

namespace App\Filament\Resources\IssuePriorities\Pages;

use App\Filament\Resources\IssuePriorities\IssuePriorityResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditIssuePriority extends EditRecord
{
    protected static string $resource = IssuePriorityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
